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
 * complete_step.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

class complete_step extends \external_api {
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'stepid' => new \external_value(PARAM_INT, 'Trail step id'),
        ]);
    }

    public static function execute(int $stepid): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), ['stepid' => $stepid]);
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/kopere_trail:view', $context);
        require_sesskey();

        $repository = new \local_kopere_trail\repository\trail_repository();
        $step = $repository->get_step((int)$params['stepid']);
        $trail = $repository->get_trail((int)$step->trailid);
        (new \local_kopere_trail\service\access_service($repository))->require_access($trail, (int)$USER->id, $context);

        $service = new \local_kopere_trail\service\progress_service($repository);
        $progress = $service->mark_step_completed((int)$params['stepid'], (int)$USER->id);
        \local_kopere_trail\event\step_completed::create([
            'context' => $context,
            'objectid' => (int)$params['stepid'],
            'relateduserid' => (int)$USER->id,
        ])->trigger();

        return [
            'completed' => true,
            'percent' => (float)$progress->percent,
        ];
    }

    public static function execute_returns(): \external_single_structure {
        return new \external_single_structure([
            'completed' => new \external_value(PARAM_BOOL, 'Completion status'),
            'percent' => new \external_value(PARAM_FLOAT, 'Trail progress percent'),
        ]);
    }
}

