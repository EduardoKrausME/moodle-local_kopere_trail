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
 * @package   trailcontent_url
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailcontent_url;

defined('MOODLE_INTERNAL') || die();

class handler implements \local_kopere_trail\contract\content_provider, \local_kopere_trail\contract\configurable_provider {
    public function get_name(): string {
        return get_string('pluginname', 'trailcontent_url');
    }

    public function get_icon(): string {
        return 'i/link';
    }

    public function add_configuration_fields(\MoodleQuickForm $mform, string $selectorfield, ?\stdClass $currentdata = null): void {
        $mform->addElement('text', 'contenturl', get_string('contenturl', 'local_kopere_trail'), ['size' => 70]);
        $mform->setType('contenturl', PARAM_URL);
        $mform->hideIf('contenturl', $selectorfield, 'neq', 'url');
        $mform->addHelpButton('contenturl', 'contenturl', 'local_kopere_trail');
    }

    public function prepare_configuration(\stdClass $data, array $config): \stdClass {
        $data->contenturl = (string)($config['url'] ?? '');
        return $data;
    }

    public function build_configuration(\stdClass $data): array {
        return ['url' => trim((string)($data->contenturl ?? ''))];
    }

    public function validate_configuration(array $data): array {
        return trim((string)($data['contenturl'] ?? '')) === ''
            ? ['contenturl' => get_string('required')]
            : [];
    }

    public function get_launch_url(\stdClass $step, int $userid): ?\moodle_url {
        $config = \local_kopere_trail\json::decode($step->contentconfig);
        $url = trim((string)($config['url'] ?? ''));
        return $url !== '' ? new \moodle_url($url) : null;
    }

    public function ensure_access(\stdClass $step, int $userid): void {
    }

    public function export_view_data(\stdClass $step, int $userid): array {
        return [];
    }
}
