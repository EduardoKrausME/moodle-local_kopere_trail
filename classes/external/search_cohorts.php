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
 * search_cohorts.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * Provides the search cohorts implementation.
 */
class search_cohorts extends \external_api {
    /**
     * Defines external function parameters.
     *
     * @return \external_function_parameters The result.
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters(['query' => new \external_value(PARAM_RAW_TRIMMED, 'Search text')]);
    }

    /**
     * Executes the external function.
     *
     * @param string $query The query.
     * @return array The result.
     */
    public static function execute(string $query): array {
        global $DB;
        $params = self::validate_parameters(self::execute_parameters(), ['query' => $query]);
        $query = $params['query'];
        self::validate_context(\context_system::instance());
        $context = \context_system::instance();
        if (!has_any_capability(['local/kopere_trail:manage', 'local/kopere_trail:enrol'], $context)) {
            require_capability('local/kopere_trail:enrol', $context);
        }
        $query = trim($query);
        if ($query === '') {
            return [];
        }
        $like = '%' . $DB->sql_like_escape($query) . '%';
        $where = $DB->sql_like('name', ':name', false) . ' OR ' . $DB->sql_like('idnumber', ':idnumber', false);
        $records = $DB->get_records_select(
            'cohort',
            $where,
            ['name' => $like,
            'idnumber' => $like],
            'name ASC',
            'id, name, idnumber',
            0,
            30
        );
        $out = [];
        foreach ($records as $record) {
            $label = format_string($record->name);
            if (trim((string)$record->idnumber) !== '') {
                $label .= ' [' . s($record->idnumber) . ']';
            }
            $out[] = ['id' => (int)$record->id, 'label' => $label];
        }
        return $out;
    }

    /**
     * Defines the external function return structure.
     *
     * @return \external_multiple_structure The result.
     */
    public static function execute_returns(): \external_multiple_structure {
        return new \external_multiple_structure(new \external_single_structure([
            'id' => new \external_value(PARAM_INT, 'Cohort id'),
            'label' => new \external_value(PARAM_RAW, 'Display label'),
        ]));
    }
}
