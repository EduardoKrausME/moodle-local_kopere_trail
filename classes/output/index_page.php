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
 * index_page.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\output;

/**
 * Provides the index page implementation.
 */
class index_page implements \renderable, \templatable {
    /**
     * Trails.
     *
     * @var array
     */
    private array $trails;
    /**
     * Canmanage.
     *
     * @var bool
     */
    private bool $canmanage;

    /**
     * Creates a new instance.
     *
     * @param array $trails The trails.
     * @param bool $canmanage The canmanage.
     */
    public function __construct(array $trails, bool $canmanage) {
        $this->trails = $trails;
        $this->canmanage = $canmanage;
    }

    /**
     * Exports data for a Mustache template.
     *
     * @param \renderer_base $output The output.
     * @return array The result.
     */
    public function export_for_template(\renderer_base $output): array {
        $items = [];
        $context = \context_system::instance();
        foreach ($this->trails as $trail) {
            $percent = (float)($trail->percent ?? 0);
            $summary = file_rewrite_pluginfile_urls(
                (string)$trail->summary,
                'pluginfile.php',
                $context->id,
                'local_kopere_trail',
                'summary',
                (int)$trail->id
            );
            $items[] = [
                'id' => (int)$trail->id,
                'name' => format_string($trail->name),
                'summary' => format_text($summary, $trail->summaryformat, ['context' => $context]),
                'url' => (new \moodle_url('/local/kopere_trail/view.php', ['id' => $trail->id]))->out(false),
                'percent' => $percent,
                'percentrounded' => round($percent),
                'status' => get_string($trail->progressstatus ?? 'notstarted', 'local_kopere_trail'),
                'completedsteps' => (int)($trail->completedsteps ?? 0),
                'totalsteps' => (int)($trail->totalsteps ?? 0),
                'progressline' => get_string('progressline', 'local_kopere_trail', (object)[
                    'completed' => (int)($trail->completedsteps ?? 0),
                    'total' => (int)($trail->totalsteps ?? 0),
                    'percent' => round($percent),
                ]),
            ];
        }

        return [
            'trails' => $items,
            'hastrails' => !empty($items),
            'canmanage' => $this->canmanage,
            'manageurl' => (new \moodle_url('/local/kopere_trail/manage.php'))->out(false),
        ];
    }
}
