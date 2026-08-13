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
 * trail_viewed.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\event;

defined('MOODLE_INTERNAL') || die();

class trail_viewed extends \core\event\base {
    protected function init(): void {
        $this->data['objecttable'] = 'local_kopere_trail';
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    public static function get_name(): string {
        return get_string('event_trail_viewed', 'local_kopere_trail');
    }

    public function get_description(): string {
        return "The user with id '{$this->userid}' viewed the trail with id '{$this->objectid}'.";
    }

    public function get_url(): \moodle_url {
        return new \moodle_url('/local/kopere_trail/view.php', ['id' => $this->objectid]);
    }
}

