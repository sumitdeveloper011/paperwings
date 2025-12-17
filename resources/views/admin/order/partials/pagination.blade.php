@if($orders->hasPages())
    <div class="pagination-wrapper">
        {{ $orders->links() }}
    </div>
@endif

