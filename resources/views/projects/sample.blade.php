@extends('layout')

@section('content')
    <div class="alert alert-info" role="alert" style="background-color: #d1ecf1; border-color: #bee5eb; border-left: 5px solid #17a2b8;">
        <h4 class="alert-heading"><i class="fas fa-info-circle"></i> Exemple de projet complété</h4>
        <p class="mb-0">Cette page présente, à titre indicatif, un exemple de projet complété.</p>
    </div>

    <div class="form-group">
        <label for="inp-name">Nom du projet</label>
        <input type="text" class="form-control" id="inp-name" value="Projet démo 2026" readonly>
    </div>

    <div class="form-group">
        <label for="inp-school_id">Établissement</label>
        <select class="form-control" id="inp-school_id" disabled>
            <option>Lycée général et technologique Bréquigny, 35205 Rennes, Rennes, France</option>
        </select>
        <small class="form-text text-muted">Sélectionnez l'établissement scolaire de vos élèves parmi la liste proposée. Le territoire de rattachement sera automatiquement attribué.<br>Vérifiez bien l'adresse complète avant de valider !</small>
    </div>

    <div class="mt-5 mb-5">
        <h5>Membres de l'équipe</h5>
        <p><small class="form-text text-muted">Précisez la composition de l'équipe pour ce projet. Vérifiez bien l'orthographe des prénoms et des noms. Ces données seront réutilisées pour l'édition des attestations de participation et les diplômes ! Les autorisations signées sont obligatoirement à joindre au dossier avant de soumettre le projet.</small></p>
        <div class="row mt-5 mb-3">
            <div class="col-1"></div>
            <div class="col-2">Prénom</div>
            <div class="col-2">Nom</div>
            <div class="col-2">Genre</div>
            <div class="col-5">
                Autorisations signées<br>
                <small>Ajoutez l'autorisation signée pour chaque élève. Le document à compléter est <a href="https://trophees-nsi.fr/ressources" target="_blank">disponible ici</a>.</small>
            </div>
        </div>
        <div>
            <div class="row mb-3">
                <div class="col-1"></div>
                <div class="col-2">
                    <input type="text" class="form-control" value="Prénom1" readonly>
                </div>
                <div class="col-2">
                    <input type="text" class="form-control" value="NOM1" readonly>
                </div>
                <div class="col-2">
                    <select class="form-control" disabled>
                        <option>Masculin</option>
                    </select>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control" value="autorisation_prenom1_nom1.pdf" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-1"></div>
                <div class="col-2">
                    <input type="text" class="form-control" value="Prénom2" readonly>
                </div>
                <div class="col-2">
                    <input type="text" class="form-control" value="NOM2" readonly>
                </div>
                <div class="col-2">
                    <select class="form-control" disabled>
                        <option>Féminin</option>
                    </select>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control" value="autorisation_prenom2_nom2.pdf" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-1"></div>
                <div class="col-2">
                    <input type="text" class="form-control" value="Prénom3" readonly>
                </div>
                <div class="col-2">
                    <input type="text" class="form-control" value="NOM3" readonly>
                </div>
                <div class="col-2">
                    <select class="form-control" disabled>
                        <option>Féminin</option>
                    </select>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control" value="autorisation_prenom3_nom3.pdf" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-1"></div>
                <div class="col-2">
                    <input type="text" class="form-control" value="Prénom4" readonly>
                </div>
                <div class="col-2">
                    <input type="text" class="form-control" value="NOM4" readonly>
                </div>
                <div class="col-2">
                    <select class="form-control" disabled>
                        <option>Masculin</option>
                    </select>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control" value="autorisation_prenom4_nom4.pdf" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="inp-grade_id">Niveau scolaire</label>
        <select class="form-control" id="inp-grade_id" disabled>
            <option>Terminale</option>
        </select>
        <small class="form-text text-muted">Tous les membres de l'équipe doivent être dans la même classe au moment du dépôt du dossier.</small>
    </div>

    <div class="mt-5">
        <h5>Répartition de la classe</h5>
        <p><small class="form-text text-muted">Précisez la répartition totale des élèves dans votre classe de NSI pour le niveau renseigné ci-dessus.</small></p>

        <div class="row">
            <div class="col-4">
                <div class="form-group mb-0">
                    <label for="inp-class_girls">Nombre de filles</label>
                    <input type="text" class="form-control" id="inp-class_girls" value="4" readonly>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group mb-0">
                    <label for="inp-class_boys">Nombre de garçons</label>
                    <input type="text" class="form-control" id="inp-class_boys" value="11" readonly>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group mb-0">
                    <label for="inp-class_not_provided">Non renseigné</label>
                    <input type="text" class="form-control" id="inp-class_not_provided" value="0" readonly>
                </div>
            </div>
        </div>
    </div>

    <p>&nbsp;</p>

    <div class="form-group">
        <label for="inp-description">Résumé du projet</label>
        <textarea class="form-control" id="inp-description" style="height: 200px" readonly>Le résumé (500 caractères maximum) est rédigé directement par les élèves pour présenter le projet informatique proposé pour concourir aux Trophées NSI. 

