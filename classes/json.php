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
 * json.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail;

/**
 * Provides the json implementation.
 */
class json {
    /**
     * Handles decode.
     *
     * @param string|null $value The value.
     * @return array The result.
     */
    public static function decode(?string $value): array {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    /**
     * Handles encode.
     *
     * @param array $value The value.
     * @return string The result.
     */
    public static function encode(array $value): string {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Checks whether valid.
     *
     * @param string|null $value The value.
     * @return bool The result.
     */
    public static function is_valid(?string $value): bool {
        if ($value === null || trim($value) === '') {
            return true;
        }

        json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
