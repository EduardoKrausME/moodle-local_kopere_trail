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
 * @package   trailcontent_h5p
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailcontent_h5p;

defined('MOODLE_INTERNAL') || die();

class handler implements \local_kopere_trail\contract\content_provider, \local_kopere_trail\contract\configurable_provider {
    public function get_name(): string {
        return get_string('pluginname', 'trailcontent_h5p');
    }

    public function get_icon(): string {
        return 'i/contentbank';
    }

    public function add_configuration_fields(\MoodleQuickForm $mform, string $selectorfield, ?\stdClass $currentdata = null): void {
        $selected = (int)($currentdata->contenth5pcmid ?? 0);
        $mform->addElement('autocomplete', 'contenth5pcmid', get_string('contenth5pcmid', 'local_kopere_trail'),
            \local_kopere_trail\form\configuration_fields::get_activity_options($selected), [
                'multiple' => false,
                'ajax' => 'local_kopere_trail/form_h5p_selector',
                'noselectionstring' => get_string('selectactivity', 'local_kopere_trail'),
            ]);
        $mform->setType('contenth5pcmid', PARAM_INT);
        $mform->hideIf('contenth5pcmid', $selectorfield, 'neq', 'h5p');
    }

    public function prepare_configuration(\stdClass $data, array $config): \stdClass {
        $data->contenth5pcmid = (int)($config['cmid'] ?? 0);
        return $data;
    }

    public function build_configuration(\stdClass $data): array {
        return ['cmid' => (int)($data->contenth5pcmid ?? 0)];
    }

    public function validate_configuration(array $data): array {
        $cmid = (int)($data['contenth5pcmid'] ?? 0);
        if ($cmid <= 0) {
            return ['contenth5pcmid' => get_string('required')];
        }
        if (!get_coursemodule_from_id('h5pactivity', $cmid, 0, false, IGNORE_MISSING)) {
            return ['contenth5pcmid' => get_string('invalidactivity', 'local_kopere_trail')];
        }
        return [];
    }

    public function get_launch_url(\stdClass $step, int $userid): ?\moodle_url {
        $config = \local_kopere_trail\json::decode($step->contentconfig);
        $cmid = (int)($config['cmid'] ?? 0);
        return $cmid > 0 ? new \moodle_url('/mod/h5pactivity/view.php', ['id' => $cmid]) : null;
    }

    public function ensure_access(\stdClass $step, int $userid): void {
        $config = \local_kopere_trail\json::decode($step->contentconfig);
        $cmid = (int)($config['cmid'] ?? 0);
        if ($cmid <= 0) {
            return;
        }
        $cm = get_coursemodule_from_id('h5pactivity', $cmid, 0, false, IGNORE_MISSING);
        if ($cm) {
            \local_kopere_trail\service\course_access_helper::ensure_course_enrolment((int)$cm->course, $userid);
        }
    }

    public function export_view_data(\stdClass $step, int $userid): array {
        return [];
    }
}
