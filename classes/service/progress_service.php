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
 * progress_service.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_kopere_trail\service;

/**
 * Provides the progress service implementation.
 */
class progress_service {
    /**
     * Trails.
     *
     * @var \local_kopere_trail\repository\trail_repository
     */
    private \local_kopere_trail\repository\trail_repository $trails;
    /**
     * Progress.
     *
     * @var \local_kopere_trail\repository\progress_repository
     */
    private \local_kopere_trail\repository\progress_repository $progress;
    /**
     * Plugins.
     *
     * @var subplugin_manager
     */
    private subplugin_manager $plugins;
    /**
     * Enrolments.
     *
     * @var enrolment_service
     */
    private enrolment_service $enrolments;

    /**
     * Creates a new instance.
     *
     * @param \local_kopere_trail\repository\trail_repository|null $trails The trails.
     * @param \local_kopere_trail\repository\progress_repository|null $progress The progress.
     * @param subplugin_manager|null $plugins The plugins.
     * @param enrolment_service|null $enrolments The enrolments.
     */
    public function __construct(
        ?\local_kopere_trail\repository\trail_repository $trails = null,
        ?\local_kopere_trail\repository\progress_repository $progress = null,
        ?subplugin_manager $plugins = null,
        ?enrolment_service $enrolments = null
    ) {
        $this->trails = $trails ?? new \local_kopere_trail\repository\trail_repository();
        $this->progress = $progress ?? new \local_kopere_trail\repository\progress_repository();
        $this->plugins = $plugins ?? new subplugin_manager();
        $this->enrolments = $enrolments ?? new enrolment_service($this->trails, $this->plugins);
    }

    /**
     * Handles rebuild user progress.
     *
     * @param int $trailid The trailid.
     * @param int $userid The userid.
     * @return \stdClass The result.
     */
    public function rebuild_user_progress(int $trailid, int $userid): \stdClass {
        $this->assert_enrolled($trailid, $userid);
        return $this->calculate_progress($trailid, $userid, true);
    }

    /**
     * Handles preview user progress.
     *
     * @param int $trailid The trailid.
     * @param int $userid The userid.
     * @return \stdClass The result.
     */
    public function preview_user_progress(int $trailid, int $userid): \stdClass {
        return $this->calculate_progress($trailid, $userid, false);
    }

    /**
     * Handles calculate progress.
     *
     * @param int $trailid The trailid.
     * @param int $userid The userid.
     * @param bool $persist The persist.
     * @return \stdClass The result.
     */
    private function calculate_progress(int $trailid, int $userid, bool $persist): \stdClass {
        $trail = $this->trails->get_trail($trailid);
        $steps = $this->get_personalized_steps($trailid, $userid);
        $storedstates = $this->progress->get_step_progress_by_trail($trailid, $userid);
        $stepstates = [];

        foreach ($steps as $step) {
            $stepstates[$step->id] = $this->sync_completion_state($step, $userid, $storedstates[$step->id] ?? null);
        }
        $stepstates = $this->apply_availability($trailid, $steps, $stepstates, $userid);

        if ($persist) {
            foreach ($steps as $step) {
                $this->save_step_state($step, $userid, $stepstates[$step->id]);
            }
        }

        $requiredsteps = array_values(array_filter($steps, static fn(\stdClass $step) : bool => empty($step->optional)));
        $total = count($requiredsteps);
        $completed = 0;
        $currentstepid = 0;
        foreach ($requiredsteps as $step) {
            $state = $stepstates[$step->id];
            if (!empty($state['completed'])) {
                $completed++;
                continue;
            }
            if ($currentstepid === 0 && !empty($state['available'])) {
                $currentstepid = (int)$step->id;
            }
        }

        $percent = $total > 0 ? round(($completed / $total) * 100, 2) : 0.0;
        $status = $total > 0 && $completed >= $total ? 'completed' : ($completed > 0 ? 'inprogress' : 'notstarted');
        $trailconfig = \local_kopere_trail\json::decode($trail->config ?? null);
        $gamificationtype = (string)($trailconfig['gamificationtype'] ?? 'progress');
        $gamification = $this->plugins->get_gamification_handler($gamificationtype);
        $xp = $gamification ? $gamification->calculate_xp($steps, $stepstates, $userid) : 0;

        $existing = $this->progress->get_trail_progress($trailid, $userid);
        $record = $existing ?: (object)[
            'trailid' => $trailid,
            'userid' => $userid,
            'timecompleted' => 0,
        ];
        $record->completedsteps = $completed;
        $record->totalsteps = $total;
        $record->percent = $percent;
        $record->status = $status;
        $record->currentstepid = $currentstepid;
        $record->xp = $xp;
        if ($status === 'completed' && empty($record->timecompleted)) {
            $record->timecompleted = time();
        } else if ($status !== 'completed') {
            $record->timecompleted = 0;
        }

        if ($persist) {
            $record = $this->progress->save_trail_progress($record);
        } else {
            $record->id = (int)($record->id ?? 0);
            $record->timemodified = (int)($record->timemodified ?? time());
        }
        $record->stepstates = $stepstates;

        if ($persist) {
            $this->enrolments->ensure_access_for_available_steps($steps, $stepstates, $userid);
        }
        return $record;
    }

