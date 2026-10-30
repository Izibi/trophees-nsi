<?php

namespace App\Exports;

/**
 * Description of one CSV export: metadata, who may access it, and which
 * columns it offers (each column knows which roles may select it).
 */
class ExportDefinition
{
    /** @var string */
    public $key;

    /** @var string */
    public $title;

    /** @var string */
    public $filename;

    /** @var string[] roles allowed to use this export at all */
    public $accessRoles;

    /** @var ExportColumn[] all columns of this export, in spec order */
    public $columns;

    /** @var bool whether the page shows the laureate filter (projects export) */
    public $withLaureateFilter;

    /**
     * @param string         $key
     * @param string         $title
     * @param string         $filename
     * @param string[]       $accessRoles
     * @param ExportColumn[] $columns
     * @param bool           $withLaureateFilter
     */
    public function __construct(
        $key,
        $title,
        $filename,
        array $accessRoles,
        array $columns,
        $withLaureateFilter = false
    ) {
        $this->key = $key;
        $this->title = $title;
        $this->filename = $filename;
        $this->accessRoles = $accessRoles;
        $this->columns = $columns;
        $this->withLaureateFilter = $withLaureateFilter;
    }

    /**
     * Whether users holding any of the given roles may use this export.
     * Mirrors the access checks of the legacy export links.
     *
     * @param string[] $userRoles
     * @return bool
     */
    public function canAccess(array $userRoles)
    {
        foreach ($this->accessRoles as $role) {
            if (in_array($role, $userRoles, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Columns selectable by the given roles, in spec order.
     *
     * @param string[] $userRoles
     * @return ExportColumn[]
     */
    public function columnsFor(array $userRoles)
    {
        $columns = [];
        foreach ($this->columns as $column) {
            foreach ($userRoles as $role) {
                if ($column->allows($role)) {
                    $columns[] = $column;
                    break;
                }
            }
        }
        return $columns;
    }

    /**
     * Distinct group keys present in the given roles' columns, in spec order.
     *
     * @param string[] $userRoles
     * @return string[]
     */
    public function groupsFor(array $userRoles)
    {
        $groups = [];
        foreach ($this->columnsFor($userRoles) as $column) {
            if (!in_array($column->group, $groups, true)) {
                $groups[] = $column->group;
            }
        }
        return $groups;
    }
}
