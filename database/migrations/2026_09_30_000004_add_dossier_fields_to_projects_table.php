<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the missing "dossier de candidature" fields (see dossier_candidature.csv).
     * All new columns are nullable : existing rows are left untouched, no backfill.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            // Composition de l'équipe
            $table->text('work_organisation')->nullable();
            // Présentation du projet
            $table->text('project_description')->nullable();
            $table->text('project_steps')->nullable();
            $table->text('project_outlook')->nullable();
            // Informations techniques
            $table->text('operationality_notes')->nullable();
            $table->string('ai_usage_percent')->nullable();
            $table->text('ai_usage_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'work_organisation',
                'project_description',
                'project_steps',
                'project_outlook',
                'operationality_notes',
                'ai_usage_percent',
                'ai_usage_notes',
            ]);
        });
    }
};
