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
 * lib.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function local_kopere_trail_extend_navigation(global_navigation $navigation): void {
    if (!isloggedin() || isguestuser()) {
        return;
    }
    $context = context_system::instance();
    if (!has_any_capability(['local/kopere_trail:view', 'local/kopere_trail:manage'], $context)) {
        return;
    }
    $navigation->add(
        get_string('pluginname', 'local_kopere_trail'),
        new moodle_url('/local/kopere_trail/index.php'),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_kopere_trail',
        new pix_icon('i/course', '')
    );
}

function local_kopere_trail_pluginfile(
    $course,
    $cm,
    context $context,
    string $filearea,
    array $args,
    bool $forcedownload,
    array $options = []
): bool {
    global $USER;
    if ($context->contextlevel !== CONTEXT_SYSTEM || !in_array($filearea, ['summary', 'stepdescription'], true)) {
        return false;
    }
    require_login();
    $itemid = (int)array_shift($args);
    if ($itemid <= 0 || !$args) {
        return false;
    }

    $repository = new \local_kopere_trail\repository\trail_repository();
    $trail = null;
    if ($filearea === 'summary') {
        $trail = $repository->get_trail($itemid, false);
    } else {
        $step = $repository->get_step($itemid, false);
        if (!$step || empty($step->visible)) {
            if (!has_capability('local/kopere_trail:manage', $context)) {
                return false;
            }
        }
        if (!$step) {
            return false;
        }
        $trail = $repository->get_trail((int)$step->trailid, false);
        if ($trail && !has_capability('local/kopere_trail:manage', $context)) {
            $type = trim((string)($step->personalizationtype ?? ''));
            if ($type !== '') {
                $handler = (new \local_kopere_trail\service\subplugin_manager())->get_personalization_handler($type);
                if (!$handler || !$handler->should_show_step($step, (int)$USER->id)) {
                    return false;
                }
            }
        }
    }
    if (!$trail) {
        return false;
    }
    if (!(new \local_kopere_trail\service\access_service($repository))->can_access($trail, (int)$USER->id, $context)) {
        return false;
    }

    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';
    $file = get_file_storage()->get_file($context->id, 'local_kopere_trail', $filearea, $itemid, $filepath, $filename);
    if (!$file || $file->is_directory()) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
    return true;
}