    /**
     * Handles mark step completed.
     *
     * @param int $stepid The stepid.
     * @param int $userid The userid.
     * @return \stdClass The result.
     */
    public function mark_step_completed(int $stepid, int $userid): \stdClass {
        $step = $this->trails->get_step($stepid);
        $this->assert_enrolled((int)$step->trailid, $userid);
        $progress = $this->rebuild_user_progress((int)$step->trailid, $userid);
        $state = $progress->stepstates[$stepid] ?? null;
        if (!$state || empty($state['available'])) {
            throw new \moodle_exception('nextlocked', 'local_kopere_trail');
        }
        $handler = $this->plugins->get_completion_handler($step->completiontype);
        if (!$handler || !$handler->can_complete_manually($step, $userid)) {
            throw new \moodle_exception('cannotcompletestep', 'local_kopere_trail');
        }
        $record = $this->progress->get_step_progress($stepid, $userid) ?: (object)[
            'trailid' => (int)$step->trailid,
            'stepid' => $stepid,
            'userid' => $userid,
            'timeavailable' => time(),
            'timestarted' => time(),
        ];
        $record->status = 'completed';
        $record->completionstate = 1;
        $record->progresspercent = 100;
        $record->timecompleted = time();
        $record->source = $step->completiontype;
        $record->details = \local_kopere_trail\json::encode(['manual' => true]);
        $this->progress->save_step_progress($record);
        $this->progress->add_event(
            (int)$step->trailid,
            $stepid,
            $userid,
            'step_completed',
            ['completiontype' => $step->completiontype]
        );
        return $this->rebuild_user_progress((int)$step->trailid, $userid);
    }

    /**
     * Exports the step cards.
     *
     * @param int $trailid The trailid.
     * @param int $userid The userid.
     * @param \stdClass|null $trailprogress The trailprogress.
     * @return array The result.
     */
    public function export_step_cards(int $trailid, int $userid, ?\stdClass $trailprogress = null): array {
        $trailprogress = $trailprogress ?? $this->rebuild_user_progress($trailid, $userid);
        $steps = $this->get_personalized_steps($trailid, $userid);
        $cards = [];
        $context = \context_system::instance();

        $isenrolled = $this->trails->has_active_enrolment($trailid, $userid);
        $trail = $this->trails->get_trail($trailid);
        $trailconfig = \local_kopere_trail\json::decode($trail->config ?? null);
        $gamificationtype = (string)($trailconfig['gamificationtype'] ?? 'progress');
        $hasgamification = $this->plugins->get_gamification_handler($gamificationtype) !== null;
        foreach ($steps as $step) {
            $state = $trailprogress->stepstates[$step->id] ?? [];
            $content = $this->plugins->get_content_handler($step->contenttype);
            $launchurl = $content ? $content->get_launch_url($step, $userid) : null;
            $description = file_rewrite_pluginfile_urls(
                (string)$step->description,
                'pluginfile.php',
                $context->id,
                'local_kopere_trail',
                'stepdescription',
                (int)$step->id
            );
            $description = format_text($description, $step->descriptionformat, ['context' => $context, 'overflowdiv' => true]);
            $competencies = [];
            $competency = $this->plugins->get_competency_handler((string)($step->competencytype ?? ''));
            if ($competency) {
                $competencies = $competency->export_competency_data($step, $userid);
            }
            $cards[] = [
                'id' => (int)$step->id,
                'name' => format_string($step->name),
                'description' => $description,
                'contenttype' => $this->plugins->get_plugin_label(subplugin_manager::TYPE_CONTENT, (string)$step->contenttype),
                'completiontype' => $this->plugins->get_plugin_label(
                    subplugin_manager::TYPE_COMPLETION,
                    (string)$step->completiontype
                ),
                'optional' => !empty($step->optional),
                'required' => empty($step->optional),
                'available' => !empty($state['available']),
                'completed' => !empty($state['completed']),
                'locked' => empty($state['available']) && empty($state['completed']),
                'statuslabel' => $this->get_status_label($state),
                'progresspercent' => (float)($state['progresspercent'] ?? 0),
                'points' => (int)$step->points,
                'showpoints' => $hasgamification && (int)$step->points > 0,
                'estimatedtime' => (int)$step->estimatedtime,
                'launchurl' => $launchurl ? $launchurl->out(false) : null,
                'canlaunch' => $launchurl && !empty($state['available']),
                'cancomplete' => $isenrolled
                    && !empty($state['available'])
                    && empty($state['completed'])
                    && $this->can_complete_manually($step, $userid),
                'completeurl' => (new \moodle_url('/local/kopere_trail/view.php', [
                    'id' => $trailid,
                    'action' => 'complete',
                    'stepid' => $step->id,
                    'sesskey' => sesskey(),
                ]))->out(false),
                'inline' => $content ? $content->export_view_data($step, $userid) : [],
                'competencies' => array_values($competencies),
                'hascompetencies' => !empty($competencies),
            ];
        }
        return $cards;
    }

