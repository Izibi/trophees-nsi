<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\ActiveContest;
use App\Exports\AwardsExportBuilder;
use App\Exports\CsvStreamer;
use App\Exports\ExportCatalog;
use App\Exports\ExportDefinition;
use App\Exports\ExportRoles;
use App\Exports\PhasePolicy;
use App\Exports\ProjectsExportBuilder;
use App\Exports\UsersExportBuilder;

class ExportController extends Controller
{
    /** @var \App\Models\Contest|null */
    private $contest;

    public function __construct(ActiveContest $active_contest)
    {
        $this->contest = $active_contest->get();
    }

    /**
     * Serves the export selection page: the field filter (and the laureate
     * filter for the projects export) for the requested export key.
     *
     * @param Request $request
     * @param string  $export export key: users, projects, awards
     * @return \Illuminate\HttpResponse
     */
    public function show(Request $request, $export)
    {
        $definition = ExportCatalog::get($export);
        if (!$definition) {
            abort(404);
        }

        $roles = ExportRoles::for($request->user());
        if (!$definition->canAccess($roles)) {
            return $definition->key == 'users' ? redirect('/') : redirect('/projects');
        }

        $groups = [];
        foreach ($definition->columnsFor($roles) as $column) {
            // Territorial and national fields share one fieldset on the page;
            // $column->group keeps its real value (used for phase blanking and
            // for expanding group keys in the fields argument).
            $displayGroup = in_array($column->group, ['territorial', 'national'], true)
                ? 'selection'
                : $column->group;
            if (!isset($groups[$displayGroup])) {
                $groups[$displayGroup] = [];
            }
            $groups[$displayGroup][] = $column;
        }

        return view('exports.show', [
            'exportKey' => $definition->key,
            'title' => $definition->title,
            'groups' => $groups,
            // The laureate filter belongs to the admin export only.
            'withLaureateFilter' => $definition->withLaureateFilter && in_array('admin', $roles, true),
            'downloadUrl' => '/export/' . $definition->key . '/download',
        ]);
    }

    /**
     * Streams the requested CSV with only the selected fields.
     *
     * @param Request $request
     * @param string  $export
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Request $request, $export)
    {
        $definition = ExportCatalog::get($export);
        if (!$definition) {
            abort(404);
        }

        $roles = ExportRoles::for($request->user());
        if (!$definition->canAccess($roles)) {
            return $definition->key == 'users' ? redirect('/') : redirect('/projects');
        }

        if (in_array($definition->key, ['projects', 'awards'], true) && !$this->contest) {
            return redirect('/')->with('error', 'Aucun concours actif : impossible de générer cet export.');
        }

        $allowed = $definition->columnsFor($roles);

        // Selection is sent as "fields=key,key,..." (comma-separated list of
        // field keys); "all=1" — or no argument at all — means every allowed
        // column. The raw fields[] array from the form is kept as a no-JS
        // fallback. Keep only requested fields the current user may select, in
        // registry order.
        $selectedKeys = [];
        $requested = $request->input('fields');
        if (is_string($requested)) {
            $requested = array_filter(array_map('trim', explode(',', $requested)), 'strlen');
        }
        if (is_array($requested)) {
            // A group name (e.g. "team") expands to every column of that group.
            $groupColumns = [];
            foreach ($definition->columns as $column) {
                $groupColumns[$column->group][] = $column->key;
            }
            $expanded = [];
            foreach ($requested as $key) {
                if (isset($groupColumns[$key])) {
                    $expanded = array_merge($expanded, $groupColumns[$key]);
                } else {
                    $expanded[] = $key;
                }
            }
            $requested = $expanded;

            foreach ($allowed as $column) {
                if (in_array($column->key, $requested, true)) {
                    $selectedKeys[] = $column->key;
                }
            }
        }
        // No (valid) selection: default to every allowed column.
        if (!count($selectedKeys)) {
            foreach ($allowed as $column) {
                $selectedKeys[] = $column->key;
            }
        }

        $header = [];
        $groupByKey = [];
        foreach ($allowed as $column) {
            if (in_array($column->key, $selectedKeys, true)) {
                $header[] = $column->label;
                $groupByKey[$column->key] = $column->group;
            }
        }

        $status = $this->contest ? $this->contest->status : null;
        $rows = $this->applyPhaseBlanking(
            $this->sourceRows($definition, $request),
            $selectedKeys,
            $groupByKey,
            PhasePolicy::blankedGroups($status)
        );

        return CsvStreamer::stream($definition->filename, $header, $rows);
    }

    /**
     * Row generator of the requested export.
     *
     * @param ExportDefinition $definition
     * @param Request          $request
     * @return \Generator
     */
    private function sourceRows(ExportDefinition $definition, Request $request)
    {
        if ($definition->key == 'users') {
            return (new UsersExportBuilder())->rows();
        }
        if ($definition->key == 'projects') {
            $builder = new ProjectsExportBuilder($this->contest, $request->user());
            $laureate = in_array('admin', ExportRoles::for($request->user()), true)
                ? $request->get('laureate')
                : null;
            return $builder->rows($laureate);
        }
        $builder = new AwardsExportBuilder($this->contest, $request->user());
        return $builder->rows();
    }

    /**
     * Keeps only the selected columns (registry order) and blanks the groups
     * that hold no data yet during the current contest phase.
     *
     * @param \Generator|array $source rows keyed by column key
     * @param string[]         $keys selected column keys, in registry order
     * @param string[]         $groupByKey column key => group key
     * @param string[]         $blankedGroups group keys to blank
     * @return \Generator
     */
    private function applyPhaseBlanking($source, array $keys, array $groupByKey, array $blankedGroups)
    {
        foreach ($source as $row) {
            $line = [];
            foreach ($keys as $key) {
                if (in_array($groupByKey[$key], $blankedGroups, true)) {
                    $line[] = '';
                } else {
                    $line[] = isset($row[$key]) ? $row[$key] : '';
                }
            }
            yield $line;
        }
    }
}
