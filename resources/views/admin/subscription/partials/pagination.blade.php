@if($subscriptions->hasPages())
    <div class="pagination-wrapper">
        {{ $subscriptions->appends(request()->except('page'))->links() }}
    </div>
@endif

