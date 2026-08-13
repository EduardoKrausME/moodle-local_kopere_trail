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
 * trail_repository.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\repository;

defined('MOODLE_INTERNAL') || die();

class trail_repository {
    public function get_trail(int $trailid, bool $mustexist = true): ?\stdClass {
        global $DB;
        $trail = $DB->get_record('local_kopere_trail', ['id' => $trailid], '*', $mustexist ? MUST_EXIST : IGNORE_MISSING);
        return $trail ?: null;
    }

    public function get_all_trails(bool $includehidden = true): array {
        global $DB;
        $params = [];
        $where = '1 = 1';
        if (!$includehidden) {
            $where .= ' AND visible = :visible';
            $params['visible'] = 1;
        }
        return $DB->get_records_select('local_kopere_trail', $where, $params, 'sortorder ASC, name ASC');
    }

    public function get_user_trails(int $userid): array {
        global $DB;
        $now = time();
        $sql = "SELECT t.*, p.percent, p.status AS progressstatus, p.completedsteps, p.totalsteps, p.xp
                  FROM {local_kopere_trail} t
                  JOIN {local_kopere_trail_enrol} e ON e.trailid = t.id
             LEFT JOIN {local_kopere_trail_prog} p ON p.trailid = t.id AND p.userid = e.userid
                 WHERE e.userid = :userid
                   AND e.status = :status
                   AND t.visible = 1
                   AND (t.startdate = 0 OR t.startdate <= :nowstart)
                   AND (t.enddate = 0 OR t.enddate >= :nowend)
              ORDER BY t.sortorder ASC, t.name ASC";
        return $DB->get_records_sql($sql, [
            'userid' => $userid,
            'status' => 'active',
            'nowstart' => $now,
            'nowend' => $now,
        ]);
    }

    public function save_trail(\stdClass $data): int {
        global $DB;
        $now = time();
        $record = (object)[
            'name' => $data->name,
            'code' => trim((string)($data->code ?? '')) !== '' ? trim((string)$data->code) : null,
            'summary' => is_array($data->summary ?? null) ? ($data->summary['text'] ?? null) : ($data->summary ?? null),
            'summaryformat' => (int)($data->summaryformat ?? (is_array($data->summary ?? null) ? ($data->summary['format'] ?? FORMAT_HTML) : FORMAT_HTML)),
            'visible' => empty($data->visible) ? 0 : 1,
            'startdate' => (int)($data->startdate ?? 0),
            'enddate' => (int)($data->enddate ?? 0),
            'config' => $data->config ?? null,
            'timemodified' => $now,
        ];
        if (!empty($data->id)) {
            $record->id = (int)$data->id;
            if (isset($data->sortorder)) {
                $record->sortorder = (int)$data->sortorder;
            }
            $DB->update_record('local_kopere_trail', $record);
            return $record->id;
        }
        $record->sortorder = $this->next_sortorder('local_kopere_trail');
        $record->timecreated = $now;
        return $DB->insert_record('local_kopere_trail', $record);
    }

    public function get_steps(int $trailid, bool $includehidden = false): array {
        global $DB;
        $params = ['trailid' => $trailid];
        $where = 'trailid = :trailid';
        if (!$includehidden) {
            $where .= ' AND visible = :visible';
            $params['visible'] = 1;
        }
        return $DB->get_records_select('local_kopere_trail_step', $where, $params, 'sortorder ASC, id ASC');
    }

    public function get_step(int $stepid, bool $mustexist = true): ?\stdClass {
        global $DB;
        $step = $DB->get_record('local_kopere_trail_step', ['id' => $stepid], '*', $mustexist ? MUST_EXIST : IGNORE_MISSING);
        return $step ?: null;
    }

    public function save_step(\stdClass $data): int {
        global $DB;
        $now = time();
        $record = (object)[
            'trailid' => (int)$data->trailid,
            'name' => $data->name,
            'description' => is_array($data->description ?? null) ? ($data->description['text'] ?? null) : ($data->description ?? null),
            'descriptionformat' => (int)($data->descriptionformat ?? (is_array($data->description ?? null) ? ($data->description['format'] ?? FORMAT_HTML) : FORMAT_HTML)),
            'contenttype' => $data->contenttype,
            'contentconfig' => $data->contentconfig ?? null,
            'completiontype' => $data->completiontype,
            'completionconfig' => $data->completionconfig ?? null,
            'prereqtype' => $data->prereqtype ?? 'previous',
            'prereqconfig' => $data->prereqconfig ?? null,
            'personalizationtype' => $data->personalizationtype ?? null,
            'personalizationconfig' => $data->personalizationconfig ?? null,
            'competencytype' => $data->competencytype ?? null,
            'competencyconfig' => $data->competencyconfig ?? null,
            'optional' => empty($data->optional) ? 0 : 1,
            'visible' => empty($data->visible) ? 0 : 1,
            'unlockmode' => $data->unlockmode ?? 'all',
            'points' => max(0, (int)($data->points ?? 0)),
            'estimatedtime' => max(0, (int)($data->estimatedtime ?? 0)),
            'timemodified' => $now,
        ];
        if (!empty($data->id)) {
            $record->id = (int)$data->id;
            if (isset($data->sortorder)) {
                $record->sortorder = (int)$data->sortorder;
            }
            $DB->update_record('local_kopere_trail_step', $record);
            return $record->id;
        }
        $record->sortorder = $this->next_sortorder('local_kopere_trail_step', ['trailid' => (int)$data->trailid]);
        $record->timecreated = $now;
        return $DB->insert_record('local_kopere_trail_step', $record);
    }

