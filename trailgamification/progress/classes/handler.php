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
 * @package   trailgamification_progress
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailgamification_progress;

defined('MOODLE_INTERNAL') || die();

class handler implements \local_kopere_trail\contract\gamification_provider {
    public function get_name(): string {
        return get_string('pluginname', 'trailgamification_progress');
    }

    public function calculate_xp(array $steps, array $stepstates, int $userid): int {
        $xp = 0;
        foreach ($steps as $step) {
            if (!empty($stepstates[$step->id]['completed'])) {
                $xp += (int)$step->points;
            }
        }

        return $xp;
    }
}

