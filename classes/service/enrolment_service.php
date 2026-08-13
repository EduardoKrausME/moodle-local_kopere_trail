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
 * enrolment_service.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\service;

defined('MOODLE_INTERNAL') || die();

class enrolment_service {
    private \local_kopere_trail\repository\trail_repository $trails;
    private subplugin_manager $plugins;

    public function __construct(
        ?\local_kopere_trail\repository\trail_repository $trails = null,
        ?subplugin_manager $plugins = null
    ) {
        $this->trails = $trails ?? new \local_kopere_trail\repository\trail_repository();
        $this->plugins = $plugins ?? new subplugin_manager();
    }

    public function sync_assignments(): int {
        $count = 0;
        foreach ($this->trails->get_trailids_requiring_sync() as $trailid) {
            $count += $this->sync_trail_assignments($trailid);
        }
        return $count;
    }

    public function sync_trail_assignments(int $trailid): int {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $desired = [];
        foreach ($this->trails->get_active_assignments($trailid) as $assignment) {
            $userids = [];
            if ($assignment->assigntype === 'user') {
                if ($DB->record_exists('user', ['id' => (int)$assignment->instanceid, 'deleted' => 0])) {
                    $userids[] = (int)$assignment->instanceid;
                }
            } else if ($assignment->assigntype === 'cohort') {
                $members = $DB->get_records('cohort_members', ['cohortid' => (int)$assignment->instanceid], '', 'userid');
                foreach ($members as $member) {
                    $userids[] = (int)$member->userid;
                }
            }
            foreach (array_unique($userids) as $userid) {
                $key = (int)$assignment->id . ':' . $userid;
                $desired[$key] = true;
                $this->trails->upsert_assignment_source($trailid, $userid, $assignment);
                $this->trails->ensure_enrolment($trailid, $userid, 'assignment', (int)$assignment->id);
            }
        }

        $affectedusers = [];
        foreach ($this->trails->get_assignment_source_records($trailid) as $source) {
            $key = (int)$source->assignmentid . ':' . (int)$source->userid;
            $affectedusers[(int)$source->userid] = true;
            if (!isset($desired[$key])) {
                $this->trails->delete_assignment_source((int)$source->id);
            }
        }

        foreach (array_keys($affectedusers) as $userid) {
            if (!$this->trails->user_has_assignment_source($trailid, (int)$userid)) {
                $this->trails->suspend_enrolment($trailid, (int)$userid);
            }
        }
        $transaction->allow_commit();
        return count($desired);
    }

    public function ensure_access_for_available_steps(array $steps, array $stepstates, int $userid): void {
        foreach ($steps as $step) {
            $state = $stepstates[$step->id] ?? null;
            if (!$state || empty($state['available'])) {
                continue;
            }
            $handler = $this->plugins->get_content_handler((string)$step->contenttype);
            if ($handler) {
                $handler->ensure_access($step, $userid);
            }
        }
    }
}
