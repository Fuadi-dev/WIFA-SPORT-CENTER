<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromoCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = PromoCode::query()->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $promoCodes = $query->paginate(20);

        return view('admin.promo.code.index', compact('promoCodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1'
        ]);

        // Convert code to uppercase
        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        PromoCode::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil dibuat'
        ]);
    }

    public function edit($id)
    {
        $promoCode = PromoCode::findOrFail($id);

        return response()->json([
            'success' => true,
            'promoCode' => $promoCode
        ]);
    }

    public function update(Request $request, $id)
    {
        $promoCode = PromoCode::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code,' . $id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1'
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        $promoCode->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        $promoCode = PromoCode::findOrFail($id);

        // Check if promo code has been used
        if ($promoCode->bookings()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak dapat dihapus karena sudah digunakan'
            ], 400);
        }

        $promoCode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil dihapus'
        ]);
    }

    public function toggleStatus($id)
    {
        $promoCode = PromoCode::findOrFail($id);
        $promoCode->is_active = !$promoCode->is_active;
        $promoCode->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah',
            'is_active' => $promoCode->is_active
        ]);
    }
}
