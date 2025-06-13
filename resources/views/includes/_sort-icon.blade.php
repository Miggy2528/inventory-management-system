@if ($sortField !== $field)
    <i class="fas fa-sort text-muted"></i>
@elseif ($sortAsc)
    <i class="fas fa-sort-up"></i>
@else
    <i class="fas fa-sort-down"></i>
@endif 