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
 * @package   trailpersonalization_rules
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailpersonalization_rules;

defined('MOODLE_INTERNAL') || die();

class handler implements \local_kopere_trail\contract\personalization_provider, \local_kopere_trail\contract\configurable_provider {
    public function get_name(): string { return get_string('pluginname', 'trailpersonalization_rules'); }

    public function add_configuration_fields(\MoodleQuickForm $mform, string $selectorfield, ?\stdClass $currentdata = null): void {
        $selected = (array)($currentdata->personalizationcohortids ?? []);
        $mform->addElement('autocomplete', 'personalizationcohortids', get_string('personalizationcohortids', 'local_kopere_trail'),
            \local_kopere_trail\form\configuration_fields::get_cohort_options($selected), [
                'multiple' => true,
                'ajax' => 'local_kopere_trail/form_cohort_selector',
            ]);
        $mform->setType('personalizationcohortids', PARAM_INT);
        $mform->hideIf('personalizationcohortids', $selectorfield, 'neq', 'rules');
        $mform->addHelpButton('personalizationcohortids', 'personalizationcohortids', 'local_kopere_trail');
    }

    public function prepare_configuration(\stdClass $data, array $config): \stdClass {
        $data->personalizationcohortids = array_values(array_filter(array_map('intval', $config['cohortids'] ?? [])));
        return $data;
    }
    public function build_configuration(\stdClass $data): array {
        $cohortids = array_values(array_unique(array_filter(array_map('intval', (array)($data->personalizationcohortids ?? [])))));
        return $cohortids ? ['cohortids' => $cohortids] : [];
    }
    public function validate_configuration(array $data): array {
        global $DB;
        foreach (array_filter(array_map('intval', (array)($data['personalizationcohortids'] ?? []))) as $cohortid) {
            if (!$DB->record_exists('cohort', ['id' => $cohortid])) {
                return ['personalizationcohortids' => get_string('invalidcohort', 'local_kopere_trail')];
            }
        }
        return [];
    }

    public function should_show_step(\stdClass $step, int $userid): bool {
        global $DB;
        $config = \local_kopere_trail\json::decode($step->personalizationconfig);
        $cohortids = array_filter(array_map('intval', $config['cohortids'] ?? []));
        if (!$cohortids) { return true; }
        [$insql, $params] = $DB->get_in_or_equal($cohortids, SQL_PARAMS_NAMED, 'cohortid');
        $params['userid'] = $userid;
        return $DB->record_exists_select('cohort_members', "userid = :userid AND cohortid {$insql}", $params);
    }
}
