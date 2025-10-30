@if($contest->status == 'preparing')
Le dépôt de projets n'est pas encore ouvert.
@elseif($contest->status == 'open')
Le dépôt de projets est ouvert jusqu'au 27 mars 2026 à 20h (heure de Paris).
@elseif($contest->status == 'instruction')
Le dépôt de projets est clôturé, les projets sont en cours d'instruction.
@elseif($contest->status == 'grading-territorial')
Le dépôt de projets est clôturé, les projets sont évalués par le jury territorial.
@elseif($contest->status == 'deliberating-territorial')
Le dépôt de projets est clôturé, la délibération territoriale est en cours.
@elseif($contest->status == 'grading-national')
Le dépôt de projets est clôturé, les projets sont évalués par le jury national.
@elseif($contest->status == 'deliberating-national')
Le dépôt de projets est clôturé, la délibération nationale est en cours.
@elseif($contest->status == 'closed')
Le dépôt de projets est clôturé, les lauréats sont désormais sélectionnés (fin du concours).
@endif
