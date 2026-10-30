<?php

namespace App\Exports;

/**
 * The export catalog: every export offered by the new /export/{key} page,
 * with its columns exactly as specified in export_csv.csv.
 *
 * Role keys used for column availability:
 *   - admin:                 Administrateur
 *   - coordinator:           Coordinateur
 *   - president-territorial: Président territorial
 *   - president-prize:       Président national (jury national d'un prix)
 */
class ExportCatalog
{
    const ALL_ROLES = ['admin', 'coordinator', 'president-territorial', 'president-prize'];
    const ADMIN_ROLES = ['admin'];
    const ADMIN_COORDINATOR_ROLES = ['admin', 'coordinator'];
    // [Sélection nationale] columns: admin, coordinator and président national only
    const NATIONAL_ROLES = ['admin', 'coordinator', 'president-prize'];

    /**
     * @param string $key
     * @return ExportDefinition|null
     */
    public static function get($key)
    {
        $all = self::all();
        return isset($all[$key]) ? $all[$key] : null;
    }

    /**
     * @return ExportDefinition[]
     */
    public static function all()
    {
        return [
            'users' => self::users(),
            'projects' => self::projects(),
            'awards' => self::awards(),
        ];
    }

    /**
     * Export des utilisateurs: unchanged data of the legacy export.
     *
     * @return ExportDefinition
     */
    private static function users()
    {
        $roles = self::ADMIN_ROLES;
        $columns = [
            new ExportColumn('id', 'ID', 'general', $roles),
            new ExportColumn('created_at', 'Date de création', 'general', $roles),
            new ExportColumn('name', 'Nom', 'general', $roles),
            new ExportColumn('login', 'Login', 'general', $roles),
            new ExportColumn('email', 'Email', 'general', $roles),
            new ExportColumn('secondary_email', 'Email secondaire', 'general', $roles),
            new ExportColumn('role', 'Rôle', 'general', $roles),
            new ExportColumn('validated', 'Validé', 'general', $roles),
            new ExportColumn('region', 'Région', 'general', $roles),
            new ExportColumn('country', 'Pays', 'general', $roles),
            new ExportColumn('last_login_at', 'Dernière connexion', 'general', $roles),
            new ExportColumn('schools', 'Etablissements scolaires', 'general', $roles),
        ];

        return new ExportDefinition(
            'users',
            'Export des utilisateurs',
            'trophees_nsi_users.csv',
            self::ADMIN_ROLES,
            $columns
        );
    }

