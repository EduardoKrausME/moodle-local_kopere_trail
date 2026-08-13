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
 * content_provider.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\contract;

/**
 * Defines the content provider contract.
 */
interface content_provider {
    /**
     * Returns the display name.
     *
     * @return string The result.
     */
    public function get_name(): string;

    /**
     * Returns the icon.
     *
     * @return string The result.
     */
    public function get_icon(): string;

    /**
     * Returns the launch url.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @return \moodle_url|null The result.
     */
    public function get_launch_url(\stdClass $step, int $userid): ?\moodle_url;

    /**
     * Handles ensure access.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @return void The result.
     */
    public function ensure_access(\stdClass $step, int $userid): void;

    /**
     * Exports the view data.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @return array The result.
     */
    public function export_view_data(\stdClass $step, int $userid): array;
}
