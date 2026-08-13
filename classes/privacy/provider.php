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
 * provider.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\privacy;

/**
 * Provides the provider implementation.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Returns the metadata.
     *
     * @param \core_privacy\local\metadata\collection $collection The collection.
     * @return \core_privacy\local\metadata\collection The result.
     */
    public static function get_metadata(
        \core_privacy\local\metadata\collection $collection
    ): \core_privacy\local\metadata\collection {
        $collection->add_database_table('local_kopere_trail_enrol', [
            'trailid' => 'privacy:metadata:trailid',
            'userid' => 'privacy:metadata:userid',
            'status' => 'privacy:metadata:status',
        ], 'privacy:metadata:enrol');

        $collection->add_database_table('local_kopere_trail_enrolsrc', [
            'trailid' => 'privacy:metadata:trailid',
            'userid' => 'privacy:metadata:userid',
        ], 'privacy:metadata:enrolsource');

        $collection->add_database_table('local_kopere_trail_prog', [
            'trailid' => 'privacy:metadata:trailid',
            'userid' => 'privacy:metadata:userid',
            'percent' => 'privacy:metadata:percent',
            'xp' => 'privacy:metadata:xp',
            'status' => 'privacy:metadata:status',
        ], 'privacy:metadata:progress');

        $collection->add_database_table('local_kopere_trail_progstep', [
            'trailid' => 'privacy:metadata:trailid',
            'stepid' => 'privacy:metadata:stepid',
            'userid' => 'privacy:metadata:userid',
            'status' => 'privacy:metadata:status',
            'progresspercent' => 'privacy:metadata:progresspercent',
        ], 'privacy:metadata:stepprogress');

        return $collection;
    }

    /**
     * Returns the contexts for userid.
     *
     * @param int $userid The userid.
     * @return \core_privacy\local\request\contextlist The result.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        global $DB;

        $contextlist = new \core_privacy\local\request\contextlist();
        $hasdata = $DB->record_exists('local_kopere_trail_assign', ['assigntype' => 'user', 'instanceid' => $userid])
            || $DB->record_exists('local_kopere_trail_enrol', ['userid' => $userid])
            || $DB->record_exists('local_kopere_trail_enrolsrc', ['userid' => $userid])
            || $DB->record_exists('local_kopere_trail_prog', ['userid' => $userid])
            || $DB->record_exists('local_kopere_trail_progstep', ['userid' => $userid])
            || $DB->record_exists('local_kopere_trail_event', ['userid' => $userid]);

        if ($hasdata) {
            $contextlist->add_context(\context_system::instance());
        }

        return $contextlist;
    }

    /**
     * Exports the user data.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist The contextlist.
     * @return void The result.
     */
    public static function export_user_data(\core_privacy\local\request\approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_SYSTEM) {
                continue;
            }

            $data = (object)[
                'assignments' => array_values($DB->get_records('local_kopere_trail_assign', [
                    'assigntype' => 'user',
                    'instanceid' => $userid,
                ])),
                'enrolments' => array_values($DB->get_records('local_kopere_trail_enrol', ['userid' => $userid])),
                'enrolmentsources' => array_values($DB->get_records('local_kopere_trail_enrolsrc', ['userid' => $userid])),
                'progress' => array_values($DB->get_records('local_kopere_trail_prog', ['userid' => $userid])),
                'steps' => array_values($DB->get_records('local_kopere_trail_progstep', ['userid' => $userid])),
                'events' => array_values($DB->get_records('local_kopere_trail_event', ['userid' => $userid])),
            ];

            \core_privacy\local\request\writer::with_context($context)->export_data([
                get_string('pluginname', 'local_kopere_trail'),
            ], $data);
        }
    }

    /**
     * Deletes the data for all users in context.
     *
     * @param \context $context The context.
     * @return void The result.
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $DB->delete_records('local_kopere_trail_enrolsrc');
        $DB->delete_records('local_kopere_trail_enrol');
        $DB->delete_records('local_kopere_trail_prog');
        $DB->delete_records('local_kopere_trail_progstep');
        $DB->delete_records('local_kopere_trail_event');
        $DB->delete_records('local_kopere_trail_assign', ['assigntype' => 'user']);
    }

    /**
     * Deletes the data for user.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist The contextlist.
     * @return void The result.
     */
    public static function delete_data_for_user(\core_privacy\local\request\approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_SYSTEM) {
                continue;
            }

            $DB->delete_records('local_kopere_trail_enrolsrc', ['userid' => $userid]);
            $DB->delete_records('local_kopere_trail_enrol', ['userid' => $userid]);
            $DB->delete_records('local_kopere_trail_prog', ['userid' => $userid]);
            $DB->delete_records('local_kopere_trail_progstep', ['userid' => $userid]);
            $DB->delete_records('local_kopere_trail_event', ['userid' => $userid]);
            $DB->delete_records('local_kopere_trail_assign', ['assigntype' => 'user', 'instanceid' => $userid]);
        }
    }
}
