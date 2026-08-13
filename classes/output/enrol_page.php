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
 * enrol_page.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\output;

/**
 * Provides the enrol page implementation.
 */
class enrol_page implements \renderable, \templatable {
    /**
     * Trail.
     *
     * @var \stdClass
     */
    private \stdClass $trail;
    /**
     * Assignments.
     *
     * @var array
     */
    private array $assignments;
    /**
     * Creates a new instance.
     *
     * @param \stdClass $trail The trail.
     * @param array $assignments The assignments.
     */
    public function __construct(\stdClass $trail, array $assignments) {
        $this->trail = $trail;
        $this->assignments = $assignments;
    }
    /**
     * Exports data for a Mustache template.
     *
     * @param \renderer_base $output The output.
     * @return array The result.
     */
    public function export_for_template(\renderer_base $output): array {
        global $DB;
        $userids = $cohortids = [];
        foreach ($this->assignments as $assignment) {
            if ($assignment->assigntype === 'user') {
                $userids[] = (int)$assignment->instanceid;
            } else if ($assignment->assigntype === 'cohort') {
                $cohortids[] = (int)$assignment->instanceid;
            }
        }
        $users = $userids ? $DB->get_records_list(
            'user',
            'id',
            array_values(array_unique($userids)),
            '',
            'id, firstname, lastname, firstnamephonetic, lastnamephonetic, middlename, alternatename, email'
        ) : [];
        $cohorts = $cohortids ? $DB->get_records_list(
            'cohort',
            'id',
            array_values(array_unique($cohortids)),
            '',
            'id, name, idnumber'
        ) : [];
        $items = [];
        foreach ($this->assignments as $assignment) {
            if ($assignment->assigntype === 'user') {
                $targetname = get_string('removeduser', 'local_kopere_trail');
                if (isset($users[$assignment->instanceid])) {
                    $user = $users[$assignment->instanceid];
                    $targetname = fullname($user) . (trim((string)$user->email) !== '' ? ' (' . s($user->email) . ')' : '');
                }
            } else {
                $targetname = get_string('removedcohort', 'local_kopere_trail');
                if (isset($cohorts[$assignment->instanceid])) {
                    $cohort = $cohorts[$assignment->instanceid];
                    $targetname = format_string($cohort->name);
                    if (trim((string)$cohort->idnumber) !== '') {
                        $targetname .= ' [' . format_string($cohort->idnumber) . ']';
                    }
                }
            }
            $items[] = [
                'type' => get_string('assignmenttype_' . $assignment->assigntype, 'local_kopere_trail'),
                'targetname' => $targetname,
                'status' => get_string($assignment->status, 'local_kopere_trail'),
            ];
        }
        return [
            'trailname' => format_string($this->trail->name),
            'assignments' => $items,
            'hasassignments' => !empty($items),
            'manageurl' => (new \moodle_url('/local/kopere_trail/manage.php'))->out(false),
        ];
    }
}