    public function get_edges(int $trailid): array {
        global $DB;
        return $DB->get_records('local_kopere_trail_edge', ['trailid' => $trailid], 'sortorder ASC, id ASC');
    }

    public function get_edge(int $edgeid, bool $mustexist = true): ?\stdClass {
        global $DB;
        $edge = $DB->get_record('local_kopere_trail_edge', ['id' => $edgeid], '*', $mustexist ? MUST_EXIST : IGNORE_MISSING);
        return $edge ?: null;
    }

    public function get_incoming_edges(int $trailid, int $stepid): array {
        global $DB;
        return $DB->get_records('local_kopere_trail_edge', ['trailid' => $trailid, 'tostepid' => $stepid], 'sortorder ASC, id ASC');
    }

    public function save_edge(\stdClass $data): int {
        global $DB;
        $now = time();
        $record = (object)[
            'trailid' => (int)$data->trailid,
            'fromstepid' => (int)$data->fromstepid,
            'tostepid' => (int)$data->tostepid,
            'ruleplugin' => $data->ruleplugin,
            'ruleconfig' => $data->ruleconfig ?? null,
            'timemodified' => $now,
        ];
        if (!empty($data->id)) {
            $record->id = (int)$data->id;
            if (isset($data->sortorder)) {
                $record->sortorder = (int)$data->sortorder;
            }
            $DB->update_record('local_kopere_trail_edge', $record);
            return $record->id;
        }
        $record->sortorder = $this->next_sortorder('local_kopere_trail_edge', ['trailid' => (int)$data->trailid]);
        $record->timecreated = $now;
        return $DB->insert_record('local_kopere_trail_edge', $record);
    }

    public function get_enrolment(int $trailid, int $userid): ?\stdClass {
        global $DB;
        $record = $DB->get_record('local_kopere_trail_enrol', ['trailid' => $trailid, 'userid' => $userid], '*', IGNORE_MISSING);
        return $record ?: null;
    }

    public function has_active_enrolment(int $trailid, int $userid): bool {
        global $DB;
        return $DB->record_exists('local_kopere_trail_enrol', ['trailid' => $trailid, 'userid' => $userid, 'status' => 'active']);
    }

    public function ensure_enrolment(int $trailid, int $userid, string $source = 'manual', int $sourceid = 0): \stdClass {
        global $DB;
        $record = $this->get_enrolment($trailid, $userid);
        $now = time();
        if ($record) {
            $record->status = 'active';
            $record->timeend = 0;
            if ($record->source !== 'manual' && $source === 'manual') {
                $record->source = 'manual';
                $record->sourceid = 0;
            }
            $record->timemodified = $now;
            $DB->update_record('local_kopere_trail_enrol', $record);
            return $record;
        }
        $record = (object)[
            'trailid' => $trailid,
            'userid' => $userid,
            'source' => $source,
            'sourceid' => $sourceid,
            'status' => 'active',
            'timestart' => $now,
            'timeend' => 0,
            'timecreated' => $now,
            'timemodified' => $now,
        ];
        $record->id = $DB->insert_record('local_kopere_trail_enrol', $record);
        return $record;
    }

    public function suspend_enrolment(int $trailid, int $userid): void {
        global $DB;
        $record = $this->get_enrolment($trailid, $userid);
        if (!$record || $record->source === 'manual') {
            return;
        }
        $record->status = 'suspended';
        $record->timeend = time();
        $record->timemodified = time();
        $DB->update_record('local_kopere_trail_enrol', $record);
    }

    public function get_active_assignments(?int $trailid = null): array {
        global $DB;
        $conditions = ['status' => 'active'];
        if ($trailid !== null) {
            $conditions['trailid'] = $trailid;
        }
        return $DB->get_records('local_kopere_trail_assign', $conditions, 'id ASC');
    }

    public function get_assignments_by_trail(int $trailid): array {
        global $DB;
        return $DB->get_records('local_kopere_trail_assign', ['trailid' => $trailid], 'assigntype ASC, id ASC');
    }

