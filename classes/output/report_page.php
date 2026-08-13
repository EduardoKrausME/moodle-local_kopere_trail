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
 * report_page.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\output;

/**
 * Provides the report page implementation.
 */
class report_page implements \renderable, \templatable {
    /**
     * Trail.
     *
     * @var \stdClass
     */
    private \stdClass $trail;
    /**
     * Rows.
     *
     * @var array
     */
    private array $rows;

    /**
     * Creates a new instance.
     *
     * @param \stdClass $trail The trail.
     * @param array $rows The rows.
     */
    public function __construct(\stdClass $trail, array $rows) {
        $this->trail = $trail;
        $this->rows = $rows;
    }

    /**
     * Exports data for a Mustache template.
     *
     * @param \renderer_base $output The output.
     * @return array The result.
     */
    public function export_for_template(\renderer_base $output): array {
        $items = [];
        foreach ($this->rows as $row) {
            $userurl = new \moodle_url('/user/profile.php', ['id' => $row->userid]);
            $items[] = [
                'student' => fullname($row),
                'email' => s($row->email),
                'userurl' => $userurl->out(false),
                'completedsteps' => (int)($row->completedsteps ?? 0),
                'totalsteps' => (int)($row->totalsteps ?? 0),
                'percent' => round((float)($row->percent ?? 0)),
                'status' => get_string($row->status ?? 'notstarted', 'local_kopere_trail'),
                'xp' => (int)($row->xp ?? 0),
                'lastupdate' => !empty($row->timemodified) ? userdate($row->timemodified) : '-',
            ];
        }

        return [
            'trailname' => format_string($this->trail->name),
            'rows' => $items,
            'hasrows' => !empty($items),
            'manageurl' => (new \moodle_url('/local/kopere_trail/manage.php'))->out(false),
        ];
    }
}
