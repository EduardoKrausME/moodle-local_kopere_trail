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
 * enrolment_service_test.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail;

defined('MOODLE_INTERNAL') || die();

class enrolment_service_test extends \advanced_testcase {
    public function test_multiple_assignment_sources_keep_enrolment_until_last_source_disappears(): void {
        global $DB, $CFG;
        $this->resetAfterTest();
        require_once($CFG->dirroot . '/cohort/lib.php');
        $user = $this->getDataGenerator()->create_user();
        $cohort = (object)[
            'contextid' => \context_system::instance()->id,
            'name' => 'Trail cohort',
            'idnumber' => 'trail-test-cohort',
        ];
        $cohort->id = cohort_add_cohort($cohort);
        cohort_add_member((int)$cohort->id, (int)$user->id);
        $repository = new \local_kopere_trail\repository\trail_repository();
        $trailid = $repository->save_trail((object)['name' => 'Multiple sources', 'visible' => 1, 'summary' => '']);
        $repository->save_assignment((object)[
            'trailid' => $trailid,
            'assigntype' => 'user',
            'instanceid' => (int)$user->id,
            'status' => 'active',
        ]);
        $repository->save_assignment((object)[
            'trailid' => $trailid,
            'assigntype' => 'cohort',
            'instanceid' => (int)$cohort->id,
            'status' => 'active',
        ]);

        $service = new \local_kopere_trail\service\enrolment_service($repository);
        $service->sync_trail_assignments($trailid);
        $this->assertTrue($repository->has_active_enrolment($trailid, (int)$user->id));
        $this->assertEquals(2, $DB->count_records('local_kopere_trail_enrolsrc', [
            'trailid' => $trailid,
            'userid' => (int)$user->id,
        ]));

        $repository->save_assignment((object)[
            'trailid' => $trailid,
            'assigntype' => 'user',
            'instanceid' => (int)$user->id,
            'status' => 'suspended',
        ]);
        $service->sync_trail_assignments($trailid);
        $this->assertTrue($repository->has_active_enrolment($trailid, (int)$user->id));
        $this->assertEquals(1, $DB->count_records('local_kopere_trail_enrolsrc', [
            'trailid' => $trailid,
            'userid' => (int)$user->id,
        ]));

        cohort_remove_member((int)$cohort->id, (int)$user->id);
        $service->sync_trail_assignments($trailid);
        $this->assertFalse($repository->has_active_enrolment($trailid, (int)$user->id));
        $this->assertEquals(0, $DB->count_records('local_kopere_trail_enrolsrc', [
            'trailid' => $trailid,
            'userid' => (int)$user->id,
        ]));
    }

    public function test_access_service_honours_visibility_and_dates(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $repository = new \local_kopere_trail\repository\trail_repository();
        $trailid = $repository->save_trail((object)[
            'name' => 'Timed trail',
            'visible' => 1,
            'summary' => '',
            'startdate' => time() + DAYSECS,
        ]);
        $repository->ensure_enrolment($trailid, (int)$user->id, 'manual');
        $trail = $repository->get_trail($trailid);
        $service = new \local_kopere_trail\service\access_service($repository);
        $this->assertFalse($service->can_access($trail, (int)$user->id));

        $trail->startdate = 0;
        $trail->visible = 0;
        $this->assertFalse($service->can_access($trail, (int)$user->id));

        $trail->visible = 1;
        $this->assertTrue($service->can_access($trail, (int)$user->id));
    }
}
