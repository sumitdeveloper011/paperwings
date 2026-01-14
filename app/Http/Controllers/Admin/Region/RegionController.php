<?php

namespace App\Http\Controllers\Admin\Region;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Region\StoreRegionRequest;
use App\Http\Requests\Admin\Region\UpdateRegionRequest;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegionController extends Controller
{
    // Display a listing of the resource
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Region::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        $regions = $query->orderBy('created_at', 'desc')->paginate(15);

        // Handle AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($regions->total() > 0 && $regions->hasPages()) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $regions->appends($request->query())
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.region.partials.table', compact('regions'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('admin.region.index', compact('regions', 'search', 'status'));
    }

    // Show the form for creating a new resource
    public function create(): View
    {
        return view('admin.region.create');
    }

    // Store a newly created resource in storage
    public function store(StoreRegionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['slug']);

        $validated['status'] = $request->has('status') ? 1 : 0;

        Region::create($validated);

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region created successfully!');
    }

    // Display the specified resource
    public function show(Region $region): View
    {
        $region->load('shippingPrice');
        return view('admin.region.show', compact('region'));
    }

    // Show the form for editing the specified resource
    public function edit(Region $region): View
    {
        return view('admin.region.edit', compact('region'));
    }

    // Update the specified resource in storage
    public function update(UpdateRegionRequest $request, Region $region): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['slug']);

        $validated['status'] = $request->has('status') ? 1 : 0;

        $region->update($validated);

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region updated successfully!');
    }

    // Remove the specified resource from storage (soft delete)
    public function destroy(Region $region): RedirectResponse
    {
        $region->delete();

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region deleted successfully!');
    }

    // Restore a soft-deleted region
    public function restore($id): RedirectResponse
    {
        $region = Region::withTrashed()->findOrFail($id);
        $region->restore();

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region restored successfully!');
    }

    // Permanently delete a region
    public function forceDelete($id): RedirectResponse
    {
        $region = Region::withTrashed()->findOrFail($id);
        $region->forceDelete();

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region permanently deleted!');
    }

    // Update region status
    public function updateStatus(Request $request, Region $region): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:0,1',
        ]);

        // Cast status to integer
        $status = (int) $validated['status'];
        $region->update(['status' => $status]);

        $statusText = $status == 1 ? 'activated' : 'deactivated';

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Region {$statusText} successfully!"
            ]);
        }

        return redirect()->route('admin.regions.index')
            ->with('success', "Region {$statusText} successfully!");
    }
}
