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

/**
 * Provides the handler implementation.
 */
class handler implements \local_kopere_trail\contract\configurable_provider, \local_kopere_trail\contract\content_provider {
    /**
     * Returns the display name.
     *
     * @return string The result.
     */
    public function get_name(): string {
        return get_string('pluginname', 'trailcontent_url');
    }

    /**
     * Returns the icon.
     *
     * @return string The result.
     */
    public function get_icon(): string {
        return 'i/link';
    }

    /**
     * Handles add configuration fields.
     *
     * @param \MoodleQuickForm $mform The mform.
     * @param string $selectorfield The selectorfield.
     * @param \stdClass|null $currentdata The currentdata.
     * @return void The result.
     */
    public function add_configuration_fields(\MoodleQuickForm $mform, string $selectorfield, ?\stdClass $currentdata = null): void {
        $mform->addElement('text', 'contenturl', get_string('contenturl', 'local_kopere_trail'), ['size' => 70]);
        $mform->setType('contenturl', PARAM_URL);
        $mform->hideIf('contenturl', $selectorfield, 'neq', 'url');
        $mform->addHelpButton('contenturl', 'contenturl', 'local_kopere_trail');
    }

    /**
     * Prepares the configuration.
     *
     * @param \stdClass $data The data.
     * @param array $config The config.
     * @return \stdClass The result.
     */
    public function prepare_configuration(\stdClass $data, array $config): \stdClass {
        $data->contenturl = (string)($config['url'] ?? '');
        return $data;
    }

    /**
     * Builds the configuration.
     *
     * @param \stdClass $data The data.
     * @return array The result.
     */
    public function build_configuration(\stdClass $data): array {
        return ['url' => trim((string)($data->contenturl ?? ''))];
    }

    /**
     * Validates the configuration.
     *
     * @param array $data The data.
     * @return array The result.
     */
    public function validate_configuration(array $data): array {
        return trim((string)($data['contenturl'] ?? '')) === ''
            ? ['contenturl' => get_string('required')]
            : [];
    }

    /**
     * Returns the launch url.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @return \moodle_url|null The result.
     */
    public function get_launch_url(\stdClass $step, int $userid): ?\moodle_url {
        $config = \local_kopere_trail\json::decode($step->contentconfig);
        $url = trim((string)($config['url'] ?? ''));
        return $url !== '' ? new \moodle_url($url) : null;
    }

    /**
     * Handles ensure access.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @return void The result.
     */
    public function ensure_access(\stdClass $step, int $userid): void {
    }

    /**
     * Exports the view data.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @return array The result.
     */
    public function export_view_data(\stdClass $step, int $userid): array {
        return [];
    }
}