    public function save_assignment(\stdClass $data): int {
        global $DB;
        $now = time();
        $record = (object)[
            'trailid' => (int)$data->trailid,
            'assigntype' => $data->assigntype,
            'instanceid' => (int)$data->instanceid,
            'status' => $data->status ?? 'active',
            'timemodified' => $now,
        ];
        $existing = $DB->get_record('local_kopere_trail_assign', [
            'trailid' => $record->trailid,
            'assigntype' => $record->assigntype,
            'instanceid' => $record->instanceid,
        ], '*', IGNORE_MISSING);
        if ($existing) {
            $record->id = (int)$existing->id;
            $DB->update_record('local_kopere_trail_assign', $record);
            return $record->id;
        }
        $record->timecreated = $now;
        return $DB->insert_record('local_kopere_trail_assign', $record);
    }

    public function get_assignment_source_records(int $trailid): array {
        global $DB;
        return $DB->get_records('local_kopere_trail_enrolsrc', ['trailid' => $trailid], 'id ASC');
    }

    public function upsert_assignment_source(int $trailid, int $userid, \stdClass $assignment): void {
        global $DB;
        $existing = $DB->get_record('local_kopere_trail_enrolsrc', [
            'assignmentid' => (int)$assignment->id,
            'userid' => $userid,
        ], '*', IGNORE_MISSING);
        $now = time();
        if ($existing) {
            $existing->trailid = $trailid;
            $existing->assigntype = $assignment->assigntype;
            $existing->instanceid = (int)$assignment->instanceid;
            $existing->timemodified = $now;
            $DB->update_record('local_kopere_trail_enrolsrc', $existing);
            return;
        }
        $DB->insert_record('local_kopere_trail_enrolsrc', (object)[
            'trailid' => $trailid,
            'userid' => $userid,
            'assignmentid' => (int)$assignment->id,
            'assigntype' => $assignment->assigntype,
            'instanceid' => (int)$assignment->instanceid,
            'timecreated' => $now,
            'timemodified' => $now,
        ]);
    }

    public function delete_assignment_source(int $sourceid): void {
        global $DB;
        $DB->delete_records('local_kopere_trail_enrolsrc', ['id' => $sourceid]);
    }

    public function user_has_assignment_source(int $trailid, int $userid): bool {
        global $DB;
        return $DB->record_exists('local_kopere_trail_enrolsrc', ['trailid' => $trailid, 'userid' => $userid]);
    }

    public function get_trailids_requiring_sync(): array {
        global $DB;
        $sql = "SELECT DISTINCT trailid FROM {local_kopere_trail_assign}
                UNION
                SELECT DISTINCT trailid FROM {local_kopere_trail_enrolsrc}";
        $records = $DB->get_records_sql($sql);
        return array_map('intval', array_keys($records));
    }

    public function get_active_enrolments(int $limitfrom = 0, int $limitnum = 0): array {
        global $DB;
        return $DB->get_records('local_kopere_trail_enrol', ['status' => 'active'], 'id ASC', '*', $limitfrom, $limitnum);
    }

    public function move_trail(int $id, string $direction): void {
        $this->move_record('local_kopere_trail', $id, [], $direction);
    }

    public function move_step(int $id, string $direction): void {
        $step = $this->get_step($id);
        $this->move_record('local_kopere_trail_step', $id, ['trailid' => (int)$step->trailid], $direction);
    }

    public function move_edge(int $id, string $direction): void {
        $edge = $this->get_edge($id);
        $this->move_record('local_kopere_trail_edge', $id, ['trailid' => (int)$edge->trailid], $direction);
    }

    private function next_sortorder(string $table, array $conditions = []): int {
        global $DB;
        $records = $DB->get_records($table, $conditions, 'sortorder DESC', 'id, sortorder', 0, 1);
        if (!$records) {
            return 10;
        }
        $record = reset($records);
        return ((int)$record->sortorder) + 10;
    }

    private function move_record(string $table, int $id, array $conditions, string $direction): void {
        global $DB;
        $current = $DB->get_record($table, ['id' => $id] + $conditions, '*', MUST_EXIST);
        $params = $conditions;
        $params['sortorder'] = (int)$current->sortorder;
        if ($direction === 'up') {
            $where = 'sortorder < :sortorder';
            $sort = 'sortorder DESC, id DESC';
        } else {
            $where = 'sortorder > :sortorder';
            $sort = 'sortorder ASC, id ASC';
        }
        foreach ($conditions as $field => $value) {
            $where .= " AND {$field} = :{$field}";
        }
        $records = $DB->get_records_select($table, $where, $params, $sort, '*', 0, 1);
        if (!$records) {
            return;
        }
        $other = reset($records);
        $old = (int)$current->sortorder;
        $current->sortorder = (int)$other->sortorder;
        $other->sortorder = $old;
        $transaction = $DB->start_delegated_transaction();
        $DB->update_record($table, $current);
        $DB->update_record($table, $other);
        $transaction->allow_commit();
    }
}
