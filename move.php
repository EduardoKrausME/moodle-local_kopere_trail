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
 * move.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();
$type = required_param('type', PARAM_ALPHA);
$id = required_param('id', PARAM_INT);
$direction = required_param('direction', PARAM_ALPHA);
if (!in_array($type, ['trail', 'step', 'edge'], true) || !in_array($direction, ['up', 'down'], true)) {
    throw new moodle_exception('invalidmove', 'local_kopere_trail');
}
$context = context_system::instance();
require_capability('local/kopere_trail:manage', $context);
$repository = new \local_kopere_trail\repository\trail_repository();

if ($type === 'trail') {
    $repository->move_trail($id, $direction);
    redirect(new moodle_url('/local/kopere_trail/manage.php'));
}
if ($type === 'step') {
    $step = $repository->get_step($id);
    $repository->move_step($id, $direction);
    redirect(new moodle_url('/local/kopere_trail/steps.php', ['trailid' => $step->trailid]));
}
$edge = $repository->get_edge($id);
$repository->move_edge($id, $direction);
redirect(new moodle_url('/local/kopere_trail/edges.php', ['trailid' => $edge->trailid]));
