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
 * progress_repository.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\repository;

/**
 * Provides the progress repository implementation.
 */
class progress_repository {
    /**
     * Returns the trail progress.
     *
     * @param int $trailid The trailid.
     * @param int $userid The userid.
     * @return \stdClass|null The result.
     */
    public function get_trail_progress(int $trailid, int $userid): ?\stdClass {
        global $DB;

        $record = $DB->get_record('local_kopere_trail_prog', [
            'trailid' => $trailid,
            'userid' => $userid,
        ], '*', IGNORE_MISSING);

        return $record ?: null;
    }

    /**
     * Saves the trail progress.
     *
     * @param \stdClass $progress The progress.
     * @return \stdClass The result.
     */
    public function save_trail_progress(\stdClass $progress): \stdClass {
        global $DB;

        $progress->timemodified = time();
        if (!empty($progress->id)) {
            $DB->update_record('local_kopere_trail_prog', $progress);
            return $progress;
        }

        $progress->id = $DB->insert_record('local_kopere_trail_prog', $progress);
        return $progress;
    }

    /**
     * Returns the step progress.
     *
     * @param int $stepid The stepid.
     * @param int $userid The userid.
     * @return \stdClass|null The result.
     */
    public function get_step_progress(int $stepid, int $userid): ?\stdClass {
        global $DB;

        $record = $DB->get_record('local_kopere_trail_progstep', [
            'stepid' => $stepid,
            'userid' => $userid,
        ], '*', IGNORE_MISSING);

        return $record ?: null;
    }

    /**
     * Returns the step progress by trail.
     *
     * @param int $trailid The trailid.
     * @param int $userid The userid.
     * @return array The result.
     */
    public function get_step_progress_by_trail(int $trailid, int $userid): array {
        global $DB;

        $records = $DB->get_records('local_kopere_trail_progstep', [
            'trailid' => $trailid,
            'userid' => $userid,
        ], '', '*', 0, 0);

        $bystep = [];
        foreach ($records as $record) {
            $bystep[$record->stepid] = $record;
        }

        return $bystep;
    }

    /**
     * Saves the step progress.
     *
     * @param \stdClass $progress The progress.
     * @return \stdClass The result.
     */
    public function save_step_progress(\stdClass $progress): \stdClass {
        global $DB;

        $progress->timemodified = time();
        if (!empty($progress->id)) {
            $DB->update_record('local_kopere_trail_progstep', $progress);
            return $progress;
        }

        $progress->id = $DB->insert_record('local_kopere_trail_progstep', $progress);
        return $progress;
    }

    /**
     * Handles add event.
     *
     * @param int $trailid The trailid.
     * @param int $stepid The stepid.
     * @param int $userid The userid.
     * @param string $eventname The eventname.
     * @param array $details The details.
     * @return void The result.
     */
    public function add_event(int $trailid, int $stepid, int $userid, string $eventname, array $details = []): void {
        global $DB;

        $DB->insert_record('local_kopere_trail_event', (object)[
            'trailid' => $trailid,
            'stepid' => $stepid,
            'userid' => $userid,
            'eventname' => $eventname,
            'details' => json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'timecreated' => time(),
        ]);
    }

    /**
     * Returns the report rows.
     *
     * @param int $trailid The trailid.
     * @return array The result.
     */
    public function get_report_rows(int $trailid): array {
        global $DB;

        $namefields = get_all_user_name_fields(true, 'u');
        $sql = "SELECT e.id,
                       e.userid,
                       {$namefields},
                       u.email,
                       p.completedsteps,
                       p.totalsteps,
                       p.percent,
                       p.status,
                       p.xp,
                       p.currentstepid,
                       p.timemodified
                  FROM {local_kopere_trail_enrol} e
                  JOIN {user} u ON u.id = e.userid
             LEFT JOIN {local_kopere_trail_prog} p ON p.trailid = e.trailid AND p.userid = e.userid
                 WHERE e.trailid = :trailid
                   AND e.status = :status
                   AND u.deleted = 0
              ORDER BY u.firstname ASC, u.lastname ASC";

        return $DB->get_records_sql($sql, [
            'trailid' => $trailid,
            'status' => 'active',
        ]);
    }
}
