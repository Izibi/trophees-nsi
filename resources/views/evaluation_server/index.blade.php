@extends('layout')

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Serveur d'évaluation</h2>
        </div>
        <div>
            <p>
                Ce serveur d'évaluation vous permet d'exécuter les projets dans un environnement virtuel.
                Il est proposé à titre expérimental, et vos retours seront utiles pour améliorer le service pour l'édition suivante.
                En tant que ressource partagée, nous vous demandons d'accepter les règles suivantes :
            </p>
        </div>
        <div>
            <p>Je m'engage à respecter les règles suivantes lors de l'utilisation de cet accès distant :</p>
            <ul>
                <li>Ne pas utiliser cet accès pour autre chose que le test des projets Trophées NSI.</li>
                <li>Ne pas l'utiliser pour lire les pdf et vidéos de présentation des projets. À faire sur sa propre machine.</li>
                <li>Ne pas l'utiliser pour lire les codes sources des projets sauf lorsque c'est nécessaire pour les faire fonctionner.</li>
                <li>Ne rien installer d'autre que les modules nécessaires à l'exécution de ces projets.</li>
                <li>Ne pas utiliser de navigateur web sur la machine sauf lorsque c'est nécessaire pour exécuter un projet.</li>
                <li>Ne pas transmettre l'accès à qui que ce soit.</li>
            </ul>
        </div>
        <div>
            <input type="checkbox" id="agreement" name="agreement">
            <label for="agreement">Lu et approuvé, {{ $user->name }}</label>
        </div>
        <div>
            <a class="btn btn-primary" id="link" target="_blank" href="{{ $url }}">Accéder au serveur d'évaluation</a><br>
            <i>{{ $nb }} utilisateurs actuellement connectés</i>
        </div>
        <hr>
        <div>
            <p><b>Informations pratiques supplémentaires (dernière mise à jour le 29 mars) :</b></p>
            <ul>
                <li>Session :<ul>
                    <li>Votre session est individuelle, les fichiers hors du dossier TropheesNSI sont donc privés à votre compte.</li>
                    <li>En cas d'inactivité, votre session sera déconnectée et fermée après quelques minutes.</li>
                    <li>Pour vous déconnecter, vous pouvez simplement fermer la page de votre navigateur.</li>
                </ul></li>
                </li>
                <li>Dossiers partagés :<ul>
                    <li>Les dossiers auxquels vous avez accès par territoire (et par prix) sont partagés en lecture et écriture par tous les membres du jury de ce territoire. Ainsi toute modification apportée à un projet afin de le faire fonctionner plus facilement profitera à tous les membres du jury.</li>
                    <li>En cas de problème avec les fichiers d'un projet, n'hésitez pas à retélécharger la version originale depuis la forge.</li>
                </ul></li>
                <li>Modules Python : <ul>
                    <li>Les versions courantes des modules les plus utilisés ont été installées sur le serveur, mais certains projets nécessitent peut-être des versions spécifiques.</li>
                    <li>Vous pouvez installer vous-mêmes des versions différentes avec pip comme vous le feriez normalement.</li>
                    <li>En cas de problème lors de l'installation des requirements via requirements.txt, essayez de vérifier le contenu du fichier, et de supprimer les modules incorrects, car pip n'installera aucun module si jamais au moins module n'est pas installable.</li>
                    <li>Il peut être nécessaire de modifier la version demandée des dépendances pour les faire fonctionner sur la version de Python du serveur.</li>
                    <li>Certains projets déclarent des modules de base dans requirements.txt, ce qui est incorrect et empêche l'installation via pip.</li>
                </ul></li>
            </ul>
    </div>

    <script type="text/javascript">
        var agreement = $('#agreement');
        var link = $('#link');

        function updateLink() {
            if (agreement.is(':checked')) {
                link.removeClass('disabled');
                link.removeAttr('aria-disabled');
            } else {
                link.addClass('disabled');
                link.attr('aria-disabled', 'true');
            }
        }

        agreement.change(updateLink);
        updateLink();
    </script>
@endsection
