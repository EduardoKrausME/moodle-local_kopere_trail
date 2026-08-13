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
 * edge_list_page.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\output;

defined('MOODLE_INTERNAL') || die();

class edge_list_page implements \renderable, \templatable {
    private \stdClass $trail;
    private array $steps;
    private array $edges;
    public function __construct(\stdClass $trail, array $steps, array $edges) { $this->trail = $trail; $this->steps = $steps; $this->edges = $edges; }
    public function export_for_template(\renderer_base $output): array {
        $stepnames = [];
        foreach ($this->steps as $step) { $stepnames[$step->id] = format_string($step->name); }
        $items = [];
        $plugins = new \local_kopere_trail\service\subplugin_manager();
        foreach ($this->edges as $edge) {
            $base = ['type' => 'edge', 'id' => $edge->id, 'sesskey' => sesskey()];
            $items[] = [
                'fromstep' => $stepnames[$edge->fromstepid] ?? get_string('removedstep', 'local_kopere_trail'),
                'tostep' => $stepnames[$edge->tostepid] ?? get_string('removedstep', 'local_kopere_trail'),
                'ruleplugin' => $plugins->get_plugin_label(\local_kopere_trail\service\subplugin_manager::TYPE_PREREQ, (string)$edge->ruleplugin),
                'editurl' => (new \moodle_url('/local/kopere_trail/edge_edit.php', ['trailid' => $this->trail->id, 'id' => $edge->id]))->out(false),
                'moveupurl' => (new \moodle_url('/local/kopere_trail/move.php', $base + ['direction' => 'up']))->out(false),
                'movedownurl' => (new \moodle_url('/local/kopere_trail/move.php', $base + ['direction' => 'down']))->out(false),
            ];
        }
        return [
            'trailname' => format_string($this->trail->name),
            'edges' => $items,
            'hasedges' => !empty($items),
            'createurl' => (new \moodle_url('/local/kopere_trail/edge_edit.php', ['trailid' => $this->trail->id]))->out(false),
            'stepsurl' => (new \moodle_url('/local/kopere_trail/steps.php', ['trailid' => $this->trail->id]))->out(false),
            'manageurl' => (new \moodle_url('/local/kopere_trail/manage.php'))->out(false),
        ];
    }
}
