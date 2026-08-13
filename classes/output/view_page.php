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
 * view_page.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\output;

defined('MOODLE_INTERNAL') || die();

class view_page implements \renderable, \templatable {
    private \stdClass $trail;
    private \stdClass $progress;
    private array $steps;
    private bool $canmanage;
    private ?\moodle_url $certificateurl;

    public function __construct(\stdClass $trail, \stdClass $progress, array $steps, bool $canmanage, ?\moodle_url $certificateurl = null) {
        $this->trail = $trail;
        $this->progress = $progress;
        $this->steps = $steps;
        $this->canmanage = $canmanage;
        $this->certificateurl = $certificateurl;
    }

    public function export_for_template(\renderer_base $output): array {
        $percent = round((float)$this->progress->percent);
        $context = \context_system::instance();
        $trailconfig = \local_kopere_trail\json::decode($this->trail->config ?? null);
        $gamificationtype = (string)($trailconfig['gamificationtype'] ?? 'progress');
        $hasgamification = (new \local_kopere_trail\service\subplugin_manager())->get_gamification_handler($gamificationtype) !== null;
        $summary = file_rewrite_pluginfile_urls(
            (string)$this->trail->summary,
            'pluginfile.php',
            $context->id,
            'local_kopere_trail',
            'summary',
            (int)$this->trail->id
        );
        return [
            'id' => (int)$this->trail->id,
            'name' => format_string($this->trail->name),
            'summary' => format_text($summary, $this->trail->summaryformat, ['context' => $context]),
            'percent' => $percent,
            'status' => get_string($this->progress->status, 'local_kopere_trail'),
            'completedsteps' => (int)$this->progress->completedsteps,
            'totalsteps' => (int)$this->progress->totalsteps,
            'xp' => (int)$this->progress->xp,
            'hasgamification' => $hasgamification,
            'progressline' => get_string('progressline', 'local_kopere_trail', (object)[
                'completed' => (int)$this->progress->completedsteps,
                'total' => (int)$this->progress->totalsteps,
                'percent' => $percent,
            ]),
            'steps' => array_values($this->steps),
            'hassteps' => !empty($this->steps),
            'canmanage' => $this->canmanage,
            'certificateurl' => $this->certificateurl ? $this->certificateurl->out(false) : null,
            'hascertificate' => $this->certificateurl !== null,
            'stepsurl' => (new \moodle_url('/local/kopere_trail/steps.php', ['trailid' => $this->trail->id]))->out(false),
            'reporturl' => (new \moodle_url('/local/kopere_trail/report.php', ['trailid' => $this->trail->id]))->out(false),
            'indexurl' => (new \moodle_url('/local/kopere_trail/index.php'))->out(false),
        ];
    }
}
