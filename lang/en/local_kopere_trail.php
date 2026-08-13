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
 * local_kopere_trail.php
 *
 * @package   local_kopere_trail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Kopere Trail';
$string['privacy:metadata'] = 'Kopere Trail stores user enrolment, progress, and completion data for learning trails.';
$string['privacy:metadata:trailid'] = 'The trail identifier.';
$string['privacy:metadata:userid'] = 'The user identifier.';
$string['privacy:metadata:status'] = 'The record status.';
$string['privacy:metadata:enrol'] = 'The user enrolment in a trail.';
$string['privacy:metadata:enrolsource'] = 'Assignment sources that keep a user enrolment active.';
$string['privacy:metadata:progress'] = 'The consolidated user progress in a trail.';
$string['privacy:metadata:stepprogress'] = 'The user progress in a trail step.';
$string['privacy:metadata:stepid'] = 'The step identifier.';
$string['privacy:metadata:percent'] = 'The consolidated trail percentage.';
$string['privacy:metadata:xp'] = 'Personal XP accumulated in the trail.';
$string['privacy:metadata:progresspercent'] = 'The step progress percentage.';
$string['kopere_trail:view'] = 'View learning trails';
$string['kopere_trail:manage'] = 'Manage learning trails';
$string['kopere_trail:enrol'] = 'Enrol users in trails';
$string['kopere_trail:viewreport'] = 'View trail reports';
$string['myjourneys'] = 'My journey';
$string['alltrails'] = 'Learning trails';
$string['managetrails'] = 'Manage trails';
$string['createtrail'] = 'Create trail';
$string['edittrail'] = 'Edit trail';
$string['trailsteps'] = 'Trail steps';
$string['trailedges'] = 'Trail connections';
$string['createstep'] = 'Create step';
$string['editstep'] = 'Edit step';
$string['createedge'] = 'Create connection';
$string['editedge'] = 'Edit connection';
$string['report'] = 'Report';
$string['trailenrolments'] = 'Trail enrolments';
$string['name'] = 'Name';
$string['code'] = 'Code';
$string['summary'] = 'Summary';
$string['visible'] = 'Visible';
$string['startdate'] = 'Start';
$string['enddate'] = 'End';
$string['certtype'] = 'Certification';
$string['description'] = 'Description';
$string['contenttype'] = 'Content type';
$string['contenthtml'] = 'Step content';
$string['contenturl'] = 'External URL';
$string['contenturl_help'] = 'Enter the full address opened when the learner accesses this step.';
$string['contentcourseid'] = 'Moodle course';
$string['contenth5pcmid'] = 'H5P activity';
$string['completiontype'] = 'Completion type';
$string['completioncourseid'] = 'Course for automatic completion';
$string['completioncmid'] = 'Activity for automatic completion';
$string['prereqtype'] = 'Prerequisite type';
$string['personalizationtype'] = 'Personalisation';
$string['personalizationcohortids'] = 'Allowed cohorts';
$string['personalizationcohortids_help'] = 'Select one or more cohorts. The step is shown when the learner belongs '
    . 'to at least one selected cohort. With no cohorts selected, the step is '
    . 'available to everyone.';
$string['prerequisites'] = 'Prerequisites';
$string['prerequisites_edges_info'] = 'Prerequisites are configured in Trail connections, linking the rule to '
    . 'the source and destination steps.';
