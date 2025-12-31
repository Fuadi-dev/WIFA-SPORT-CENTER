<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\SportPrice;
use Illuminate\Http\Request;

class SportPriceController extends Controller
{
    /**
     * Display the price management page
     */
    public function index()
    {
        $sports = Sport::with(['prices' => function ($query) {
            $query->orderBy('time_slot');
        }])->where('is_active', true)->get();
        
        $timeSlots = SportPrice::getTimeSlots();
        
        return view('admin.prices.index', compact('sports', 'timeSlots'));
    }

    /**
     * Store a new price or update existing
     */
    public function store(Request $request)
    {
        $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'time_slot' => 'required|in:morning,afternoon,evening',
            'weekday_price' => 'required|numeric|min:0',
            'weekend_price' => 'required|numeric|min:0',
        ]);

        $timeSlotConfig = SportPrice::getTimeSlots()[$request->time_slot];

        SportPrice::updateOrCreate(
            [
                'sport_id' => $request->sport_id,
                'time_slot' => $request->time_slot,
            ],
            [
                'start_time' => $timeSlotConfig['start'],
                'end_time' => $timeSlotConfig['end'],
                'weekday_price' => $request->weekday_price,
                'weekend_price' => $request->weekend_price,
                'is_active' => true,
            ]
        );

        return redirect()->route('admin.prices.index')
            ->with('success', 'Harga berhasil disimpan.');
    }

    /**
     * Update multiple prices at once
     */
    public function updateBulk(Request $request)
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*.sport_id' => 'required|exists:sports,id',
            'prices.*.time_slot' => 'required|in:morning,afternoon,evening',
            'prices.*.weekday_price' => 'required|numeric|min:0',
            'prices.*.weekend_price' => 'required|numeric|min:0',
        ]);

        foreach ($request->prices as $priceData) {
            $timeSlotConfig = SportPrice::getTimeSlots()[$priceData['time_slot']];
            
            SportPrice::updateOrCreate(
                [
                    'sport_id' => $priceData['sport_id'],
                    'time_slot' => $priceData['time_slot'],
                ],
                [
                    'start_time' => $timeSlotConfig['start'],
                    'end_time' => $timeSlotConfig['end'],
                    'weekday_price' => $priceData['weekday_price'],
                    'weekend_price' => $priceData['weekend_price'],
                    'is_active' => true,
                ]
            );
        }

        return redirect()->route('admin.prices.index')
            ->with('success', 'Semua harga berhasil diperbarui.');
    }

    /**
     * Toggle price active status
     */
    public function toggleStatus($id)
    {
        $price = SportPrice::findOrFail($id);
        $price->is_active = !$price->is_active;
        $price->save();

        return response()->json([
            'success' => true,
            'is_active' => $price->is_active,
            'message' => $price->is_active ? 'Harga diaktifkan' : 'Harga dinonaktifkan'
        ]);
    }

    /**
     * Delete a price
     */
    public function destroy($id)
    {
        $price = SportPrice::findOrFail($id);
        $price->delete();

        return redirect()->route('admin.prices.index')
            ->with('success', 'Harga berhasil dihapus.');
    }

    /**
     * Get prices for a specific sport (API)
     */
    public function getPricesBySport($sportId)
    {
        $prices = SportPrice::where('sport_id', $sportId)
            ->where('is_active', true)
            ->get();

        return response()->json($prices);
    }
}
