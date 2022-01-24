<h3>
@if($contest->status == 'preparing' || $contest->status == 'open')
Grading is not open yet.
@elseif($contest->status == 'closed')
Grading is closed.
@endif
</h3>