    /**
     * Returns the certificate url.
     *
     * @param \stdClass $trail The trail.
     * @param int $userid The userid.
     * @param \stdClass $progress The progress.
     * @return \moodle_url|null The result.
     */
    public function get_certificate_url(\stdClass $trail, int $userid, \stdClass $progress): ?\moodle_url {
        if (($progress->status ?? '') !== 'completed') {
            return null;
        }
        $config = \local_kopere_trail\json::decode($trail->config ?? null);
        $type = (string)($config['certtype'] ?? '');
        $handler = $this->plugins->get_cert_handler($type);
        return $handler ? $handler->get_certificate_url($trail, $userid) : null;
    }

    /**
     * Handles refresh active progress.
     *
     * @return int The result.
     */
    public function refresh_active_progress(): int {
        $count = 0;
        $access = new access_service($this->trails);
        $trailcache = [];
        foreach ($this->trails->get_active_enrolments() as $enrolment) {
            try {
                $trailid = (int)$enrolment->trailid;
                if (!array_key_exists($trailid, $trailcache)) {
                    $trailcache[$trailid] = $this->trails->get_trail($trailid, false);
                }
                $trail = $trailcache[$trailid];
                if (!$trail || !$access->is_open($trail)) {
                    continue;
                }
                $this->rebuild_user_progress($trailid, (int)$enrolment->userid);
                $count++;
            } catch (\Throwable $e) {
                mtrace('Kopere Trail progress refresh failed for enrolment ' . (int)$enrolment->id . ': ' . $e->getMessage());
            }
        }
        return $count;
    }

    /**
     * Handles sync completion state.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @param \stdClass|null $stored The stored.
     * @return array The result.
     */
    private function sync_completion_state(\stdClass $step, int $userid, ?\stdClass $stored): array {
        $handler = $this->plugins->get_completion_handler($step->completiontype);
        $native = $handler ? $handler->get_completion($step, $userid) : [
            'completed' => false,
            'progresspercent' => 0,
            'source' => $step->completiontype,
            'details' => [],
        ];
        $alreadycompleted = $stored && (int)$stored->completionstate === 1;
        $completed = $alreadycompleted || !empty($native['completed']);
        $progresspercent = max((float)($stored->progresspercent ?? 0), (float)($native['progresspercent'] ?? 0));
        if ($completed) {
            $progresspercent = 100;
        }
        $storedtime = (int)($stored->timecompleted ?? 0);
        return [
            'completed' => $completed,
            'available' => false,
            'status' => $completed ? 'completed' : 'locked',
            'progresspercent' => $progresspercent,
            'source' => $native['source'] ?? $step->completiontype,
            'details' => $native['details'] ?? [],
            'timecompleted' => $completed ? ($storedtime > 0 ? $storedtime : time()) : 0,
        ];
    }

    /**
     * Handles apply availability.
     *
     * @param int $trailid The trailid.
     * @param array $steps The steps.
     * @param array $stepstates The stepstates.
     * @param int $userid The userid.
     * @return array The result.
     */
    private function apply_availability(int $trailid, array $steps, array $stepstates, int $userid): array {
        $edges = $this->trails->get_edges($trailid);
        if (!$edges) {
            return $this->apply_linear_availability($steps, $stepstates);
        }
        $stepsbyid = [];
        foreach ($steps as $step) {
            $stepsbyid[$step->id] = $step;
        }
        $incoming = [];
        foreach ($edges as $edge) {
            if (!isset($stepsbyid[$edge->fromstepid]) || !isset($stepsbyid[$edge->tostepid])) {
                continue;
            }
            $incoming[$edge->tostepid][] = $edge;
        }
        foreach ($steps as $step) {
            if (!empty($stepstates[$step->id]['completed'])) {
                $stepstates[$step->id]['available'] = true;
                $stepstates[$step->id]['status'] = 'completed';
                continue;
            }
            $stepincoming = $incoming[$step->id] ?? [];
            if (!$stepincoming) {
                $stepstates[$step->id]['available'] = true;
                $stepstates[$step->id]['status'] = 'available';
                continue;
            }
            $matches = 0;
            foreach ($stepincoming as $edge) {
                $fromstep = $stepsbyid[$edge->fromstepid];
                if (empty($stepstates[$edge->fromstepid]['completed'])) {
                    continue;
                }
                $handler = $this->plugins->get_prereq_handler($edge->ruleplugin);
                if ($handler && $handler->is_available($edge, $fromstep, $step, $userid)) {
                    $matches++;
                }
            }
            $required = count($stepincoming);
            $available = $step->unlockmode === 'any' ? $matches > 0 : $matches >= $required;
            $stepstates[$step->id]['available'] = $available;
            $stepstates[$step->id]['status'] = $available ? 'available' : 'locked';
        }
        return $stepstates;
    }

