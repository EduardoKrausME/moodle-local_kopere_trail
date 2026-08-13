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
 * @package   trailcompletion_coursecompletion
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailcompletion_coursecompletion;

defined('MOODLE_INTERNAL') || die();

class handler implements \local_kopere_trail\contract\completion_provider, \local_kopere_trail\contract\configurable_provider {
    public function get_name(): string { return get_string('pluginname', 'trailcompletion_coursecompletion'); }

    public function add_configuration_fields(\MoodleQuickForm $mform, string $selectorfield, ?\stdClass $currentdata = null): void {
        $selected = (int)($currentdata->completioncourseid ?? 0);
        $mform->addElement('autocomplete', 'completioncourseid', get_string('completioncourseid', 'local_kopere_trail'),
            \local_kopere_trail\form\configuration_fields::get_course_options($selected), [
                'multiple' => false,
                'ajax' => 'core_course/form-course-selector',
                'noselectionstring' => get_string('selectcourse', 'local_kopere_trail'),
            ]);
        $mform->setType('completioncourseid', PARAM_INT);
        $mform->hideIf('completioncourseid', $selectorfield, 'neq', 'coursecompletion');
    }

    public function prepare_configuration(\stdClass $data, array $config): \stdClass { $data->completioncourseid = (int)($config['courseid'] ?? 0); return $data; }
    public function build_configuration(\stdClass $data): array { return ['courseid' => (int)($data->completioncourseid ?? 0)]; }
    public function validate_configuration(array $data): array {
        global $DB, $SITE;
        $courseid = (int)($data['completioncourseid'] ?? 0);
        if ($courseid <= 0) { return ['completioncourseid' => get_string('required')]; }
        if ($courseid === (int)$SITE->id || !$DB->record_exists('course', ['id' => $courseid])) {
            return ['completioncourseid' => get_string('invalidcourse', 'local_kopere_trail')];
        }
        return [];
    }
    public function can_complete_manually(\stdClass $step, int $userid): bool { return false; }

    public function get_completion(\stdClass $step, int $userid): array {
        global $DB, $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        $config = array_merge(\local_kopere_trail\json::decode($step->contentconfig), \local_kopere_trail\json::decode($step->completionconfig));
        $courseid = (int)($config['courseid'] ?? 0);
        $course = $courseid > 0 ? $DB->get_record('course', ['id' => $courseid], '*', IGNORE_MISSING) : false;
        if (!$course) { return $this->result(false, 0, ['reason' => 'course_not_found']); }
        $completion = new \completion_info($course);
        $completed = $completion->is_course_complete($userid);
        return $this->result($completed, $completed ? 100 : 0, ['courseid' => $courseid]);
    }
    private function result(bool $completed, float $percent, array $details): array {
        return ['completed' => $completed, 'progresspercent' => $percent, 'source' => 'coursecompletion', 'details' => $details];
    }
}
