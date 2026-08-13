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
 * search_activities.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

class search_activities extends \external_api {
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'query' => new \external_value(PARAM_RAW_TRIMMED, 'Search text'),
            'modnames' => new \external_multiple_structure(new \external_value(PARAM_PLUGIN, 'Module name'), 'Allowed module names', VALUE_DEFAULT, []),
        ]);
    }

    public static function execute(string $query, array $modnames = []): array {
        global $DB;
        $params = self::validate_parameters(self::execute_parameters(), [
            'query' => $query,
            'modnames' => $modnames,
        ]);
        $query = $params['query'];
        $modnames = $params['modnames'];
        self::validate_context(\context_system::instance());
        require_capability('local/kopere_trail:manage', \context_system::instance());
        $query = trim($query);
        if ($query === '') {
            return [];
        }
        $allmodules = $DB->get_records('modules', ['visible' => 1], 'name ASC', 'id, name');
        if ($modnames) {
            $allowed = array_flip($modnames);
            $allmodules = array_filter($allmodules, static fn($module) => isset($allowed[$module->name]));
        }
        $like = '%' . $DB->sql_like_escape($query) . '%';
        $results = [];
        foreach ($allmodules as $module) {
            if (count($results) >= 30) {
                break;
            }
            if (!$DB->get_manager()->table_exists($module->name)) {
                continue;
            }
            $columns = $DB->get_columns($module->name);
            if (!isset($columns['name'])) {
                continue;
            }
            $sql = "SELECT cm.id, c.fullname AS coursename, x.name AS activityname
                      FROM {course_modules} cm
                      JOIN {course} c ON c.id = cm.course
                      JOIN {{$module->name}} x ON x.id = cm.instance
                     WHERE cm.module = :moduleid
                       AND cm.deletioninprogress = 0
                       AND (" . $DB->sql_like('x.name', ':activityname', false) . " OR " . $DB->sql_like('c.fullname', ':coursename', false) . ")
                  ORDER BY c.fullname ASC, x.name ASC";
            $remaining = 30 - count($results);
            $records = $DB->get_records_sql($sql, [
                'moduleid' => (int)$module->id,
                'activityname' => $like,
                'coursename' => $like,
            ], 0, $remaining);
            foreach ($records as $record) {
                $results[] = [
                    'id' => (int)$record->id,
                    'label' => format_string($record->coursename) . ' / ' . format_string($record->activityname) . ' (' . $module->name . ')',
                ];
            }
        }
        usort($results, static fn($a, $b) => strnatcasecmp($a['label'], $b['label']));
        return array_slice($results, 0, 30);
    }

    public static function execute_returns(): \external_multiple_structure {
        return new \external_multiple_structure(new \external_single_structure([
            'id' => new \external_value(PARAM_INT, 'Course module id'),
            'label' => new \external_value(PARAM_RAW, 'Display label'),
        ]));
    }
}