    /**
     * Export projets / coordination: one row per project.
     *
     * @return ExportDefinition
     */
    private static function projects()
    {
        $all = self::ALL_ROLES;
        $admin = self::ADMIN_ROLES;
        $adminCoordinator = self::ADMIN_COORDINATOR_ROLES;
        $national = self::NATIONAL_ROLES;

        $columns = [
            new ExportColumn('id', 'ID', 'general', $all),
            new ExportColumn('name', 'Nom du projet', 'general', $all),
            new ExportColumn('status', 'Statut du projet', 'general', $all),
            new ExportColumn('teacher_first_name', 'Prénom du professeur', 'general', $all),
            new ExportColumn('teacher_last_name', 'Nom du professeur', 'general', $all),
            new ExportColumn('teacher_email', 'Email du professeur', 'general', $adminCoordinator),
            new ExportColumn('school_name', 'Nom du lycée', 'general', $all),
            new ExportColumn('school_address', 'Adresse du lycée', 'general', $admin),
            new ExportColumn('school_zip', 'Code postal', 'general', $admin),
            new ExportColumn('school_city', 'Ville du lycée', 'general', $all),
            new ExportColumn('grade', 'Niveau scolaire de l\'équipe', 'general', $all),
            new ExportColumn('team_girls', 'Nombre de filles dans l\'équipe', 'general', $all),
            new ExportColumn('team_boys', 'Nombre de garçons dans l\'équipe', 'general', $all),
            new ExportColumn('team_not_provided', 'Nombre d\'élèves de genre non renseigné dans l\'équipe', 'general', $all),
            new ExportColumn('class_girls', 'Nombre total de filles dans la classe de NSI', 'general', $all),
            new ExportColumn('class_boys', 'Nombre total de garçons dans la classe de NSI', 'general', $all),
            new ExportColumn('class_not_provided', 'Nombre total d\'élèves de genre non renseigné dans la classe de NSI', 'general', $all),
            new ExportColumn('teacher_comment', 'Commentaire du professeur', 'general', $all),

            new ExportColumn('terr_ratings_amount', '[Sélection territoriale] Nombre de notes', 'territorial', $all),
            new ExportColumn('terr_score_technical', '[Sélection territoriale] Compétences Techniques', 'territorial', $all),
            new ExportColumn('terr_score_non_technical', '[Sélection territoriale] Compétences Non Techniques', 'territorial', $all),
            new ExportColumn('terr_score_total', '[Sélection territoriale] Note totale', 'territorial', $all),
            new ExportColumn('terr_prize', '[Sélection territoriale] Prix décerné', 'territorial', $all),
            new ExportColumn('terr_jury_comment', '[Sélection territoriale] Commentaire du jury (pour le jury national)', 'territorial', $all),
            new ExportColumn('terr_team_comment', '[Sélection territoriale] Commentaire du jury (pour l\'équipe)', 'territorial', $all),

            new ExportColumn('nat_ratings_amount', '[Sélection nationale] Nombre de notes', 'national', $national),
            new ExportColumn('nat_score_technical', '[Sélection nationale] Compétences Techniques', 'national', $national),
            new ExportColumn('nat_score_non_technical', '[Sélection nationale] Compétences Non Techniques', 'national', $national),
            new ExportColumn('nat_score_total', '[Sélection nationale] Note totale', 'national', $national),
            new ExportColumn('nat_prize', '[Sélection nationale] Prix décerné', 'national', $national),
            new ExportColumn('nat_team_comment', '[Sélection nationale] Commentaire du jury (pour l\'équipe)', 'national', $national),
        ];

        // Fixed team member slots 1..4 (config: nsi.team_size_max = 4).
        for ($i = 1; $i <= 4; $i++) {
            $columns[] = new ExportColumn('member_' . $i . '_first_name', 'Membre de l\'équipe ' . $i . ' : Prénom', 'team', $all);
            $columns[] = new ExportColumn('member_' . $i . '_last_name', 'Membre de l\'équipe ' . $i . ' : Nom', 'team', $all);
            $columns[] = new ExportColumn('member_' . $i . '_gender', 'Membre de l\'équipe ' . $i . ' : Genre', 'team', $all);
        }

        return new ExportDefinition(
            'projects',
            'Export des projets',
            'trophees_nsi_projets.csv',
            self::ALL_ROLES,
            $columns,
            true
        );
    }

    /**
     * Export lauréats: one row per award, sorted by award then project ID.
     * Used for the import into the new website.
     *
     * @return ExportDefinition
     */
    private static function awards()
    {
        $all = self::ALL_ROLES;

        $columns = [
            new ExportColumn('project_name', 'Nom du projet', 'general', $all),
            new ExportColumn('territory', '[Sélection territoriale] Territoire de rattachement', 'territorial', $all),
            new ExportColumn('school_name', 'Nom du lycée', 'general', $all),
            new ExportColumn('school_city', 'Ville du lycée', 'general', $all),
            new ExportColumn('teacher_name', 'Prénom et nom du professeur', 'general', $all),
            new ExportColumn('terr_prize', '[Sélection territoriale] Prix décerné', 'territorial', $all),
            new ExportColumn('nat_prize', '[Sélection nationale] Prix décerné', 'national', $all),
            new ExportColumn('description', 'Résumé du projet', 'general', $all),
            new ExportColumn('video', 'Lien de la vidéo', 'general', $all),
            new ExportColumn('image', 'Image du projet', 'general', $all),
        ];

        for ($i = 1; $i <= 4; $i++) {
            $columns[] = new ExportColumn('member_' . $i, 'Membre de l\'équipe ' . $i . ' : Prénom / NOM', 'team', $all);
        }

        return new ExportDefinition(
            'awards',
            'Export des lauréats',
            'trophees_nsi_laureats.csv',
            self::ALL_ROLES,
            $columns
        );
    }
}