$string['gradeitemid'] = 'Grade item';
$string['mingrade'] = 'Minimum grade';
$string['mingrade_help'] = 'Enter the minimum grade required to unlock the destination step.';
$string['optional'] = 'Optional step';
$string['unlockmode'] = 'Unlock rule';
$string['unlockmode_all'] = 'All required previous steps';
$string['unlockmode_any'] = 'Any required previous step';
$string['points'] = 'Step XP';
$string['estimatedtime'] = 'Estimated time in minutes';
$string['savechanges'] = 'Save changes';
$string['cancel'] = 'Cancel';
$string['actions'] = 'Actions';
$string['view'] = 'View';
$string['edit'] = 'Edit';
$string['steps'] = 'Steps';
$string['reports'] = 'Reports';
$string['completed'] = 'Completed';
$string['inprogress'] = 'In progress';
$string['notstarted'] = 'Not started';
$string['locked'] = 'Locked';
$string['available'] = 'Available';
$string['optionalstep'] = 'Optional';
$string['requiredstep'] = 'Required';
$string['launchstep'] = 'Open step';
$string['markcomplete'] = 'Mark as completed';
$string['completedsteps'] = '{$a->completed} of {$a->total} steps completed';
$string['progressline'] = '{$a->completed} of {$a->total} steps completed | {$a->percent}% of the trail';
$string['nosteps'] = 'This trail does not have any steps yet.';
$string['notrails'] = 'No trails are currently available.';
$string['notrailenrolments'] = 'You are not enrolled in any trail yet.';
$string['nextlocked'] = 'Available after completing the prerequisites.';
$string['trailnotfound'] = 'Trail not found.';
$string['stepnotfound'] = 'Step not found.';
$string['trailcreated'] = 'Trail created.';
$string['trailupdated'] = 'Trail updated.';
$string['stepcreated'] = 'Step created.';
$string['stepupdated'] = 'Step updated.';
$string['edgecreated'] = 'Connection created.';
$string['edgeupdated'] = 'Connection updated.';
$string['edge_same_step'] = 'Source and destination steps must be different.';
$string['stepcompleted'] = 'Step completed.';
$string['cannotcompletestep'] = 'This step cannot be completed manually.';
$string['enrolmentsynced'] = 'Enrolments synchronised.';
$string['status'] = 'Status';
$string['student'] = 'Learner';
$string['lastupdate'] = 'Last update';
$string['currentstep'] = 'Current step';
$string['percent'] = 'Percentage';
$string['xp'] = 'XP';
$string['assignmenttype'] = 'Enrolment type';
$string['assignmenttype_user'] = 'User';
$string['assignmenttype_cohort'] = 'Cohort';
$string['assignmentuser'] = 'User';
$string['assignmentcohort'] = 'Cohort';
$string['assignmenttarget'] = 'User or cohort';
$string['selectuser'] = 'Search by user name or email';
$string['selectcohort'] = 'Search for a cohort';
$string['invaliduser'] = 'The selected user does not exist or has been deleted.';
$string['invalidcohort'] = 'The selected cohort does not exist.';
$string['active'] = 'Active';
$string['suspended'] = 'Suspended';
$string['noassignments'] = 'No enrolment assignment is configured for this trail.';
$string['noedges'] = 'No connection is configured. Without connections, the trail follows a linear order.';
$string['noreportrows'] = 'No learners were found in this trail.';
$string['task_sync_enrolments'] = 'Synchronise trail enrolments';
$string['event_trail_viewed'] = 'Trail viewed';
$string['event_step_completed'] = 'Trail step completed';
$string['trail'] = 'Trail';
$string['selecttrail'] = 'Select a trail';
$string['gotomanage'] = 'Manage trails';
$string['backtotrail'] = 'Back to trail';
$string['backtomanage'] = 'Back to management';
$string['fromstep'] = 'Source step';
$string['tostep'] = 'Destination step';
$string['subplugintype_trailcontent'] = 'Trail content';
$string['subplugintype_trailcontent_plural'] = 'Trail content plugins';
$string['subplugintype_trailcompletion'] = 'Trail completion';
$string['subplugintype_trailcompletion_plural'] = 'Trail completion plugins';
$string['subplugintype_trailprereq'] = 'Trail prerequisite';
$string['subplugintype_trailprereq_plural'] = 'Trail prerequisite plugins';
$string['subplugintype_trailpersonalization'] = 'Trail personalisation';
$string['subplugintype_trailpersonalization_plural'] = 'Trail personalisation plugins';
$string['subplugintype_trailcert'] = 'Trail certification';
$string['subplugintype_trailcert_plural'] = 'Trail certification plugins';
$string['subplugintype_trailgamification'] = 'Trail gamification';
$string['subplugintype_trailgamification_plural'] = 'Trail gamification plugins';
$string['subplugintype_trailcompetency'] = 'Trail competency';
$string['subplugintype_trailcompetency_plural'] = 'Trail competency plugins';
$string['gamificationtype'] = 'Progress and XP calculation';
$string['competencytype'] = 'Competencies';
$string['competencyids'] = 'Related competencies';
$string['competencies'] = 'Competencies';
$string['selectcourse'] = 'Search by course name';
$string['selectactivity'] = 'Search by activity or course name';
$string['selectgradeitem'] = 'Search by grade item or course';
$string['endbeforestart'] = 'The end date cannot be earlier than the start date.';
$string['nonnegativevalue'] = 'Enter a value greater than or equal to zero.';
$string['removeduser'] = 'Removed user';
$string['removedcohort'] = 'Removed cohort';
$string['removedstep'] = 'Removed step';
$string['unnamedgradeitem'] = 'Unnamed grade item';
$string['moveup'] = 'Move up';
$string['movedown'] = 'Move down';
$string['invalidmove'] = 'Invalid move.';
$string['invalidsteptrail'] = 'The specified step does not belong to this trail.';
$string['invalidedgetrail'] = 'The specified connection does not belong to this trail.';
$string['task_refresh_progress'] = 'Refresh trail progress';
$string['certificate'] = 'Certificate';
$string['microcertificate'] = 'Micro-certificate';
$string['certificate_title'] = 'Certificate of completion';
$string['certificate_student_intro'] = 'This certifies that';
$string['certificate_completed_intro'] = 'completed the learning trail';
$string['certificate_completed_on'] = 'Completed on';
$string['printcertificate'] = 'Print certificate';
$string['viewcertificate'] = 'View certificate';

$string['missingplugin'] = 'Unavailable subplugin: {$a}';
$string['invalidcourse'] = 'The selected course no longer exists.';
$string['invalidactivity'] = 'The selected activity no longer exists or is not of the expected type.';
$string['invalidgradeitem'] = 'The selected grade item no longer exists.';
$string['invalidcompetency'] = 'One of the selected competencies no longer exists.';
$string['privacy:metadata:assignment'] = 'Stores direct user or cohort assignments to learning trails.';
$string['privacy:metadata:assignmenttype'] = 'Whether the assignment targets a user or a cohort.';
$string['privacy:metadata:assignmenttarget'] = 'The identifier of the user or cohort targeted by the assignment.';
$string['privacy:metadata:event'] = 'Stores audit events generated by trail progress.';
$string['privacy:metadata:eventname'] = 'The type of progress event recorded.';
$string['privacy:metadata:eventdetails'] = 'Technical details associated with the progress event.';