    /**
     * Handles apply linear availability.
     *
     * @param array $steps The steps.
     * @param array $stepstates The stepstates.
     * @return array The result.
     */
    private function apply_linear_availability(array $steps, array $stepstates): array {
        $lastrequired = null;
        foreach ($steps as $step) {
            if (!empty($stepstates[$step->id]['completed'])) {
                $stepstates[$step->id]['available'] = true;
                $stepstates[$step->id]['status'] = 'completed';
                if (empty($step->optional)) {
                    $lastrequired = $step;
                }
                continue;
            }
            $available = $lastrequired === null || !empty($stepstates[$lastrequired->id]['completed']);
            $stepstates[$step->id]['available'] = $available;
            $stepstates[$step->id]['status'] = $available ? 'available' : 'locked';
            if (empty($step->optional) && $available) {
                $lastrequired = $step;
            }
        }
        return $stepstates;
    }

    /**
     * Saves the step state.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @param array $state The state.
     * @return void The result.
     */
    private function save_step_state(\stdClass $step, int $userid, array $state): void {
        $record = $this->progress->get_step_progress((int)$step->id, $userid) ?: (object)[
            'trailid' => (int)$step->trailid,
            'stepid' => (int)$step->id,
            'userid' => $userid,
            'timeavailable' => 0,
            'timestarted' => 0,
            'timecompleted' => 0,
        ];
        if (!empty($state['available']) && empty($record->timeavailable)) {
            $record->timeavailable = time();
        }
        if (!empty($state['completed']) && empty($record->timecompleted)) {
            $record->timecompleted = $state['timecompleted'] ?: time();
        }
        $record->status = $state['status'];
        $record->completionstate = !empty($state['completed']) ? 1 : 0;
        $record->progresspercent = (float)$state['progresspercent'];
        $record->source = $state['source'] ?? $step->completiontype;
        $record->details = \local_kopere_trail\json::encode($state['details'] ?? []);
        $this->progress->save_step_progress($record);
    }

    /**
     * Returns the personalized steps.
     *
     * @param int $trailid The trailid.
     * @param int $userid The userid.
     * @return array The result.
     */
    private function get_personalized_steps(int $trailid, int $userid): array {
        $steps = $this->trails->get_steps($trailid, false);
        return array_values(array_filter($steps, function(\stdClass $step) use ($userid): bool {
            $type = trim((string)($step->personalizationtype ?? ''));
            if ($type === '') {
                return true;
            }
            $handler = $this->plugins->get_personalization_handler($type);
            return $handler ? $handler->should_show_step($step, $userid) : false;
        }));
    }

    /**
     * Checks whether complete manually.
     *
     * @param \stdClass $step The step.
     * @param int $userid The userid.
     * @return bool The result.
     */
    private function can_complete_manually(\stdClass $step, int $userid): bool {
        $handler = $this->plugins->get_completion_handler($step->completiontype);
        return $handler && $handler->can_complete_manually($step, $userid);
    }

    /**
     * Handles assert enrolled.
     *
     * @param int $trailid The trailid.
     * @param int $userid The userid.
     * @return void The result.
     */
    private function assert_enrolled(int $trailid, int $userid): void {
        if (!$this->trails->has_active_enrolment($trailid, $userid)) {
            throw new \required_capability_exception(\context_system::instance(), 'local/kopere_trail:view', 'nopermissions', '');
        }
    }

    /**
     * Returns the status label.
     *
     * @param array $state The state.
     * @return string The result.
     */
    private function get_status_label(array $state): string {
        if (!empty($state['completed'])) {
            return get_string('completed', 'local_kopere_trail');
        }
        if (!empty($state['available'])) {
            return get_string('available', 'local_kopere_trail');
        }
        return get_string('locked', 'local_kopere_trail');
    }
}
