<h3>
@if($contest->status == 'preparing')
Le dépôt de projets n'est pas encore ouvert.
@elseif($contest->status == 'open')
La période de dépôt de projets est en cours.
@elseif($contest->status == 'grading')
Le dépôt de projets est clôturé, les projets sont évalués par le jury national.
@elseif($contest->status == 'deliberating')
Le dépôt de projets est clôturé, le jury national délibère.
@elseif($contest->status == 'closed')
Le dépôt de projets est clôturé, le jury a terminé la délibération.
@endif
</h3>