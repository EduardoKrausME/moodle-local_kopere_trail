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
 * edge_edit.php
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
$url = new moodle_url('/local/kopere_trail/edge_edit.php', array_filter(['trailid' => $trailid, 'id' => $id ?: null]));
$pagetitle = $id ? get_string('editedge', 'local_kopere_trail') : get_string('createedge', 'local_kopere_trail');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$repository = new \local_kopere_trail\repository\trail_repository();
$repository->get_trail($trailid);
$steps = $repository->get_steps($trailid, true);
$configuration = new \local_kopere_trail\service\configuration_service();
$edge = null;
if ($id) {
    $edge = $repository->get_edge($id);
    if ((int)$edge->trailid !== $trailid) {
        throw new moodle_exception('invalidedgetrail', 'local_kopere_trail');
    }
    $edge = $configuration->prepare_edge_for_form($edge);
}
$form = new \local_kopere_trail\form\edge_form($url, ['steps' => $steps, 'currentdata' => $edge]);
if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/kopere_trail/edges.php', ['trailid' => $trailid]));
}
if ($data = $form->get_data()) {
    $data = $configuration->build_edge_config($data);
    $repository->save_edge($data);
    redirect(new moodle_url('/local/kopere_trail/edges.php', ['trailid' => $trailid]), get_string($edge ? 'edgeupdated' : 'edgecreated', 'local_kopere_trail'));
}
if ($edge) {
    $form->set_data($edge);
} else {
    $form->set_data((object)['trailid' => $trailid, 'ruleplugin' => 'step']);
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
