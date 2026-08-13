<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * configuration_fields.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\form;

/**
 * Provides the configuration fields implementation.
 */
class configuration_fields {
    /**
     * Returns the course options.
     *
     * @param int $selectedid The selectedid.
     * @return array The result.
     */
    public static function get_course_options(int $selectedid = 0): array {
        global $DB;
        if ($selectedid <= 0) {
            return [];
        }
        $course = $DB->get_record('course', ['id' => $selectedid], 'id, fullname, shortname', IGNORE_MISSING);
        if (!$course) {
            return [];
        }
        $label = format_string($course->fullname);
        if (trim((string)$course->shortname) !== '') {
            $label .= ' [' . format_string($course->shortname) . ']';
        }
        return [(int)$course->id => $label];
    }

    /**
     * Returns the activity options.
     *
     * @param int $selectedid The selectedid.
     * @return array The result.
     */
    public static function get_activity_options(int $selectedid = 0): array {
        if ($selectedid <= 0) {
            return [];
        }
        $cm = get_coursemodule_from_id('', $selectedid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return [];
        }
        $modinfo = get_fast_modinfo((int)$cm->course);
        $cminfo = $modinfo->get_cm($selectedid);
        return [$selectedid => format_string($modinfo->get_course()->fullname) . ' / ' . format_string($cminfo->name)];
    }

    /**
     * Returns the cohort options.
     *
     * @param array $selectedids The selectedids.
     * @return array The result.
     */
    public static function get_cohort_options(array $selectedids = []): array {
        global $DB;
        $selectedids = array_values(array_unique(array_filter(array_map('intval', $selectedids))));
        if (!$selectedids) {
            return [];
        }
        $cohorts = $DB->get_records_list('cohort', 'id', $selectedids, 'name ASC', 'id, name, idnumber');
        $options = [];
        foreach ($cohorts as $cohort) {
            $label = format_string($cohort->name);
            if (trim((string)$cohort->idnumber) !== '') {
                $label .= ' [' . format_string($cohort->idnumber) . ']';
            }
            $options[(int)$cohort->id] = $label;
        }
        return $options;
    }

    /**
     * Returns the grade item options.
     *
     * @param int $selectedid The selectedid.
     * @return array The result.
     */
    public static function get_grade_item_options(int $selectedid = 0): array {
        global $DB;
        if ($selectedid <= 0) {
            return [];
        }
        $sql = "SELECT gi.id, gi.itemname, gi.itemtype, gi.itemmodule, gi.iteminstance, c.fullname AS coursename
                  FROM {grade_items} gi
                  JOIN {course} c ON c.id = gi.courseid
                 WHERE gi.id = :id";
        $item = $DB->get_record_sql($sql, ['id' => $selectedid], IGNORE_MISSING);
        if (!$item) {
            return [];
        }
        return [(int)$item->id => self::grade_item_label($item)];
    }

    /**
     * Returns the competency options.
     *
     * @param array $selectedids The selectedids.
     * @return array The result.
     */
    public static function get_competency_options(array $selectedids = []): array {
        global $DB;
        $selectedids = array_values(array_unique(array_filter(array_map('intval', $selectedids))));
        if (!$selectedids || !$DB->get_manager()->table_exists('competency')) {
            return [];
        }
        $records = $DB->get_records_list('competency', 'id', $selectedids, 'shortname ASC', 'id, shortname, idnumber');
        $options = [];
        foreach ($records as $record) {
            $label = format_string($record->shortname);
            if (trim((string)$record->idnumber) !== '') {
                $label .= ' [' . s($record->idnumber) . ']';
            }
            $options[(int)$record->id] = $label;
        }
        return $options;
    }

    /**
     * Handles grade item label.
     *
     * @param \stdClass $item The item.
     * @return string The result.
     */
    public static function grade_item_label(\stdClass $item): string {
        $itemname = trim((string)$item->itemname);
        if ($itemname === '') {
            $itemname = get_string('unnamedgradeitem', 'local_kopere_trail');
        }
        return format_string($item->coursename) . ' / ' . format_string($itemname);
    }
}
