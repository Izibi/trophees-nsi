<?php

namespace App\Exports;

/**
 * Small pure formatting helpers shared by the export row builders.
 */
class ExportFormats
{
    /**
     * Splits a full name into [first name, last name]. The users table only
     * stores a single `name` column, so the first word is the first name and
     * the remainder is the last name.
     *
     * @param string|null $name
     * @return string[] [first name, last name]
     */
    public static function splitName($name)
    {
        $name = trim((string) $name);
        if ($name === '') {
            return ['', ''];
        }
        $position = mb_strpos($name, ' ');
        if ($position === false) {
            return [$name, ''];
        }
        return [mb_substr($name, 0, $position), mb_substr($name, $position + 1)];
    }

    /**
     * Gender label expected by the spec: fille / garçon / non renseigné.
     * The database stores: '', 'male', 'female', 'other'.
     *
     * @param string|null $gender
     * @return string
     */
    public static function genderLabel($gender)
    {
        if ($gender === 'male') {
            return 'garçon';
        }
        if ($gender === 'female') {
            return 'fille';
        }
        return 'non renseigné';
    }

    /**
     * Joins several values into one cell (" | " separator, empties dropped).
     *
     * @param array $values
     * @return string
     */
    public static function joinValues(array $values)
    {
        $kept = [];
        foreach ($values as $value) {
            if ($value !== null && $value !== '') {
                $kept[] = $value;
            }
        }
        return implode(' | ', $kept);
    }

    /**
     * Formats a team member for the Lauréats export: "Prénom NOM" (last name
     * uppercased, as per the spec's "Prénom / NOM" columns).
     *
     * @param object $member object with first_name / last_name properties
     * @return string
     */
    public static function memberFullName($member)
    {
        $firstName = trim((string) $member->first_name);
        $lastName = trim((string) $member->last_name);
        $fullName = trim($firstName . ' ' . mb_strtoupper($lastName));
        return $fullName;
    }
}
