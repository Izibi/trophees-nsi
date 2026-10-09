@if($contest->status == 'preparing')
Le dépôt de projets n'est pas encore ouvert.
@elseif($contest->status == 'open')
Le dépôt de projets est ouvert jusqu'au 23/03/2027 à 20h​ (heure de Paris) pour les élèves et jusqu'au 25/03/2027 à 20h (heure de Paris) pour les professeurs.
@elseif($contest->status == 'completion')
Le dépôt de projets est désormais terminé pour les élèves. Les projets doivent être vérifiés et validés par les professeurs avant le 25/03/2027 à 20h (heure de Paris).
@elseif($contest->status == 'instruction')
Le dépôt de projets est clôturé, les projets sont en cours d'instruction. En cas d'anomalie détectée sur un dossier, les professeurs référents seront notifiés par courriel !
@elseif($contest->status == 'grading-territorial')
Le dépôt de projets est clôturé, les projets sont évalués par le jury territorial. RDV le 11/05/2027 pour découvrir les projets sélectionnés pour la finale nationale.
@elseif($contest->status == 'deliberating-territorial')
Le dépôt de projets est clôturé, la délibération territoriale est en cours.
@elseif($contest->status == 'grading-national')
Le dépôt de projets est clôturé, les projets sont évalués par le jury national.
@elseif($contest->status == 'deliberating-national')
Le dépôt de projets est clôturé, la délibération nationale est en cours. RDV le 02/06/2027 pour découvrir les résultats nationaux.
@elseif($contest->status == 'closed')
Le dépôt de projets est clôturé.
@endif
