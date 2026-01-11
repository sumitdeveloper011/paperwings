@if($users->hasPages())
    <div class="pagination-wrapper">
        {{ $users->links() }}
    </div>
@endif
