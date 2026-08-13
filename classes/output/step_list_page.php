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
 * step_list_page.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\output;

/**
 * Provides the step list page implementation.
 */
class step_list_page implements \renderable, \templatable {
    /**
     * Trail.
     *
     * @var \stdClass
     */
    private \stdClass $trail;
    /**
     * Steps.
     *
     * @var array
     */
    private array $steps;
    /**
     * Creates a new instance.
     *
     * @param \stdClass $trail The trail.
     * @param array $steps The steps.
     */
    public function __construct(\stdClass $trail, array $steps) {
        $this->trail = $trail;
        $this->steps = $steps;
    }
    /**
     * Exports data for a Mustache template.
     *
     * @param \renderer_base $output The output.
     * @return array The result.
     */
    public function export_for_template(\renderer_base $output): array {
        $items = [];
        $plugins = new \local_kopere_trail\service\subplugin_manager();
        foreach ($this->steps as $step) {
            $base = ['type' => 'step', 'id' => $step->id, 'sesskey' => sesskey()];
            $items[] = [
                'id' => (int)$step->id,
                'name' => format_string($step->name),
                'contenttype' => $plugins->get_plugin_label(
                    \local_kopere_trail\service\subplugin_manager::TYPE_CONTENT,
                    (string)$step->contenttype
                ),
                'completiontype' => $plugins->get_plugin_label(
                    \local_kopere_trail\service\subplugin_manager::TYPE_COMPLETION,
                    (string)$step->completiontype
                ),
                'optional' => !empty($step->optional),
                'visiblelabel' => !empty($step->visible) ? get_string('yes') : get_string('no'),
                'editurl' => (new \moodle_url(
                    '/local/kopere_trail/step_edit.php',
                    ['trailid' => $this->trail->id,
                    'id' => $step->id]
                ))->out(false),
                'moveupurl' => (new \moodle_url('/local/kopere_trail/move.php', $base + ['direction' => 'up']))->out(false),
                'movedownurl' => (new \moodle_url('/local/kopere_trail/move.php', $base + ['direction' => 'down']))->out(false),
            ];
        }
        return [
            'trailname' => format_string($this->trail->name),
            'steps' => $items,
            'hassteps' => !empty($items),
            'createurl' => (new \moodle_url('/local/kopere_trail/step_edit.php', ['trailid' => $this->trail->id]))->out(false),
            'edgesurl' => (new \moodle_url('/local/kopere_trail/edges.php', ['trailid' => $this->trail->id]))->out(false),
            'manageurl' => (new \moodle_url('/local/kopere_trail/manage.php'))->out(false),
        ];
    }
}
