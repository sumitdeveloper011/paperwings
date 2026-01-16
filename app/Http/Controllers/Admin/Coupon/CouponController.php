<?php

namespace App\Http\Controllers\Admin\Coupon;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Coupon\StoreCouponRequest;
use App\Http\Requests\Admin\Coupon\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CouponController extends Controller
{
    // Display a listing of the resource
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Coupon::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(15);

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            // Only show pagination if there are coupons and multiple pages
            if ($coupons->total() > 0 && $coupons->hasPages()) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $coupons
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.coupon.partials.table', compact('coupons'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('admin.coupon.index', compact('coupons', 'search', 'status'));
    }

    // Show the form for creating a new resource
    public function create(): View
    {
        return view('admin.coupon.create');
    }

    // Store a newly created resource in storage
    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // UUID is auto-generated in model boot method, but we can set it explicitly
        $validated['uuid'] = Str::uuid();

        // Code is already uppercase from prepareForValidation, but ensure it
        $validated['code'] = strtoupper(trim($validated['code']));

        // Status is already validated as integer 0 or 1
        $validated['status'] = (int) $validated['status'];

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully!');
    }

    // Display the specified resource
    public function show(Coupon $coupon): View
    {
        return view('admin.coupon.show', compact('coupon'));
    }

    // Show the form for editing the specified resource
    public function edit(Coupon $coupon): View
    {
        return view('admin.coupon.edit', compact('coupon'));
    }

    // Update the specified resource in storage
    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $validated = $request->validated();

        // Code is already uppercase from prepareForValidation, but ensure it
        $validated['code'] = strtoupper(trim($validated['code']));

        // Status is already validated as integer 0 or 1
        $validated['status'] = (int) $validated['status'];

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully!');
    }

    // Remove the specified resource from storage
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully!');
    }

    // Update coupon status
    public function updateStatus(Request $request, Coupon $coupon): RedirectResponse|JsonResponse
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $coupon->update(['status' => $request->status]);

        $statusLabel = $request->status == 1 ? 'Active' : 'Inactive';

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Coupon set to {$statusLabel}"
            ]);
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', "Coupon set to {$statusLabel}");
    }
}
