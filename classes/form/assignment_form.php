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
 * assignment_form.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

class assignment_form extends \moodleform {
    protected function definition(): void {
        $mform = $this->_form;

        $mform->addElement('hidden', 'trailid');
        $mform->setType('trailid', PARAM_INT);

        $mform->addElement('select', 'assigntype', get_string('assignmenttype', 'local_kopere_trail'), [
            'user' => get_string('assignmenttype_user', 'local_kopere_trail'),
            'cohort' => get_string('assignmenttype_cohort', 'local_kopere_trail'),
        ]);

        $mform->addElement('autocomplete', 'userid', get_string('assignmentuser', 'local_kopere_trail'), [], [
            'ajax' => 'core_user/form_user_selector',
            'multiple' => false,
            'noselectionstring' => get_string('selectuser', 'local_kopere_trail'),
        ]);
        $mform->setType('userid', PARAM_INT);
        $mform->hideIf('userid', 'assigntype', 'neq', 'user');

        $mform->addElement('autocomplete', 'cohortid', get_string('assignmentcohort', 'local_kopere_trail'), [], [
            'multiple' => false,
            'ajax' => 'local_kopere_trail/form_cohort_selector',
            'noselectionstring' => get_string('selectcohort', 'local_kopere_trail'),
        ]);
        $mform->setType('cohortid', PARAM_INT);
        $mform->hideIf('cohortid', 'assigntype', 'neq', 'cohort');

        $mform->addElement('select', 'status', get_string('status', 'local_kopere_trail'), [
            'active' => get_string('active', 'local_kopere_trail'),
            'suspended' => get_string('suspended', 'local_kopere_trail'),
        ]);

        $this->add_action_buttons(true, get_string('savechanges', 'local_kopere_trail'));
    }

    public function validation($data, $files): array {
        global $DB;

        $errors = parent::validation($data, $files);
        $assigntype = (string)($data['assigntype'] ?? 'user');

        if ($assigntype === 'cohort') {
            $cohortid = (int)($data['cohortid'] ?? 0);
            if ($cohortid <= 0) {
                $errors['cohortid'] = get_string('required');
            } else if (!$DB->record_exists('cohort', ['id' => $cohortid])) {
                $errors['cohortid'] = get_string('invalidcohort', 'local_kopere_trail');
            }
        } else {
            $userid = (int)($data['userid'] ?? 0);
            if ($userid <= 0) {
                $errors['userid'] = get_string('required');
            } else if (!$DB->record_exists('user', ['id' => $userid, 'deleted' => 0])) {
                $errors['userid'] = get_string('invaliduser', 'local_kopere_trail');
            }
        }

        return $errors;
    }
}
