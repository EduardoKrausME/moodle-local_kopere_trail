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
 * search_grade_items.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

class search_grade_items extends \external_api {
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters(['query' => new \external_value(PARAM_RAW_TRIMMED, 'Search text')]);
    }

    public static function execute(string $query): array {
        global $DB, $SITE;
        $params = self::validate_parameters(self::execute_parameters(), ['query' => $query]);
        $query = $params['query'];
        self::validate_context(\context_system::instance());
        require_capability('local/kopere_trail:manage', \context_system::instance());
        $query = trim($query);
        if ($query === '') {
            return [];
        }
        $like = '%' . $DB->sql_like_escape($query) . '%';
        $sql = "SELECT gi.id, gi.itemname, gi.itemtype, gi.itemmodule, gi.iteminstance, c.fullname AS coursename
                  FROM {grade_items} gi
                  JOIN {course} c ON c.id = gi.courseid
                 WHERE c.id <> :siteid
                   AND gi.itemtype <> 'course'
                   AND (" . $DB->sql_like('gi.itemname', ':itemname', false) . " OR " . $DB->sql_like('c.fullname', ':coursename', false) . ")
              ORDER BY c.fullname ASC, gi.sortorder ASC";
        $records = $DB->get_records_sql($sql, ['siteid' => $SITE->id, 'itemname' => $like, 'coursename' => $like], 0, 30);
        $out = [];
        foreach ($records as $record) {
            $out[] = ['id' => (int)$record->id, 'label' => \local_kopere_trail\form\configuration_fields::grade_item_label($record)];
        }
        return $out;
    }

    public static function execute_returns(): \external_multiple_structure {
        return new \external_multiple_structure(new \external_single_structure([
            'id' => new \external_value(PARAM_INT, 'Grade item id'),
            'label' => new \external_value(PARAM_RAW, 'Display label'),
        ]));
    }
}
