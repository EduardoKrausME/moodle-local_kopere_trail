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
 * handler.php
 *
 * @package   trailcontent_moodlecourse
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailcontent_moodlecourse;

defined('MOODLE_INTERNAL') || die();

class handler implements \local_kopere_trail\contract\content_provider, \local_kopere_trail\contract\configurable_provider {
    public function get_name(): string {
        return get_string('pluginname', 'trailcontent_moodlecourse');
    }

    public function get_icon(): string {
        return 'i/course';
    }

    public function add_configuration_fields(\MoodleQuickForm $mform, string $selectorfield, ?\stdClass $currentdata = null): void {
        $selected = (int)($currentdata->contentcourseid ?? 0);
        $mform->addElement('autocomplete', 'contentcourseid', get_string('contentcourseid', 'local_kopere_trail'),
            \local_kopere_trail\form\configuration_fields::get_course_options($selected), [
                'multiple' => false,
                'ajax' => 'core_course/form-course-selector',
                'noselectionstring' => get_string('selectcourse', 'local_kopere_trail'),
            ]);
        $mform->setType('contentcourseid', PARAM_INT);
        $mform->hideIf('contentcourseid', $selectorfield, 'neq', 'moodlecourse');
    }

    public function prepare_configuration(\stdClass $data, array $config): \stdClass {
        $data->contentcourseid = (int)($config['courseid'] ?? 0);
        return $data;
    }

    public function build_configuration(\stdClass $data): array {
        return ['courseid' => (int)($data->contentcourseid ?? 0)];
    }

    public function validate_configuration(array $data): array {
        global $DB, $SITE;
        $courseid = (int)($data['contentcourseid'] ?? 0);
        if ($courseid <= 0) {
            return ['contentcourseid' => get_string('required')];
        }
        if ($courseid === (int)$SITE->id || !$DB->record_exists('course', ['id' => $courseid])) {
            return ['contentcourseid' => get_string('invalidcourse', 'local_kopere_trail')];
        }
        return [];
    }

    public function get_launch_url(\stdClass $step, int $userid): ?\moodle_url {
        $config = \local_kopere_trail\json::decode($step->contentconfig);
        $courseid = (int)($config['courseid'] ?? 0);
        return $courseid > 0 ? new \moodle_url('/course/view.php', ['id' => $courseid]) : null;
    }

    public function ensure_access(\stdClass $step, int $userid): void {
        $config = \local_kopere_trail\json::decode($step->contentconfig);
        $courseid = (int)($config['courseid'] ?? 0);
        if ($courseid > 0) {
            \local_kopere_trail\service\course_access_helper::ensure_course_enrolment($courseid, $userid);
        }
    }

    public function export_view_data(\stdClass $step, int $userid): array {
        return [];
    }
}
