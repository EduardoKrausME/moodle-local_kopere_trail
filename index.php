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
 * index.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
if (!has_any_capability(['local/kopere_trail:view', 'local/kopere_trail:manage'], $context)) {
    require_capability('local/kopere_trail:view', $context);
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/kopere_trail/index.php'));
$PAGE->set_title(get_string('myjourneys', 'local_kopere_trail'));
$PAGE->set_heading(get_string('myjourneys', 'local_kopere_trail'));

$repository = new \local_kopere_trail\repository\trail_repository();
$service = new \local_kopere_trail\service\progress_service();
$trails = $repository->get_user_trails((int)$USER->id);

foreach ($trails as $trail) {
    $service->rebuild_user_progress((int)$trail->id, (int)$USER->id);
}
$trails = $repository->get_user_trails((int)$USER->id);

$page = new \local_kopere_trail\output\index_page($trails, has_capability('local/kopere_trail:manage', $context));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_kopere_trail/index', $page->export_for_template($OUTPUT));
echo $OUTPUT->footer();
