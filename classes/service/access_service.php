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
 * access_service.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\service;

defined('MOODLE_INTERNAL') || die();

class access_service {
    private \local_kopere_trail\repository\trail_repository $trails;

    public function __construct(?\local_kopere_trail\repository\trail_repository $trails = null) {
        $this->trails = $trails ?? new \local_kopere_trail\repository\trail_repository();
    }

    public function is_open(\stdClass $trail, ?int $now = null): bool {
        $now = $now ?? time();
        if (empty($trail->visible)) {
            return false;
        }
        if (!empty($trail->startdate) && (int)$trail->startdate > $now) {
            return false;
        }
        if (!empty($trail->enddate) && (int)$trail->enddate < $now) {
            return false;
        }
        return true;
    }

    public function can_access(\stdClass $trail, int $userid, ?\context $context = null): bool {
        $context = $context ?? \context_system::instance();
        if (has_capability('local/kopere_trail:manage', $context, $userid)) {
            return true;
        }
        if (!has_capability('local/kopere_trail:view', $context, $userid)) {
            return false;
        }
        if (!$this->is_open($trail)) {
            return false;
        }
        return $this->trails->has_active_enrolment((int)$trail->id, $userid);
    }

    public function require_access(\stdClass $trail, int $userid, ?\context $context = null): void {
        $context = $context ?? \context_system::instance();
        if (!$this->can_access($trail, $userid, $context)) {
            throw new \required_capability_exception($context, 'local/kopere_trail:view', 'nopermissions', '');
        }
    }
}
