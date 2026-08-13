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
 * upgrade.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_kopere_trail_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026081204) {
        $steptable = new xmldb_table('local_kopere_trail_step');
        $competencytype = new xmldb_field('competencytype', XMLDB_TYPE_CHAR, '100', null, false, false, null, 'personalizationconfig');
        if (!$dbman->field_exists($steptable, $competencytype)) {
            $dbman->add_field($steptable, $competencytype);
        }
        $competencyconfig = new xmldb_field('competencyconfig', XMLDB_TYPE_TEXT, null, null, false, false, null, 'competencytype');
        if (!$dbman->field_exists($steptable, $competencyconfig)) {
            $dbman->add_field($steptable, $competencyconfig);
        }

        $table = new xmldb_table('local_kopere_trail_enrolsrc');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('trailid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('assignmentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('assigntype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
            $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_key('trail_fk', XMLDB_KEY_FOREIGN, ['trailid'], 'local_kopere_trail', ['id']);
            $table->add_key('assignment_fk', XMLDB_KEY_FOREIGN, ['assignmentid'], 'local_kopere_trail_assign', ['id']);
            $table->add_index('assignment_user_uix', XMLDB_INDEX_UNIQUE, ['assignmentid', 'userid']);
            $table->add_index('trail_user_ix', XMLDB_INDEX_NOTUNIQUE, ['trailid', 'userid']);
            $dbman->create_table($table);
        }

        $sql = "SELECT e.id, e.trailid, e.userid, e.sourceid, a.assigntype, a.instanceid
                  FROM {local_kopere_trail_enrol} e
                  JOIN {local_kopere_trail_assign} a ON a.id = e.sourceid
                 WHERE e.source <> :manual
                   AND e.sourceid > 0";
        foreach ($DB->get_records_sql($sql, ['manual' => 'manual']) as $record) {
            if (!$DB->record_exists('local_kopere_trail_enrolsrc', [
                'assignmentid' => (int)$record->sourceid,
                'userid' => (int)$record->userid,
            ])) {
                $DB->insert_record('local_kopere_trail_enrolsrc', (object)[
                    'trailid' => (int)$record->trailid,
                    'userid' => (int)$record->userid,
                    'assignmentid' => (int)$record->sourceid,
                    'assigntype' => $record->assigntype,
                    'instanceid' => (int)$record->instanceid,
                    'timecreated' => time(),
                    'timemodified' => time(),
                ]);
            }
        }

        upgrade_plugin_savepoint(true, 2026081204, 'local', 'kopere_trail');
    }

    return true;
}
