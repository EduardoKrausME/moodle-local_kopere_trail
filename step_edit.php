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
 * step_edit.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();
$trailid = required_param('trailid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
$context = context_system::instance();
require_capability('local/kopere_trail:manage', $context);
$url = new moodle_url('/local/kopere_trail/step_edit.php', array_filter(['trailid' => $trailid, 'id' => $id ?: null]));
$pagetitle = $id ? get_string('editstep', 'local_kopere_trail') : get_string('createstep', 'local_kopere_trail');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$repository = new \local_kopere_trail\repository\trail_repository();
$repository->get_trail($trailid);
$configuration = new \local_kopere_trail\service\configuration_service();
$editoroptions = ['maxfiles' => EDITOR_UNLIMITED_FILES, 'trusttext' => true, 'context' => $context];
$step = null;
if ($id) {
    $step = $repository->get_step($id);
    if ((int)$step->trailid !== $trailid) {
        throw new moodle_exception('invalidsteptrail', 'local_kopere_trail');
    }
    $step = $configuration->prepare_step_for_form($step);
    $step = file_prepare_standard_editor($step, 'description', $editoroptions, $context, 'local_kopere_trail', 'stepdescription', $id);
}
$form = new \local_kopere_trail\form\step_form($url, ['currentdata' => $step]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/kopere_trail/steps.php', ['trailid' => $trailid]));
}
if ($data = $form->get_data()) {
    $data = $configuration->build_step_configs($data);
    if (!$id) {
        $data->description = $data->description_editor ?? ['text' => '', 'format' => FORMAT_HTML];
        $id = $repository->save_step($data);
    }
    $data->id = $id;
    $data = file_postupdate_standard_editor($data, 'description', $editoroptions, $context, 'local_kopere_trail', 'stepdescription', $id);
    $repository->save_step($data);
    redirect(new moodle_url('/local/kopere_trail/steps.php', ['trailid' => $trailid]), get_string($step ? 'stepupdated' : 'stepcreated', 'local_kopere_trail'));
}
if ($step) {
    $form->set_data($step);
} else {
    $form->set_data((object)[
        'trailid' => $trailid,
        'contenttype' => 'html',
        'completiontype' => 'manual',
        'unlockmode' => 'all',
        'visible' => 1,
    ]);
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
