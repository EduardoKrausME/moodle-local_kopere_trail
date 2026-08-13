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
 * handler.php
 *
 * @package   trailcompletion_manual
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailcompletion_manual;

/**
 * Provides the handler implementation.
 */
class handler implements \local_kopere_trail\contract\completion_provider {
    /**
     * Returns the display name.
     *
     * @return string The result.
     */
    public function get_name(): string {
        return get_string('pluginname', 'trailcompletion_manual');
    }

    /**
     * Checks whether complete manually.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @return bool The result.
     */
    public function can_complete_manually(\stdClass $step, int $userid): bool {
        return true;
    }

    /**
     * Returns the completion.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @return array The result.
     */
    public function get_completion(\stdClass $step, int $userid): array {
        global $DB;

        $progress = $DB->get_record('local_kopere_trail_progstep', [
            'stepid' => $step->id,
            'userid' => $userid,
        ], '*', IGNORE_MISSING);

        $completed = $progress && (int)$progress->completionstate === 1;
        return [
            'completed' => $completed,
            'progresspercent' => $completed ? 100 : 0,
            'source' => 'manual',
            'details' => [],
        ];
    }
}
