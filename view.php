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
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$stepid = optional_param('stepid', 0, PARAM_INT);
$context = context_system::instance();
if (!has_any_capability(['local/kopere_trail:view', 'local/kopere_trail:manage'], $context)) {
    require_capability('local/kopere_trail:view', $context);
}

$url = new moodle_url('/local/kopere_trail/view.php', ['id' => $id]);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->requires->js_call_amd('local_kopere_trail/trail', 'init');
$PAGE->requires->strings_for_js(['nextlocked', 'completed', 'available', 'locked'], 'local_kopere_trail');

$repository = new \local_kopere_trail\repository\trail_repository();
$trail = $repository->get_trail($id);
(new \local_kopere_trail\service\access_service($repository))->require_access($trail, (int)$USER->id, $context);
$service = new \local_kopere_trail\service\progress_service($repository);

if ($action === 'complete' && $stepid > 0) {
    require_sesskey();
    $step = $repository->get_step($stepid);
    if ((int)$step->trailid !== $id) {
        throw new moodle_exception('invalidsteptrail', 'local_kopere_trail');
    }
    $service->mark_step_completed($stepid, (int)$USER->id);
    \local_kopere_trail\event\step_completed::create([
        'context' => $context,
        'objectid' => $stepid,
        'relateduserid' => (int)$USER->id,
    ])->trigger();
    redirect($url, get_string('stepcompleted', 'local_kopere_trail'));
}

$isenrolled = $repository->has_active_enrolment($id, (int)$USER->id);
$progress = $isenrolled
    ? $service->rebuild_user_progress($id, (int)$USER->id)
    : $service->preview_user_progress($id, (int)$USER->id);
$steps = $service->export_step_cards($id, (int)$USER->id, $progress);
$certificateurl = $isenrolled ? $service->get_certificate_url($trail, (int)$USER->id, $progress) : null;

$PAGE->set_title(format_string($trail->name));
$PAGE->set_heading(format_string($trail->name));
\local_kopere_trail\event\trail_viewed::create(['context' => $context, 'objectid' => $id])->trigger();

$page = new \local_kopere_trail\output\view_page(
    $trail,
    $progress,
    $steps,
    has_capability('local/kopere_trail:manage', $context),
    $certificateurl
);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_kopere_trail/view', $page->export_for_template($OUTPUT));
echo $OUTPUT->footer();
