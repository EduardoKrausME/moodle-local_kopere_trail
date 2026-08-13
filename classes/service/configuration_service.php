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
 * configuration_service.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\service;

defined('MOODLE_INTERNAL') || die();

class configuration_service {
    private subplugin_manager $plugins;

    public function __construct(?subplugin_manager $plugins = null) {
        $this->plugins = $plugins ?? new subplugin_manager();
    }

    public function prepare_trail_for_form(\stdClass $trail): \stdClass {
        $config = \local_kopere_trail\json::decode($trail->config ?? null);
        $trail->certtype = (string)($config['certtype'] ?? '');
        $trail->gamificationtype = (string)($config['gamificationtype'] ?? 'progress');
        return $this->prepare_provider_configuration(
            subplugin_manager::TYPE_CERT,
            $trail->certtype,
            $trail,
            $config
        );
    }

    public function build_trail_config(\stdClass $data): \stdClass {
        $certtype = (string)($data->certtype ?? '');
        $config = $this->build_provider_configuration(subplugin_manager::TYPE_CERT, $certtype, $data);
        if ($certtype !== '') {
            $config['certtype'] = $certtype;
        }
        $gamificationtype = (string)($data->gamificationtype ?? 'progress');
        if ($gamificationtype !== '') {
            $config['gamificationtype'] = $gamificationtype;
        }
        $data->config = $config ? \local_kopere_trail\json::encode($config) : null;
        return $data;
    }

    public function prepare_step_for_form(\stdClass $step): \stdClass {
        $step = $this->prepare_provider_configuration(
            subplugin_manager::TYPE_CONTENT,
            (string)$step->contenttype,
            $step,
            \local_kopere_trail\json::decode($step->contentconfig ?? null)
        );
        $step = $this->prepare_provider_configuration(
            subplugin_manager::TYPE_COMPLETION,
            (string)$step->completiontype,
            $step,
            \local_kopere_trail\json::decode($step->completionconfig ?? null)
        );
        $step = $this->prepare_provider_configuration(
            subplugin_manager::TYPE_PERSONALIZATION,
            (string)($step->personalizationtype ?? ''),
            $step,
            \local_kopere_trail\json::decode($step->personalizationconfig ?? null)
        );
        $step = $this->prepare_provider_configuration(
            subplugin_manager::TYPE_COMPETENCY,
            (string)($step->competencytype ?? ''),
            $step,
            \local_kopere_trail\json::decode($step->competencyconfig ?? null)
        );
        return $step;
    }

    public function build_step_configs(\stdClass $data): \stdClass {
        $contentconfig = $this->build_provider_configuration(subplugin_manager::TYPE_CONTENT, (string)$data->contenttype, $data);
        $completionconfig = $this->build_provider_configuration(subplugin_manager::TYPE_COMPLETION, (string)$data->completiontype, $data);
        $personalizationconfig = $this->build_provider_configuration(subplugin_manager::TYPE_PERSONALIZATION, (string)($data->personalizationtype ?? ''), $data);
        $competencyconfig = $this->build_provider_configuration(subplugin_manager::TYPE_COMPETENCY, (string)($data->competencytype ?? ''), $data);

        $data->contentconfig = $contentconfig ? \local_kopere_trail\json::encode($contentconfig) : null;
        $data->completionconfig = $completionconfig ? \local_kopere_trail\json::encode($completionconfig) : null;
        $data->personalizationconfig = $personalizationconfig ? \local_kopere_trail\json::encode($personalizationconfig) : null;
        $data->competencyconfig = $competencyconfig ? \local_kopere_trail\json::encode($competencyconfig) : null;
        $data->prereqtype = 'previous';
        $data->prereqconfig = null;
        return $data;
    }

    public function prepare_edge_for_form(\stdClass $edge): \stdClass {
        return $this->prepare_provider_configuration(
            subplugin_manager::TYPE_PREREQ,
            (string)$edge->ruleplugin,
            $edge,
            \local_kopere_trail\json::decode($edge->ruleconfig ?? null)
        );
    }

    public function build_edge_config(\stdClass $data): \stdClass {
        $config = $this->build_provider_configuration(subplugin_manager::TYPE_PREREQ, (string)$data->ruleplugin, $data);
        $data->ruleconfig = $config ? \local_kopere_trail\json::encode($config) : null;
        return $data;
    }

    public function add_provider_fields(\MoodleQuickForm $mform, string $type, string $selectorfield, ?\stdClass $currentdata = null): void {
        foreach (array_keys($this->plugins->get_options($type)) as $name) {
            $handler = $this->plugins->get_handler($type, $name);
            if ($handler instanceof \local_kopere_trail\contract\configurable_provider) {
                $handler->add_configuration_fields($mform, $selectorfield, $currentdata);
            }
        }
    }

    public function validate_provider(string $type, string $name, array $data): array {
        $handler = $this->plugins->get_handler($type, $name);
        if (!$handler instanceof \local_kopere_trail\contract\configurable_provider) {
            return [];
        }
        return $handler->validate_configuration($data);
    }

    private function prepare_provider_configuration(string $type, string $name, \stdClass $data, array $config): \stdClass {
        $handler = $this->plugins->get_handler($type, $name);
        if (!$handler instanceof \local_kopere_trail\contract\configurable_provider) {
            return $data;
        }
        return $handler->prepare_configuration($data, $config);
    }

    private function build_provider_configuration(string $type, string $name, \stdClass $data): array {
        $handler = $this->plugins->get_handler($type, $name);
        if (!$handler instanceof \local_kopere_trail\contract\configurable_provider) {
            return [];
        }
        return $handler->build_configuration($data);
    }
}