Ce texte sera notamment exploité pour la promotion du projet sur le site internet et/ou les comptes sociaux officiels du concours, pour toutes les communications officielles associées aux Trophées NSI, et plus largement pour valoriser les projets réalisés par les élèves inscrits en spécialité « Numérique et Sciences informatiques » (NSI).</textarea>
        <small class="form-text text-muted">Ce résumé doit être écrit par les membres de l'équipe. Ce texte sera utilisé pour les communications officielles du projet (site internet, réseaux sociaux). <div class="text-right text-muted">494/500</div></small>
    </div>

    <div class="form-group">
        <label for="inp-video">Vidéo</label>
        <input type="text" class="form-control" id="inp-video" value="https://tube-sciences-technologies.apps.education.fr/search?search=tropheesnsi&searchTarget=local" placeholder="https://" readonly>
        <small class="form-text text-muted">La vidéo est à publier sur <a href="https://tube-sciences-technologies.apps.education.fr/" target="_blank">l'instance Peertube Tube Sciences & Technologies</a>. Renseignez ici son URL.</small>
    </div>

    <div class="form-group">
        <label for="inp-url">Dossier technique</label>
        <input type="text" class="form-control" id="inp-url" value="https://forge.apps.education.fr/trophees-nsi-2026/modele" placeholder="https://" readonly>
        <small class="form-text text-muted">Les livrables à fournir sont précisés dans le règlement du concours. Le dossier technique est à déposer sur <a href="https://docs.forge.apps.education.fr/#qui-peut-sinscrire-et-participer-a-la-forge-des-communs-numeriques-educatifs" target="_blank">la forge des communs numériques éducatifs</a>, selon <a href="https://forge.apps.education.fr/trophees-nsi-2026/modele" target="_blank">la nomenclature suivante</a>. Renseignez ici l'URL du projet (choisissez le niveau de visibilité "publique" dans les paramètres du projet).</small>
    </div>

    <div class="form-group">
        <label for="inp-code_notes">Nature du code et usage de l'IA</label>
        <textarea class="form-control" id="inp-code_notes" style="height: 200px" readonly>Le concours s'engage dans une démarche de lutte contre le plagiat. Les élèves doivent impérativement détailler la nature du code et détailler, avec transparence et clarté, l'usage éventuel de l'Intelligence Artificielle. L'utilisation de l'IA doit être limitée, réfléchie et justifiée !

Pour cette section, les élèves devront apporter des informations détaillées sur les points suivants : 
- Est-ce que le projet est une création originale ? Si non, l'exploitation de codes existants est clairement énoncée, les autrices ou les auteurs sont identifiés et mentionnés. 
- Avez-vous eu recours à l'Intelligence Artificielle ? Si oui, quelles ont été les modalités d'utilisation de l'IA dans ce projet  (par exemple : fonctions utilisées, proportion de l'IA dans le projet global).
- Et toute information utile à porter à la connaissance des membres du jury.

Cette section est rédigée par les élèves. Elle est obligatoire pour soumettre le projet.</textarea>
        <small class="form-text text-muted">Le concours s'engage dans une démarche de lutte contre le plagiat. Les élèves doivent impérativement détailler la nature du code et préciser, avec transparence et clarté, l'usage éventuel de l'Intelligence Artificielle. L'utilisation de l'IA doit être limitée, réfléchie et justifiée !<br>Pour cette section, les élèves devront apporter des informations détaillées sur les points suivants :<br>- Est-ce que le projet est une création originale ? Si non, l'exploitation de codes existants est clairement énoncée, les autrices ou les auteurs sont identifiés et mentionnés.<br>- Avez-vous eu recours à l'Intelligence Artificielle ? Si oui, quelles ont été les modalités d'utilisation de l'IA dans ce projet  (par exemple : fonctions utilisées, proportion de l'IA dans le projet global).<br>- Et toute information utile à porter à la connaissance des membres du jury.<br>Cette section est rédigée par les élèves. Elle est obligatoire pour soumettre le projet.</small>
    </div>

    <div class="row">
        <div class="col-6 file-box mb-4">
            <span class="file-box-title mb-2">Image</span>
            <input type="text" class="form-control" value="image_projet.png" readonly>
            <small class="form-text text-muted">Veuillez fournir une image carrée, de taille 500px × 500 px.</small>
        </div>
    </div>

    <div class="form-group">
        <label for="inp-teacher_notes">Informations complémentaires (commentaire de l'enseignant sur le projet et l'équipe)</label>
        <textarea class="form-control" id="inp-teacher_notes" style="height: 200px" readonly>Le commentaire est réservé aux membres du jury. Il n'est pas communiqué aux élèves. 
Il permet de partager des précisions utiles à porter à la connaissance des membres du jury :

- contexte de réalisation du projet, 
- motivation et implication des élèves, 
- progression des élèves durant l'année scolaire,
- degré d'autonomie des élèves.

Cette section est rédigée par le professeur de NSI. Il est obligatoire pour soumettre le projet.</textarea>
        <small class="form-text text-muted">Merci de bien vouloir apporter des précisions utiles à porter à la connaissance des membres du jury (contexte de réalisation du projet, motivation et implication des élèves, progression des élèves durant l'année scolaire).</small>
    </div>

    <div class="mt-5">
        <i>Taille maximum des fichiers : 20Mo</i>
    </div>

    <div class="mt-5">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="inp-cb_tested_by_teacher" checked disabled>
            <label class="form-check-label" for="inp-cb_tested_by_teacher">Je certifie avoir testé moi-même le projet, et confirme que celui-ci fonctionne comme présenté dans la vidéo.</label>
        </div>
    </div>
    <div class="mt-2">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="inp-cb_video_authorization" checked disabled>
            <label class="form-check-label" for="inp-cb_video_authorization">Je certifie que tous les élèves de ce projet ont une autorisation signée pour l'utilisation de l'image ou de la voix et de leurs oeuvres.</label>
        </div>
    </div>
    <div class="mt-2">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="inp-cb_reglament_accepted" checked disabled>
            <label class="form-check-label" for="inp-cb_reglament_accepted">Je certifie avoir lu et accepté le <a href="https://trophees-nsi.fr/reglement" target="_blank">règlement du concours</a>.</label>
        </div>
    </div>
@endsection
