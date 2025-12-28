<?php

namespace App\Http\Controllers\Admin\ShippingPrice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShippingPrice\StoreShippingPriceRequest;
use App\Http\Requests\Admin\ShippingPrice\UpdateShippingPriceRequest;
use App\Models\ShippingPrice;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShippingPriceController extends Controller
{
    // Display a listing of the resource
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = ShippingPrice::with('region');

        if ($search) {
            $query->whereHas('region', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        $shippingPrices = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.shipping-price.index', compact('shippingPrices', 'search', 'status'));
    }

    // Show the form for creating a new resource
    public function create(Request $request): View
    {
        $regions = Region::active()->orderBy('name')->get();
        $selectedRegionId = $request->get('region_id');
        return view('admin.shipping-price.create', compact('regions', 'selectedRegionId'));
    }

    // Store a newly created resource in storage
    public function store(StoreShippingPriceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        $validated['uuid'] = Str::uuid();
        $validated['status'] = $request->has('status') ? 1 : 0;

        ShippingPrice::create($validated);

        return redirect()->route('admin.shipping-prices.index')
            ->with('success', 'Shipping price created successfully!');
    }

    // Display the specified resource
    public function show(ShippingPrice $shippingPrice): View
    {
        $shippingPrice->load('region');
        return view('admin.shipping-price.show', compact('shippingPrice'));
    }

    // Show the form for editing the specified resource
    public function edit(ShippingPrice $shippingPrice): View
    {
        $regions = Region::active()->orderBy('name')->get();
        return view('admin.shipping-price.edit', compact('shippingPrice', 'regions'));
    }

    // Update the specified resource in storage
    public function update(UpdateShippingPriceRequest $request, ShippingPrice $shippingPrice): RedirectResponse
    {
        $validated = $request->validated();
        
        $validated['status'] = $request->has('status') ? 1 : 0;

        $shippingPrice->update($validated);

        return redirect()->route('admin.shipping-prices.index')
            ->with('success', 'Shipping price updated successfully!');
    }

    // Remove the specified resource from storage
    public function destroy(ShippingPrice $shippingPrice): RedirectResponse
    {
        $shippingPrice->delete();

        return redirect()->route('admin.shipping-prices.index')
            ->with('success', 'Shipping price deleted successfully!');
    }

    // Update shipping price status
    public function updateStatus(Request $request, ShippingPrice $shippingPrice): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $shippingPrice->update(['status' => $request->status]);

        $statusText = $request->status == 1 ? 'activated' : 'deactivated';

        return redirect()->route('admin.shipping-prices.index')
            ->with('success', "Shipping price {$statusText} successfully!");
    }
}
