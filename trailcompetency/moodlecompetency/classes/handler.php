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
 * @package   trailcompetency_moodlecompetency
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailcompetency_moodlecompetency;

/**
 * Provides the handler implementation.
 */
class handler implements \local_kopere_trail\contract\competency_provider, \local_kopere_trail\contract\configurable_provider {
    /**
     * Returns the display name.
     *
     * @return string The result.
     */
    public function get_name(): string {
        return get_string('pluginname', 'trailcompetency_moodlecompetency');
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
        $selected = (array)($currentdata->competencyids ?? []);
        $mform->addElement('autocomplete', 'competencyids', get_string('competencyids', 'local_kopere_trail'),
            \local_kopere_trail\form\configuration_fields::get_competency_options($selected), [
                'multiple' => true,
                'ajax' => 'local_kopere_trail/form_competency_selector',
            ]);
        $mform->setType('competencyids', PARAM_INT);
        $mform->hideIf('competencyids', $selectorfield, 'neq', 'moodlecompetency');
    }

    /**
     * Prepares the configuration.
     *
     * @param \stdClass $data The data.
     * @param array $config The config.
     * @return \stdClass The result.
     */
    public function prepare_configuration(\stdClass $data, array $config): \stdClass {
        $data->competencyids = array_values(array_filter(array_map('intval', $config['competencyids'] ?? [])));
        return $data;
    }
    /**
     * Builds the configuration.
     *
     * @param \stdClass $data The data.
     * @return array The result.
     */
    public function build_configuration(\stdClass $data): array {
        $ids = array_values(array_unique(array_filter(array_map('intval', (array)($data->competencyids ?? [])))));
        return $ids ? ['competencyids' => $ids] : [];
    }
    /**
     * Validates the configuration.
     *
     * @param array $data The data.
     * @return array The result.
     */
    public function validate_configuration(array $data): array {
        global $DB;
        foreach (array_filter(array_map('intval', (array)($data['competencyids'] ?? []))) as $competencyid) {
            if (!$DB->record_exists('competency', ['id' => $competencyid])) {
                return ['competencyids' => get_string('invalidcompetency', 'local_kopere_trail')];
            }
        }
        return [];
    }

    /**
     * Exports the competency data.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @return array The result.
     */
    public function export_competency_data(\stdClass $step, int $userid): array {
        global $DB;
        $config = \local_kopere_trail\json::decode($step->competencyconfig ?? null);
        $ids = array_values(array_filter(array_map('intval', $config['competencyids'] ?? [])));
        if (!$ids) {
            return [];
        }
        $records = $DB->get_records_list('competency', 'id', $ids, 'shortname ASC', 'id, shortname, idnumber');
        $items = [];
        foreach ($records as $record) {
            $items[] = [
                'id' => (int)$record->id,
                'name' => format_string($record->shortname),
                'idnumber' => s((string)$record->idnumber),
            ];
        }
        return $items;
    }
}
