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
 * enrol.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$trailid = required_param('trailid', PARAM_INT);
$context = context_system::instance();
require_capability('local/kopere_trail:enrol', $context);

$url = new moodle_url('/local/kopere_trail/enrol.php', ['trailid' => $trailid]);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('trailenrolments', 'local_kopere_trail'));
$PAGE->set_heading(get_string('trailenrolments', 'local_kopere_trail'));

$repository = new \local_kopere_trail\repository\trail_repository();
$trail = $repository->get_trail($trailid);
$form = new \local_kopere_trail\form\assignment_form($url);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/kopere_trail/manage.php'));
}

if ($data = $form->get_data()) {
    $data->instanceid = $data->assigntype === 'cohort' ? (int)$data->cohortid : (int)$data->userid;
    $repository->save_assignment($data);
    (new \local_kopere_trail\service\enrolment_service($repository))->sync_trail_assignments($trailid);
    redirect($url, get_string('enrolmentsynced', 'local_kopere_trail'));
}

$form->set_data((object)['trailid' => $trailid, 'status' => 'active']);
$page = new \local_kopere_trail\output\enrol_page($trail, $repository->get_assignments_by_trail($trailid));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_kopere_trail/enrol', $page->export_for_template($OUTPUT));
$form->display();
echo $OUTPUT->footer();
