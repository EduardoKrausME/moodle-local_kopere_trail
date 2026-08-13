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
 * @package   trailcontent_html
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailcontent_html;

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
        return get_string('pluginname', 'trailcontent_html');
    }

    /**
     * Returns the icon.
     *
     * @return string The result.
     */
    public function get_icon(): string {
        return 'i/marker';
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
        $mform->addElement('editor', 'contenthtml', get_string('contenthtml', 'local_kopere_trail'), null, [
            'maxfiles' => 0,
            'trusttext' => true,
            'context' => \context_system::instance(),
        ]);
        $mform->setType('contenthtml', PARAM_RAW);
        $mform->hideIf('contenthtml', $selectorfield, 'neq', 'html');
    }

    /**
     * Prepares the configuration.
     *
     * @param \stdClass $data The data.
     * @param array $config The config.
     * @return \stdClass The result.
     */
    public function prepare_configuration(\stdClass $data, array $config): \stdClass {
        $data->contenthtml = [
            'text' => (string)($config['html'] ?? ''),
            'format' => (int)($config['format'] ?? FORMAT_HTML),
        ];
        return $data;
    }

    /**
     * Builds the configuration.
     *
     * @param \stdClass $data The data.
     * @return array The result.
     */
    public function build_configuration(\stdClass $data): array {
        $value = $data->contenthtml ?? [];
        return [
            'html' => (string)($value['text'] ?? ''),
            'format' => (int)($value['format'] ?? FORMAT_HTML),
        ];
    }

    /**
     * Validates the configuration.
     *
     * @param array $data The data.
     * @return array The result.
     */
    public function validate_configuration(array $data): array {
        $value = $data['contenthtml'] ?? [];
        return trim((string)($value['text'] ?? '')) === ''
            ? ['contenthtml' => get_string('required')]
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
        return null;
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
        $config = \local_kopere_trail\json::decode($step->contentconfig);
        $html = (string)($config['html'] ?? '');
        if ($html === '') {
            return [];
        }

        return [
            'html' => format_text($html, (int)($config['format'] ?? FORMAT_HTML), [
                'context' => \context_system::instance(),
                'trusted' => true,
            ]),
        ];
    }
}
