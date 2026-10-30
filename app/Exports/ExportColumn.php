<?php

namespace App\Exports;

/**
 * Describes one exportable column: its machine key, its CSV header label,
 * the group it belongs to (general / territorial / national / team) and the
 * user roles that are allowed to select it (see export_csv.csv).
 */
class ExportColumn
{
    /** @var string */
    public $key;

    /** @var string */
    public $label;

    /** @var string */
    public $group;

    /** @var string[] */
    public $roles;

    /**
     * @param string   $key    machine key used in the `fields[]` request parameter
     * @param string   $label  CSV header label, exactly as in the spec table
     * @param string   $group  one of: general, territorial, national, team
     * @param string[] $roles  roles allowed to select this column
     */
    public function __construct($key, $label, $group, array $roles)
    {
        $this->key = $key;
        $this->label = $label;
        $this->group = $group;
        $this->roles = $roles;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function allows($role)
    {
        return in_array($role, $this->roles, true);
    }
}
