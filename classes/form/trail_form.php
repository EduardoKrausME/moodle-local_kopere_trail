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
 * trail_form.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Provides the trail form implementation.
 */
class trail_form extends \moodleform {
    /**
     * Defines the form fields.
     *
     * @return void The result.
     */
    protected function definition(): void {
        $mform = $this->_form;
        $plugins = new \local_kopere_trail\service\subplugin_manager();
        $configuration = new \local_kopere_trail\service\configuration_service($plugins);
        $currentdata = $this->_customdata['currentdata'] ?? null;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('text', 'name', get_string('name', 'local_kopere_trail'), ['size' => 60]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required');
        $mform->addElement('text', 'code', get_string('code', 'local_kopere_trail'), ['size' => 30]);
        $mform->setType('code', PARAM_ALPHANUMEXT);
        $mform->addElement('editor', 'summary_editor', get_string('summary', 'local_kopere_trail'), null, [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'trusttext' => true,
            'context' => \context_system::instance(),
        ]);
        $mform->setType('summary_editor', PARAM_RAW);
        $mform->addElement('selectyesno', 'visible', get_string('visible', 'local_kopere_trail'));
        $mform->setDefault('visible', 1);
        $mform->addElement('date_time_selector', 'startdate', get_string('startdate', 'local_kopere_trail'), ['optional' => true]);
        $mform->addElement('date_time_selector', 'enddate', get_string('enddate', 'local_kopere_trail'), ['optional' => true]);

        $mform->addElement('select', 'certtype', get_string('certtype', 'local_kopere_trail'),
            $plugins->get_options(\local_kopere_trail\service\subplugin_manager::TYPE_CERT, true));
        $configuration->add_provider_fields(
            $mform,
            \local_kopere_trail\service\subplugin_manager::TYPE_CERT,
            'certtype',
            $currentdata
        );

        $mform->addElement('select', 'gamificationtype', get_string('gamificationtype', 'local_kopere_trail'),
            $plugins->get_options(\local_kopere_trail\service\subplugin_manager::TYPE_GAMIFICATION, true));
        $mform->setDefault('gamificationtype', 'progress');
        $this->add_action_buttons(true, get_string('savechanges', 'local_kopere_trail'));
    }

    /**
     * Validates submitted form data.
     *
     * @param mixed $data The data.
     * @param mixed $files The files.
     * @return array The result.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        if (!empty($data['startdate']) && !empty($data['enddate']) && (int)$data['enddate'] < (int)$data['startdate']) {
            $errors['enddate'] = get_string('endbeforestart', 'local_kopere_trail');
        }
        $configuration = new \local_kopere_trail\service\configuration_service();
        $errors += $configuration->validate_provider(
            \local_kopere_trail\service\subplugin_manager::TYPE_CERT,
            (string)($data['certtype'] ?? ''),
            $data
        );
        return $errors;
    }
}
