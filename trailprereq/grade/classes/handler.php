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
 * @package   trailprereq_grade
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailprereq_grade;

defined('MOODLE_INTERNAL') || die();

class handler implements \local_kopere_trail\contract\prereq_provider, \local_kopere_trail\contract\configurable_provider {
    public function get_name(): string { return get_string('pluginname', 'trailprereq_grade'); }

    public function add_configuration_fields(\MoodleQuickForm $mform, string $selectorfield, ?\stdClass $currentdata = null): void {
        $selected = (int)($currentdata->gradeitemid ?? 0);
        $mform->addElement('autocomplete', 'gradeitemid', get_string('gradeitemid', 'local_kopere_trail'),
            \local_kopere_trail\form\configuration_fields::get_grade_item_options($selected), [
                'multiple' => false,
                'ajax' => 'local_kopere_trail/form_gradeitem_selector',
                'noselectionstring' => get_string('selectgradeitem', 'local_kopere_trail'),
            ]);
        $mform->setType('gradeitemid', PARAM_INT);
        $mform->hideIf('gradeitemid', $selectorfield, 'neq', 'grade');
        $mform->addElement('text', 'mingrade', get_string('mingrade', 'local_kopere_trail'));
        $mform->setType('mingrade', PARAM_FLOAT);
        $mform->setDefault('mingrade', 0);
        $mform->hideIf('mingrade', $selectorfield, 'neq', 'grade');
        $mform->addHelpButton('mingrade', 'mingrade', 'local_kopere_trail');
    }

    public function prepare_configuration(\stdClass $data, array $config): \stdClass {
        $data->gradeitemid = (int)($config['gradeitemid'] ?? 0);
        $data->mingrade = isset($config['mingrade']) ? (float)$config['mingrade'] : 0;
        return $data;
    }
    public function build_configuration(\stdClass $data): array { return ['gradeitemid' => (int)($data->gradeitemid ?? 0), 'mingrade' => (float)($data->mingrade ?? 0)]; }
    public function validate_configuration(array $data): array {
        global $DB;
        $gradeitemid = (int)($data['gradeitemid'] ?? 0);
        if ($gradeitemid <= 0) { return ['gradeitemid' => get_string('required')]; }
        if (!$DB->record_exists('grade_items', ['id' => $gradeitemid])) {
            return ['gradeitemid' => get_string('invalidgradeitem', 'local_kopere_trail')];
        }
        return [];
    }

    public function is_available(\stdClass $edge, \stdClass $fromstep, \stdClass $tostep, int $userid): bool {
        global $DB;
        $config = \local_kopere_trail\json::decode($edge->ruleconfig);
        $gradeitemid = (int)($config['gradeitemid'] ?? 0);
        $mingrade = (float)($config['mingrade'] ?? 0);
        if ($gradeitemid <= 0) { return true; }
        $grade = $DB->get_record('grade_grades', ['itemid' => $gradeitemid, 'userid' => $userid], 'id, finalgrade', IGNORE_MISSING);
        return $grade && $grade->finalgrade !== null && (float)$grade->finalgrade >= $mingrade;
    }
    public function get_blocked_reason(\stdClass $edge, \stdClass $fromstep, \stdClass $tostep, int $userid): string { return get_string('nextlocked', 'local_kopere_trail'); }
}
