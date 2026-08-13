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
 * step_form.php
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
 * Provides the step form implementation.
 */
class step_form extends \moodleform {
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
        $mform->addElement('hidden', 'trailid');
        $mform->setType('trailid', PARAM_INT);
        $mform->addElement('text', 'name', get_string('name', 'local_kopere_trail'), ['size' => 60]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required');
        $mform->addElement('editor', 'description_editor', get_string('description', 'local_kopere_trail'), null, [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'trusttext' => true,
            'context' => \context_system::instance(),
        ]);
        $mform->setType('description_editor', PARAM_RAW);

        $mform->addElement('select', 'contenttype', get_string('contenttype', 'local_kopere_trail'),
            $plugins->get_options(\local_kopere_trail\service\subplugin_manager::TYPE_CONTENT));
        $mform->setDefault('contenttype', 'html');
        $configuration->add_provider_fields(
            $mform,
            \local_kopere_trail\service\subplugin_manager::TYPE_CONTENT,
            'contenttype',
            $currentdata
        );

        $mform->addElement('select', 'completiontype', get_string('completiontype', 'local_kopere_trail'),
            $plugins->get_options(\local_kopere_trail\service\subplugin_manager::TYPE_COMPLETION));
        $mform->setDefault('completiontype', 'manual');
        $configuration->add_provider_fields(
            $mform,
            \local_kopere_trail\service\subplugin_manager::TYPE_COMPLETION,
            'completiontype',
            $currentdata
        );

        $mform->addElement('select', 'personalizationtype', get_string('personalizationtype', 'local_kopere_trail'),
            $plugins->get_options(\local_kopere_trail\service\subplugin_manager::TYPE_PERSONALIZATION, true));
        $configuration->add_provider_fields(
            $mform,
            \local_kopere_trail\service\subplugin_manager::TYPE_PERSONALIZATION,
            'personalizationtype',
            $currentdata
        );

        $mform->addElement('select', 'competencytype', get_string('competencytype', 'local_kopere_trail'),
            $plugins->get_options(\local_kopere_trail\service\subplugin_manager::TYPE_COMPETENCY, true));
        $configuration->add_provider_fields(
            $mform,
            \local_kopere_trail\service\subplugin_manager::TYPE_COMPETENCY,
            'competencytype',
            $currentdata
        );

        $mform->addElement('static', 'prerequisiteinfo', get_string('prerequisites', 'local_kopere_trail'),
            get_string('prerequisites_edges_info', 'local_kopere_trail'));
        $mform->addElement('select', 'unlockmode', get_string('unlockmode', 'local_kopere_trail'), [
            'all' => get_string('unlockmode_all', 'local_kopere_trail'),
            'any' => get_string('unlockmode_any', 'local_kopere_trail'),
        ]);
        $mform->addElement('advcheckbox', 'optional', get_string('optional', 'local_kopere_trail'));
        $mform->addElement('advcheckbox', 'visible', get_string('visible', 'local_kopere_trail'));
        $mform->setDefault('visible', 1);
        $mform->addElement('text', 'points', get_string('points', 'local_kopere_trail'));
        $mform->setType('points', PARAM_INT);
        $mform->setDefault('points', 0);
        $mform->addElement('text', 'estimatedtime', get_string('estimatedtime', 'local_kopere_trail'));
        $mform->setType('estimatedtime', PARAM_INT);
        $mform->setDefault('estimatedtime', 0);
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
        if ((int)($data['points'] ?? 0) < 0) {
            $errors['points'] = get_string('nonnegativevalue', 'local_kopere_trail');
        }
        if ((int)($data['estimatedtime'] ?? 0) < 0) {
            $errors['estimatedtime'] = get_string('nonnegativevalue', 'local_kopere_trail');
        }
        $configuration = new \local_kopere_trail\service\configuration_service();
        foreach ([
            [\local_kopere_trail\service\subplugin_manager::TYPE_CONTENT, 'contenttype'],
            [\local_kopere_trail\service\subplugin_manager::TYPE_COMPLETION, 'completiontype'],
            [\local_kopere_trail\service\subplugin_manager::TYPE_PERSONALIZATION, 'personalizationtype'],
            [\local_kopere_trail\service\subplugin_manager::TYPE_COMPETENCY, 'competencytype'],
        ] as [$type, $field]) {
            $errors += $configuration->validate_provider($type, (string)($data[$field] ?? ''), $data);
        }
        return $errors;
    }
}
