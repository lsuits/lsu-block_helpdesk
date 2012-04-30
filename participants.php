<?php

require_once('../../config.php');
require_once($CFG->libdir . '/grouplib.php');
require_once('lib.php');
require_once($CFG->libdir . '/quick_template/lib.php');

/**
 * Author: Philip Cali
 */

require_login();

$id = required_param('id', PARAM_INT);
$group = optional_param('group', 0, PARAM_INT);
$roleid = optional_param('roleid', 0, PARAM_INT);

$sitecontext = get_context_instance(CONTEXT_SYSTEM);

require_capability('block/helpdesk:viewenrollments', $sitecontext);

$context = get_context_instance(CONTEXT_COURSE, $id);
$frontpagectx = get_context_instance(CONTEXT_COURSE, SITEID);

$course = $DB->get_record('course', array('id' => $id));

$blockname = get_string('pluginname', 'block_helpdesk');
$search = get_string('search_courses', 'block_helpdesk');

$PAGE->set_context($sitecontext);
$PAGE->navbar->add($search);
$PAGE->set_title($blockname . ': '. $search);
$PAGE->set_heading($blockname);
$PAGE->set_url('/blocks/helpdesk/participants.php', array(
    'id' => $id, 'group' => $group, 'roleid' => $roleid
));

echo $OUTPUT->header();

$heading = $course->fullname;

if ($group > 0) {
    $heading .= ' ' . $DB->get_field('groups', 'name', array('id' => $group));
}

echo $OUTPUT->heading($heading);

// Force visibility of all groups please.
$course->groupmode=2;
groups_print_course_menu($course, "participants.php?id=$id&amp;roleid=$roleid");

// Gett all the roles
$rolenamesurl = new moodle_url('/blocks/helpdesk/participants.php', array('id' => $id));
$roles = get_roles_used_in_context($context, true);
$rolenames = array(0 => get_string('allparticipants'));
foreach($roles as $role) {
    $rolenames[$role->id] = strip_tags(role_get_name($role, $context));
}

//By passing in a role of '0', we get every role
$users = get_role_users($roleid, $context, false, '', 'u.lastname, u.firstname',
                        null, $group);

if ($roleid > 0) {
    $a = new stdClass;
    $a->role = $rolenames[$roleid];
    $header = format_string(get_string('xuserswiththerole', 'role', $a));

    if ($group) {
        $a->group = $heading;
        $header .= ' ' . format_string(get_string('ingroup', 'role', $a));
    }
} else {
    $header = get_string('allparticipants');
}
$header .= ': '. count($users);

$template_data = array (
    'select' => $OUTPUT->single_select($rolenamesurl, 'roleid', $rolenames, $roleid, null, 'rolesform'),
    'users' => $users,
    'heading' => $OUTPUT->heading($header, 3),
    'wwwroot' => $CFG->wwwroot,
);

$template_registers = array(
    "function" => array(
        'picture' => function($params, &$smarty) use($OUTPUT, $id) {
            $params['user']->imagealt = '';
            return $OUTPUT->user_picture($params['user'],
                    array('courseid' => $id, 'alttext' => false));
        }
    )
);

quick_template::render('participants.tpl', $template_data, 'block_helpdesk', $template_registers);

echo $OUTPUT->footer();

