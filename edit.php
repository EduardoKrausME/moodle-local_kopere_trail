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
 * edit.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();
$id = optional_param('id', 0, PARAM_INT);
$context = context_system::instance();
require_capability('local/kopere_trail:manage', $context);
$url = new moodle_url('/local/kopere_trail/edit.php', $id ? ['id' => $id] : []);
$pagetitle = $id ? get_string('edittrail', 'local_kopere_trail') : get_string('createtrail', 'local_kopere_trail');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

$repository = new \local_kopere_trail\repository\trail_repository();
$configuration = new \local_kopere_trail\service\configuration_service();
$editoroptions = ['maxfiles' => EDITOR_UNLIMITED_FILES, 'trusttext' => true, 'context' => $context];
$trail = null;
if ($id) {
    $trail = $configuration->prepare_trail_for_form($repository->get_trail($id));
    $trail = file_prepare_standard_editor($trail, 'summary', $editoroptions, $context, 'local_kopere_trail', 'summary', $id);
}
$form = new \local_kopere_trail\form\trail_form($url, ['currentdata' => $trail]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/kopere_trail/manage.php'));
}
if ($data = $form->get_data()) {
    $data = $configuration->build_trail_config($data);
    if (!$id) {
        $data->summary = $data->summary_editor ?? ['text' => '', 'format' => FORMAT_HTML];
        $id = $repository->save_trail($data);
    }
    $data->id = $id;
    $data = file_postupdate_standard_editor($data, 'summary', $editoroptions, $context, 'local_kopere_trail', 'summary', $id);
    $repository->save_trail($data);
    redirect(
        new moodle_url('/local/kopere_trail/manage.php'),
        get_string($trail ? 'trailupdated' : 'trailcreated', 'local_kopere_trail')
    );
}
if ($trail) {
    $form->set_data($trail);
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
