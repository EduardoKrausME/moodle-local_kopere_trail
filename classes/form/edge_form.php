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
 * edge_form.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

class edge_form extends \moodleform {
    protected function definition(): void {
        $mform = $this->_form;
        $steps = $this->_customdata['steps'] ?? [];
        $currentdata = $this->_customdata['currentdata'] ?? null;
        $plugins = new \local_kopere_trail\service\subplugin_manager();
        $configuration = new \local_kopere_trail\service\configuration_service($plugins);
        $stepoptions = [];
        foreach ($steps as $step) {
            $stepoptions[$step->id] = format_string($step->name);
        }
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'trailid');
        $mform->setType('trailid', PARAM_INT);
        $mform->addElement('select', 'fromstepid', get_string('fromstep', 'local_kopere_trail'), $stepoptions);
        $mform->addRule('fromstepid', get_string('required'), 'required');
        $mform->addElement('select', 'tostepid', get_string('tostep', 'local_kopere_trail'), $stepoptions);
        $mform->addRule('tostepid', get_string('required'), 'required');
        $mform->addElement('select', 'ruleplugin', get_string('prereqtype', 'local_kopere_trail'),
            $plugins->get_options(\local_kopere_trail\service\subplugin_manager::TYPE_PREREQ));
        $mform->setDefault('ruleplugin', 'step');
        $configuration->add_provider_fields($mform, \local_kopere_trail\service\subplugin_manager::TYPE_PREREQ, 'ruleplugin', $currentdata);
        $this->add_action_buttons(true, get_string('savechanges', 'local_kopere_trail'));
    }

    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        if ((int)($data['fromstepid'] ?? 0) === (int)($data['tostepid'] ?? 0)) {
            $errors['tostepid'] = get_string('edge_same_step', 'local_kopere_trail');
        }
        $configuration = new \local_kopere_trail\service\configuration_service();
        $errors += $configuration->validate_provider(
            \local_kopere_trail\service\subplugin_manager::TYPE_PREREQ,
            (string)($data['ruleplugin'] ?? ''),
            $data
        );
        return $errors;
    }
}
