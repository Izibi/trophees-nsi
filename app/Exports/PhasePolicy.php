<?php

namespace App\Exports;

/**
 * Phase-based blanking of export fields.
 *
 * Access to exports is *never* phase-dependent (it follows the current link
 * access rules); only the values of whole column groups are blanked:
 *
 *  - before the territorial phases (preparing, open, completion, instruction)
 *    both [Sélection territoriale] and [Sélection nationale] fields are blank;
 *  - during the territorial phases, [Sélection nationale] fields are blank;
 *  - during the national phases and once closed, nothing is blanked.
 */
class PhasePolicy
{
    const GROUP_TERRITORIAL = 'territorial';
    const GROUP_NATIONAL = 'national';

    /** Phases in which territorial results exist. */
    const TERRITORIAL_PHASES = ['grading-territorial', 'deliberating-territorial'];

    /** Phases in which both territorial and national results exist. */
    const NATIONAL_PHASES = ['grading-national', 'deliberating-national', 'closed'];

    /**
     * Column groups whose values must be blanked for the given contest status.
     *
     * @param string|null $status contest status
     * @return string[] blanked group keys
     */
    public static function blankedGroups($status)
    {
        if ($status !== null && in_array($status, self::TERRITORIAL_PHASES, true)) {
            return [self::GROUP_NATIONAL];
        }
        if ($status !== null && in_array($status, self::NATIONAL_PHASES, true)) {
            return [];
        }
        // preparing, open, completion, instruction, or no active contest:
        // no selection results exist yet, blank both groups.
        return [self::GROUP_TERRITORIAL, self::GROUP_NATIONAL];
    }
}
