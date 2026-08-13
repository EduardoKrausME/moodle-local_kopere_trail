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
 * @package   trailprereq_previous
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailprereq_previous;

/**
 * Provides the handler implementation.
 */
class handler implements \local_kopere_trail\contract\prereq_provider {
    /**
     * Returns the display name.
     *
     * @return string The result.
     */
    public function get_name(): string {
        return get_string('pluginname', 'trailprereq_previous');
    }

    /**
     * Checks whether available.
     *
     * @param \stdClass $edge The edge.
     * @param \stdClass $fromstep The fromstep.
     * @param \stdClass $tostep The tostep.
     * @param int $userid The userid.
     * @return bool The result.
     */
    public function is_available(\stdClass $edge, \stdClass $fromstep, \stdClass $tostep, int $userid): bool {
        return true;
    }

    /**
     * Returns the blocked reason.
     *
     * @param \stdClass $edge The edge.
     * @param \stdClass $fromstep The fromstep.
     * @param \stdClass $tostep The tostep.
     * @param int $userid The userid.
     * @return string The result.
     */
    public function get_blocked_reason(\stdClass $edge, \stdClass $fromstep, \stdClass $tostep, int $userid): string {
        return get_string('nextlocked', 'local_kopere_trail');
    }
}
