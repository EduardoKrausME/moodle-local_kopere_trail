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
 * manage_page.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\output;

/**
 * Provides the manage page implementation.
 */
class manage_page implements \renderable, \templatable {
    /**
     * Trails.
     *
     * @var array
     */
    private array $trails;
    /**
     * Creates a new instance.
     *
     * @param array $trails The trails.
     */
    public function __construct(array $trails) {
        $this->trails = $trails;
    }
    /**
     * Exports data for a Mustache template.
     *
     * @param \renderer_base $output The output.
     * @return array The result.
     */
    public function export_for_template(\renderer_base $output): array {
        $items = [];
        foreach ($this->trails as $trail) {
            $base = ['type' => 'trail', 'id' => $trail->id, 'sesskey' => sesskey()];
            $items[] = [
                'id' => (int)$trail->id,
                'name' => format_string($trail->name),
                'code' => s($trail->code ?? ''),
                'visiblelabel' => !empty($trail->visible) ? get_string('yes') : get_string('no'),
                'viewurl' => (new \moodle_url('/local/kopere_trail/view.php', ['id' => $trail->id]))->out(false),
                'editurl' => (new \moodle_url('/local/kopere_trail/edit.php', ['id' => $trail->id]))->out(false),
                'stepsurl' => (new \moodle_url('/local/kopere_trail/steps.php', ['trailid' => $trail->id]))->out(false),
                'enrolurl' => (new \moodle_url('/local/kopere_trail/enrol.php', ['trailid' => $trail->id]))->out(false),
                'reporturl' => (new \moodle_url('/local/kopere_trail/report.php', ['trailid' => $trail->id]))->out(false),
                'moveupurl' => (new \moodle_url('/local/kopere_trail/move.php', $base + ['direction' => 'up']))->out(false),
                'movedownurl' => (new \moodle_url('/local/kopere_trail/move.php', $base + ['direction' => 'down']))->out(false),
            ];
        }
        return [
            'trails' => $items,
            'hastrails' => !empty($items),
            'createurl' => (new \moodle_url('/local/kopere_trail/edit.php'))->out(false),
        ];
    }
}
