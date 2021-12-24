@if($rows->hasPages())
    <div class="card mt-3 mb-3">
        <div class="card-body">
            {{ $rows->links() }}
        </div>
    </div>
@endif