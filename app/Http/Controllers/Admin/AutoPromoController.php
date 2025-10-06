<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoPromo;
use Illuminate\Http\Request;

class AutoPromoController extends Controller
{
    public function index(Request $request)
    {
        $query = AutoPromo::query()->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by schedule type
        if ($request->filled('schedule_type')) {
            $query->where('schedule_type', $request->schedule_type);
        }

        $autoPromos = $query->paginate(20);

        return view('admin.promo.auto.index', compact('autoPromos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'schedule_type' => 'required|in:specific_date,day_of_week,daily',
            'specific_date' => 'required_if:schedule_type,specific_date|nullable|date',
            'days_of_week' => 'required_if:schedule_type,day_of_week|nullable|array',
            'days_of_week.*' => 'integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'valid_from' => 'required_unless:schedule_type,specific_date|nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'min_transaction' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0'
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // Check for conflicting promos
        $conflict = $this->checkPromoConflict($validated);
        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Promo bentrok! Sudah ada promo lain dengan jadwal dan waktu yang sama atau overlap. Silakan pilih jadwal atau waktu yang berbeda.'
            ], 422);
        }

        AutoPromo::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Promo otomatis berhasil dibuat'
        ]);
    }

    public function edit($id)
    {
        $autoPromo = AutoPromo::findOrFail($id);

        // Format dates for HTML date input (YYYY-MM-DD)
        $autoPromo->valid_from = $autoPromo->valid_from ? $autoPromo->valid_from->format('Y-m-d') : null;
        $autoPromo->valid_until = $autoPromo->valid_until ? $autoPromo->valid_until->format('Y-m-d') : null;
        $autoPromo->specific_date = $autoPromo->specific_date ? $autoPromo->specific_date->format('Y-m-d') : null;

        return response()->json([
            'success' => true,
            'autoPromo' => $autoPromo
        ]);
    }

    public function update(Request $request, $id)
    {
        $autoPromo = AutoPromo::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'schedule_type' => 'required|in:specific_date,day_of_week,daily',
            'specific_date' => 'required_if:schedule_type,specific_date|nullable|date',
            'days_of_week' => 'required_if:schedule_type,day_of_week|nullable|array',
            'days_of_week.*' => 'integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'valid_from' => 'required_unless:schedule_type,specific_date|nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'min_transaction' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0'
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // Check for conflicting promos (exclude current promo)
        $conflict = $this->checkPromoConflict($validated, $id);
        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Promo bentrok! Sudah ada promo lain dengan jadwal dan waktu yang sama atau overlap. Silakan pilih jadwal atau waktu yang berbeda.'
            ], 422);
        }

        $autoPromo->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Promo otomatis berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        $autoPromo = AutoPromo::findOrFail($id);
        $autoPromo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promo otomatis berhasil dihapus'
        ]);
    }

    public function toggleStatus($id)
    {
        $autoPromo = AutoPromo::findOrFail($id);
        $autoPromo->is_active = !$autoPromo->is_active;
        $autoPromo->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah',
            'is_active' => $autoPromo->is_active
        ]);
    }

    /**
     * Check if there's a conflicting promo with the same schedule and time
     */
    private function checkPromoConflict(array $data, $excludeId = null): bool
    {
        // Get all active promos
        $existingPromos = AutoPromo::when($excludeId, function($q) use ($excludeId) {
            return $q->where('id', '!=', $excludeId);
        })->get();

        foreach ($existingPromos as $existing) {
            // Check if time ranges overlap
            if (!$this->timeRangesOverlap(
                $data['start_time'], 
                $data['end_time'],
                $existing->start_time,
                $existing->end_time
            )) {
                continue; // No time overlap, skip this promo
            }

            // Check if dates/schedules conflict
            if ($this->schedulesConflict($data, $existing)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if two time ranges overlap
     */
    private function timeRangesOverlap($start1, $end1, $start2, $end2): bool
    {
        // Convert to comparable format if needed
        $start1 = is_string($start1) ? $start1 : $start1->format('H:i:s');
        $end1 = is_string($end1) ? $end1 : $end1->format('H:i:s');
        $start2 = is_string($start2) ? $start2 : $start2->format('H:i:s');
        $end2 = is_string($end2) ? $end2 : $end2->format('H:i:s');

        // Two time ranges overlap if:
        // start1 < end2 AND start2 < end1
        // This handles all overlap cases including:
        // - Complete overlap: 08:00-16:00 vs 08:00-16:00 (same exact time)
        // - Partial overlap: 08:00-12:00 vs 10:00-14:00 (middle overlap)
        // - Contained: 08:00-16:00 vs 10:00-12:00 (one inside another)
        // - Start/End touch: 08:00-12:00 vs 12:00-16:00 (end meets start - NOT overlap)
        
        return $start1 < $end2 && $start2 < $end1;
    }

    /**
     * Check if schedules conflict between new and existing promo
     */
    private function schedulesConflict(array $newData, $existing): bool
    {
        $newType = $newData['schedule_type'];
        $existingType = $existing->schedule_type;

        // Case 1: Both are specific_date
        if ($newType === 'specific_date' && $existingType === 'specific_date') {
            return $newData['specific_date'] === $existing->specific_date;
        }

        // Case 2: New is specific_date, existing is day_of_week
        if ($newType === 'specific_date' && $existingType === 'day_of_week') {
            $newDate = \Carbon\Carbon::parse($newData['specific_date']);
            $dayOfWeek = $newDate->dayOfWeek; // 0=Sunday, 6=Saturday
            
            // Check if the day is in existing promo's days
            if ($existing->days_of_week && in_array($dayOfWeek, $existing->days_of_week)) {
                // Check if date is within existing promo's validity period
                return $this->dateInRange($newDate, $existing->valid_from, $existing->valid_until);
            }
            return false;
        }

        // Case 3: New is specific_date, existing is daily
        if ($newType === 'specific_date' && $existingType === 'daily') {
            $newDate = \Carbon\Carbon::parse($newData['specific_date']);
            return $this->dateInRange($newDate, $existing->valid_from, $existing->valid_until);
        }

        // Case 4: New is day_of_week, existing is specific_date
        if ($newType === 'day_of_week' && $existingType === 'specific_date') {
            $existingDate = \Carbon\Carbon::parse($existing->specific_date);
            $dayOfWeek = $existingDate->dayOfWeek;
            
            if (isset($newData['days_of_week']) && in_array($dayOfWeek, $newData['days_of_week'])) {
                return $this->dateInRange($existingDate, $newData['valid_from'] ?? null, $newData['valid_until'] ?? null);
            }
            return false;
        }

        // Case 5: Both are day_of_week
        if ($newType === 'day_of_week' && $existingType === 'day_of_week') {
            // Check if any days overlap
            if (isset($newData['days_of_week']) && $existing->days_of_week) {
                $daysOverlap = !empty(array_intersect($newData['days_of_week'], $existing->days_of_week));
                if ($daysOverlap) {
                    // Check if date ranges overlap
                    return $this->dateRangesOverlap(
                        $newData['valid_from'] ?? null,
                        $newData['valid_until'] ?? null,
                        $existing->valid_from,
                        $existing->valid_until
                    );
                }
            }
            return false;
        }

        // Case 6: New is day_of_week, existing is daily
        if ($newType === 'day_of_week' && $existingType === 'daily') {
            return $this->dateRangesOverlap(
                $newData['valid_from'] ?? null,
                $newData['valid_until'] ?? null,
                $existing->valid_from,
                $existing->valid_until
            );
        }

        // Case 7: New is daily, existing is specific_date
        if ($newType === 'daily' && $existingType === 'specific_date') {
            $existingDate = \Carbon\Carbon::parse($existing->specific_date);
            return $this->dateInRange($existingDate, $newData['valid_from'] ?? null, $newData['valid_until'] ?? null);
        }

        // Case 8: New is daily, existing is day_of_week
        if ($newType === 'daily' && $existingType === 'day_of_week') {
            return $this->dateRangesOverlap(
                $newData['valid_from'] ?? null,
                $newData['valid_until'] ?? null,
                $existing->valid_from,
                $existing->valid_until
            );
        }

        // Case 9: Both are daily
        if ($newType === 'daily' && $existingType === 'daily') {
            return $this->dateRangesOverlap(
                $newData['valid_from'] ?? null,
                $newData['valid_until'] ?? null,
                $existing->valid_from,
                $existing->valid_until
            );
        }

        return false;
    }

    /**
     * Check if a date is within a date range
     */
    private function dateInRange($date, $rangeStart, $rangeEnd): bool
    {
        $date = \Carbon\Carbon::parse($date);
        $start = $rangeStart ? \Carbon\Carbon::parse($rangeStart) : null;
        $end = $rangeEnd ? \Carbon\Carbon::parse($rangeEnd) : null;

        if ($start && $date->lt($start)) {
            return false;
        }

        if ($end && $date->gt($end)) {
            return false;
        }

        return true;
    }

    /**
     * Check if two date ranges overlap
     */
    private function dateRangesOverlap($start1, $end1, $start2, $end2): bool
    {
        $start1 = $start1 ? \Carbon\Carbon::parse($start1) : null;
        $end1 = $end1 ? \Carbon\Carbon::parse($end1) : null;
        $start2 = $start2 ? \Carbon\Carbon::parse($start2) : null;
        $end2 = $end2 ? \Carbon\Carbon::parse($end2) : null;

        // If either range has no start, they potentially overlap infinitely
        if (!$start1 || !$start2) {
            return true;
        }

        // Check overlap: start1 <= end2 (or no end2) AND start2 <= end1 (or no end1)
        $condition1 = !$end2 || $start1->lte($end2);
        $condition2 = !$end1 || $start2->lte($end1);

        return $condition1 && $condition2;
    }
}
