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
 * progress_service_test.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail;

/**
 * Provides the progress service test implementation.
 *
 * @coversNothing
 */
final class progress_service_test extends \advanced_testcase {
    /**
     * Handles test rebuild does not create enrolment.
     *
     * @return void The result.
     */
    public function test_rebuild_does_not_create_enrolment(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $repository = new \local_kopere_trail\repository\trail_repository();
        $trailid = $repository->save_trail((object)[
            'name' => 'Safety trail',
            'visible' => 1,
            'summary' => '',
            'summaryformat' => FORMAT_HTML,
        ]);

        $this->expectException(\required_capability_exception::class);
        try {
            (new \local_kopere_trail\service\progress_service($repository))->rebuild_user_progress($trailid, (int)$user->id);
        } finally {
            $this->assertFalse($DB->record_exists('local_kopere_trail_enrol', [
                'trailid' => $trailid,
                'userid' => (int)$user->id,
            ]));
        }
    }

    /**
     * Handles test personalized removed source step does not keep destination locked.
     *
     * @return void The result.
     */
    public function test_personalized_removed_source_step_does_not_keep_destination_locked(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $repository = new \local_kopere_trail\repository\trail_repository();
        $trailid = $repository->save_trail((object)['name' => 'Graph trail', 'visible' => 1, 'summary' => '']);
        $repository->ensure_enrolment($trailid, (int)$user->id, 'manual');

        $hiddenbyrule = $repository->save_step((object)[
            'trailid' => $trailid,
            'name' => 'Personalized source',
            'contenttype' => 'html',
            'contentconfig' => json_encode(['html' => 'A', 'format' => FORMAT_HTML]),
            'completiontype' => 'manual',
            'personalizationtype' => 'rules',
            'personalizationconfig' => json_encode(['cohortids' => [999999]]),
            'visible' => 1,
            'unlockmode' => 'all',
        ]);
        $destination = $repository->save_step((object)[
            'trailid' => $trailid,
            'name' => 'Destination',
            'contenttype' => 'html',
            'contentconfig' => json_encode(['html' => 'B', 'format' => FORMAT_HTML]),
            'completiontype' => 'manual',
            'visible' => 1,
            'unlockmode' => 'all',
        ]);
        $repository->save_edge((object)[
            'trailid' => $trailid,
            'fromstepid' => $hiddenbyrule,
            'tostepid' => $destination,
            'ruleplugin' => 'step',
        ]);

        $service = new \local_kopere_trail\service\progress_service($repository);
        $progress = $service->rebuild_user_progress($trailid, (int)$user->id);
        $this->assertArrayHasKey($destination, $progress->stepstates);
        $this->assertTrue($progress->stepstates[$destination]['available']);
    }
}
