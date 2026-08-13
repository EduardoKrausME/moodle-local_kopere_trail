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
 * services.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_kopere_trail_complete_step' => [
        'classname' => 'local_kopere_trail\\external\\complete_step',
        'methodname' => 'execute',
        'description' => 'Mark a trail step as completed when the configured completion plugin allows it.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/kopere_trail:view',
    ],
    'local_kopere_trail_search_cohorts' => [
        'classname' => 'local_kopere_trail\\external\\search_cohorts',
        'methodname' => 'execute',
        'description' => 'Search cohorts for trail forms.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_kopere_trail_search_activities' => [
        'classname' => 'local_kopere_trail\\external\\search_activities',
        'methodname' => 'execute',
        'description' => 'Search activities for trail forms.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'local/kopere_trail:manage',
    ],
    'local_kopere_trail_search_grade_items' => [
        'classname' => 'local_kopere_trail\\external\\search_grade_items',
        'methodname' => 'execute',
        'description' => 'Search grade items for trail forms.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'local/kopere_trail:manage',
    ],
    'local_kopere_trail_search_competencies' => [
        'classname' => 'local_kopere_trail\\external\\search_competencies',
        'methodname' => 'execute',
        'description' => 'Search competencies for trail forms.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'local/kopere_trail:manage',
    ],
];
