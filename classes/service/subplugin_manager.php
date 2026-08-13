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
 * subplugin_manager.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\service;

defined('MOODLE_INTERNAL') || die();

class subplugin_manager {
    public const TYPE_CONTENT = 'trailcontent';
    public const TYPE_COMPLETION = 'trailcompletion';
    public const TYPE_PREREQ = 'trailprereq';
    public const TYPE_PERSONALIZATION = 'trailpersonalization';
    public const TYPE_CERT = 'trailcert';
    public const TYPE_GAMIFICATION = 'trailgamification';
    public const TYPE_COMPETENCY = 'trailcompetency';

    public function get_options(string $type, bool $includenone = false): array {
        $options = [];
        if ($includenone) {
            $options[''] = get_string('none');
        }

        foreach (\core_component::get_plugin_list($type) as $name => $path) {
            $component = $type . '_' . $name;
            $options[$name] = get_string('pluginname', $component);
        }

        return $options;
    }

    public function get_plugin_label(string $type, string $name): string {
        if ($name === '') {
            return get_string('none');
        }
        $plugins = \core_component::get_plugin_list($type);
        if (!isset($plugins[$name])) {
            return get_string('missingplugin', 'local_kopere_trail', $name);
        }
        return get_string('pluginname', $type . '_' . $name);
    }

    public function get_content_handler(string $name): ?\local_kopere_trail\contract\content_provider {
        $handler = $this->get_handler(self::TYPE_CONTENT, $name);
        return $handler instanceof \local_kopere_trail\contract\content_provider ? $handler : null;
    }

    public function get_completion_handler(string $name): ?\local_kopere_trail\contract\completion_provider {
        $handler = $this->get_handler(self::TYPE_COMPLETION, $name);
        return $handler instanceof \local_kopere_trail\contract\completion_provider ? $handler : null;
    }

    public function get_prereq_handler(string $name): ?\local_kopere_trail\contract\prereq_provider {
        $handler = $this->get_handler(self::TYPE_PREREQ, $name);
        return $handler instanceof \local_kopere_trail\contract\prereq_provider ? $handler : null;
    }

    public function get_personalization_handler(string $name): ?\local_kopere_trail\contract\personalization_provider {
        if ($name === '') {
            return null;
        }

        $handler = $this->get_handler(self::TYPE_PERSONALIZATION, $name);
        return $handler instanceof \local_kopere_trail\contract\personalization_provider ? $handler : null;
    }

    public function get_cert_handler(string $name): ?\local_kopere_trail\contract\cert_provider {
        $handler = $this->get_handler(self::TYPE_CERT, $name);
        return $handler instanceof \local_kopere_trail\contract\cert_provider ? $handler : null;
    }

    public function get_competency_handler(string $name): ?\local_kopere_trail\contract\competency_provider {
        if ($name === '') {
            return null;
        }
        $handler = $this->get_handler(self::TYPE_COMPETENCY, $name);
        return $handler instanceof \local_kopere_trail\contract\competency_provider ? $handler : null;
    }

    public function get_gamification_handler(string $name): ?\local_kopere_trail\contract\gamification_provider {
        $handler = $this->get_handler(self::TYPE_GAMIFICATION, $name);
        return $handler instanceof \local_kopere_trail\contract\gamification_provider ? $handler : null;
    }

    public function get_handler(string $type, string $name): ?object {
        if ($name === '') {
            return null;
        }

        $classname = '\\' . $type . '_' . $name . '\\handler';
        if (!class_exists($classname)) {
            return null;
        }

        return new $classname();
    }
}

