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
 * @package   trailcompletion_activitycompletion
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailcompletion_activitycompletion;

defined('MOODLE_INTERNAL') || die();

class handler implements \local_kopere_trail\contract\completion_provider, \local_kopere_trail\contract\configurable_provider {
    public function get_name(): string { return get_string('pluginname', 'trailcompletion_activitycompletion'); }

    public function add_configuration_fields(\MoodleQuickForm $mform, string $selectorfield, ?\stdClass $currentdata = null): void {
        $selected = (int)($currentdata->completioncmid ?? 0);
        $mform->addElement('autocomplete', 'completioncmid', get_string('completioncmid', 'local_kopere_trail'),
            \local_kopere_trail\form\configuration_fields::get_activity_options($selected), [
                'multiple' => false,
                'ajax' => 'local_kopere_trail/form_activity_selector',
                'noselectionstring' => get_string('selectactivity', 'local_kopere_trail'),
            ]);
        $mform->setType('completioncmid', PARAM_INT);
        $mform->hideIf('completioncmid', $selectorfield, 'neq', 'activitycompletion');
    }

    public function prepare_configuration(\stdClass $data, array $config): \stdClass {
        $data->completioncmid = (int)($config['cmid'] ?? 0);
        return $data;
    }
    public function build_configuration(\stdClass $data): array { return ['cmid' => (int)($data->completioncmid ?? 0)]; }
    public function validate_configuration(array $data): array {
        $cmid = (int)($data['completioncmid'] ?? 0);
        if ($cmid <= 0) { return ['completioncmid' => get_string('required')]; }
        if (!get_coursemodule_from_id('', $cmid, 0, false, IGNORE_MISSING)) {
            return ['completioncmid' => get_string('invalidactivity', 'local_kopere_trail')];
        }
        return [];
    }
    public function can_complete_manually(\stdClass $step, int $userid): bool { return false; }

    public function get_completion(\stdClass $step, int $userid): array {
        global $DB, $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        $config = array_merge(\local_kopere_trail\json::decode($step->contentconfig), \local_kopere_trail\json::decode($step->completionconfig));
        $cmid = (int)($config['cmid'] ?? 0);
        if ($cmid <= 0) { return $this->result(false, 0, ['reason' => 'cmid_missing']); }
        $cm = get_coursemodule_from_id('', $cmid, 0, false, IGNORE_MISSING);
        if (!$cm) { return $this->result(false, 0, ['reason' => 'cm_not_found']); }
        $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
        $completion = new \completion_info($course);
        $data = $completion->get_data($cm, false, $userid);
        $completed = in_array((int)$data->completionstate, [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS], true);
        return $this->result($completed, $completed ? 100 : 0, ['cmid' => $cmid, 'completionstate' => (int)$data->completionstate]);
    }

    private function result(bool $completed, float $percent, array $details): array {
        return ['completed' => $completed, 'progresspercent' => $percent, 'source' => 'activitycompletion', 'details' => $details];
    }
}
