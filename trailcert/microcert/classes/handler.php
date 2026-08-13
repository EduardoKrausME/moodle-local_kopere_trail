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
 * @package   trailcert_microcert
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailcert_microcert;

/**
 * Provides the handler implementation.
 */
class handler implements \local_kopere_trail\contract\cert_provider {
    /**
     * Returns the display name.
     *
     * @return string The result.
     */
    public function get_name(): string {
        return get_string('pluginname', 'trailcert_microcert');
    }

    /**
     * Returns the certificate url.
     *
     * @param \stdClass $trail The trail.
     * @param int $userid The userid.
     * @return \moodle_url|null The result.
     */
    public function get_certificate_url(\stdClass $trail, int $userid): ?\moodle_url {
        global $DB;
        $progress = $DB->get_record('local_kopere_trail_prog', [
            'trailid' => (int)$trail->id,
            'userid' => $userid,
            'status' => 'completed',
        ], 'id, timecompleted', IGNORE_MISSING);
        if (!$progress) {
            return null;
        }
        return new \moodle_url('/local/kopere_trail/trailcert/microcert/view.php', [
            'trailid' => (int)$trail->id,
            'userid' => $userid,
        ]);
    }
}
