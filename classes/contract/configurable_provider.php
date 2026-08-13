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
 * configurable_provider.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\contract;

/**
 * Defines the configurable provider contract.
 */
interface configurable_provider {
    /**
     * Handles add configuration fields.
     *
     * @param \MoodleQuickForm $mform The mform.
     * @param string $selectorfield The selectorfield.
     * @param \stdClass|null $currentdata The currentdata.
     * @return void The result.
     */
    public function add_configuration_fields(\MoodleQuickForm $mform, string $selectorfield, ?\stdClass $currentdata = null): void;

    /**
     * Prepares the configuration.
     *
     * @param \stdClass $data The data.
     * @param array $config The config.
     * @return \stdClass The result.
     */
    public function prepare_configuration(\stdClass $data, array $config): \stdClass;

    /**
     * Builds the configuration.
     *
     * @param \stdClass $data The data.
     * @return array The result.
     */
    public function build_configuration(\stdClass $data): array;

    /**
     * Validates the configuration.
     *
     * @param array $data The data.
     * @return array The result.
     */
    public function validate_configuration(array $data): array;
}
