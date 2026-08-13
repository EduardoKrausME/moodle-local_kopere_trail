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
 * report.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();
$trailid = required_param('trailid', PARAM_INT);
$context = context_system::instance();
require_capability('local/kopere_trail:viewreport', $context);
$url = new moodle_url('/local/kopere_trail/report.php', ['trailid' => $trailid]);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('report', 'local_kopere_trail'));
$PAGE->set_heading(get_string('report', 'local_kopere_trail'));

$trails = new \local_kopere_trail\repository\trail_repository();
$progress = new \local_kopere_trail\repository\progress_repository();
$trail = $trails->get_trail($trailid);
$page = new \local_kopere_trail\output\report_page($trail, $progress->get_report_rows($trailid));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_kopere_trail/report', $page->export_for_template($OUTPUT));
echo $OUTPUT->footer();
