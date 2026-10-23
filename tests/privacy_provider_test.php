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
 * Privacy API regression tests.
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail;

use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use local_kopere_trail\privacy\provider;

/**
 * Checks user discovery and approved deletion without affecting other users.
 *
 * @covers \local_kopere_trail\privacy\provider
 */
final class privacy_provider_test extends \advanced_testcase {
    /**
     * Verifies that direct assignments and audit events are included in user discovery and deletion.
     *
     * @return void
     */
    public function test_system_context_userlist_and_bulk_deletion(): void {
        global $DB;

        $this->resetAfterTest();
        $first = $this->getDataGenerator()->create_user();
        $second = $this->getDataGenerator()->create_user();
        $trailid = $DB->insert_record("local_kopere_trail", (object)[
            "name" => "Privacy regression trail",
        ]);

        foreach ([$first, $second] as $user) {
            $DB->insert_record("local_kopere_trail_assign", (object)[
                "trailid" => $trailid,
                "assigntype" => "user",
                "instanceid" => (int)$user->id,
            ]);
            $DB->insert_record("local_kopere_trail_event", (object)[
                "trailid" => $trailid,
                "userid" => (int)$user->id,
                "eventname" => "progress_changed",
                "details" => "{}",
            ]);
        }

        $systemcontext = \context_system::instance();
        $users = new userlist($systemcontext, "local_kopere_trail");
        provider::get_users_in_context($users);
        $this->assertContains((int)$first->id, $users->get_userids());
        $this->assertContains((int)$second->id, $users->get_userids());

        $othercontext = new userlist(\context_user::instance((int)$first->id), "local_kopere_trail");
        provider::get_users_in_context($othercontext);
        $this->assertEmpty($othercontext->get_userids());

        $approved = new approved_userlist($systemcontext, "local_kopere_trail", [(int)$first->id]);
        provider::delete_data_for_users($approved);

        $this->assertFalse($DB->record_exists("local_kopere_trail_assign", [
            "assigntype" => "user",
            "instanceid" => (int)$first->id,
        ]));
        $this->assertFalse($DB->record_exists("local_kopere_trail_event", ["userid" => (int)$first->id]));
        $this->assertTrue($DB->record_exists("local_kopere_trail_assign", [
            "assigntype" => "user",
            "instanceid" => (int)$second->id,
        ]));
        $this->assertTrue($DB->record_exists("local_kopere_trail_event", ["userid" => (int)$second->id]));
    }
}
