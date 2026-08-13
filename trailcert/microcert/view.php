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
 * view.php
 *
 * @package   trailcert_microcert
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');

require_login();
$trailid = required_param('trailid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$context = context_system::instance();
$canmanage = has_any_capability(['local/kopere_trail:manage', 'local/kopere_trail:viewreport'], $context);
if ((int)$USER->id !== $userid && !$canmanage) {
    throw new required_capability_exception($context, 'local/kopere_trail:view', 'nopermissions', '');
}
$repository = new \local_kopere_trail\repository\trail_repository();
$trail = $repository->get_trail($trailid);
if ((int)$USER->id === $userid) {
    (new \local_kopere_trail\service\access_service($repository))->require_access($trail, $userid, $context);
}
$progress = $DB->get_record('local_kopere_trail_prog', [
    'trailid' => $trailid,
    'userid' => $userid,
    'status' => 'completed',
], '*', MUST_EXIST);
$user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);

$url = new moodle_url('/local/kopere_trail/trailcert/microcert/view.php', ['trailid' => $trailid, 'userid' => $userid]);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('certificate', 'local_kopere_trail'));
$PAGE->set_heading(get_string('certificate', 'local_kopere_trail'));
$PAGE->requires->js_call_amd('local_kopere_trail/certificate', 'init');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_kopere_trail/certificate', [
    'student' => fullname($user),
    'trailname' => format_string($trail->name),
    'completedon' => userdate((int)$progress->timecompleted, get_string('strftimedatefullshort', 'langconfig')),
    'backurl' => (new moodle_url('/local/kopere_trail/view.php', ['id' => $trailid]))->out(false),
]);
echo $OUTPUT->footer();
