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
 * steps.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$trailid = required_param('trailid', PARAM_INT);
$context = context_system::instance();
require_capability('local/kopere_trail:manage', $context);

$url = new moodle_url('/local/kopere_trail/steps.php', ['trailid' => $trailid]);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('trailsteps', 'local_kopere_trail'));
$PAGE->set_heading(get_string('trailsteps', 'local_kopere_trail'));

$repository = new \local_kopere_trail\repository\trail_repository();
$trail = $repository->get_trail($trailid);
$steps = $repository->get_steps($trailid, true);
$page = new \local_kopere_trail\output\step_list_page($trail, $steps);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_kopere_trail/step_list', $page->export_for_template($OUTPUT));
echo $OUTPUT->footer();
