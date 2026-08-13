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
 * course_access_helper.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\service;

defined('MOODLE_INTERNAL') || die();

class course_access_helper {
    public static function ensure_course_enrolment(int $courseid, int $userid): void {
        global $DB, $CFG;
        require_once($CFG->libdir . '/enrollib.php');
        $context = \context_course::instance($courseid, IGNORE_MISSING);
        if (!$context || is_enrolled($context, $userid, '', true)) {
            return;
        }
        $manual = enrol_get_plugin('manual');
        if (!$manual) {
            return;
        }
        $manualinstance = null;
        foreach (enrol_get_instances($courseid, true) as $instance) {
            if ($instance->enrol === 'manual' && (int)$instance->status === ENROL_INSTANCE_ENABLED) {
                $manualinstance = $instance;
                break;
            }
        }
        if (!$manualinstance) {
            return;
        }
        $roleid = (int)get_config('local_kopere_trail', 'studentroleid');
        if ($roleid <= 0 || !$DB->record_exists('role', ['id' => $roleid])) {
            $roleid = (int)($manualinstance->roleid ?? 0);
        }
        if ($roleid <= 0) {
            return;
        }
        $manual->enrol_user($manualinstance, $userid, $roleid);
    }
}
