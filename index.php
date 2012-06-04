<?php

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir. '/quick_template/lib.php');

require_login();

$mode = optional_param('mode', 'course', PARAM_ALPHA);

$blockname = get_string('pluginname', 'block_helpdesk');

$data = data_submitted();

if ($mode == 'user') {
    $header = get_string('search_users', 'block_helpdesk');
    $criterion = array('username' => get_string('username'),
                       'firstname' => get_string('firstname'),
                       'lastname' => get_string('lastname'));
    $fields = 'id, firstname, lastname';

    $sql = $data ? hdesk_get_results_sql($data, $criterion) : null;
    $results = $sql ? $DB->get_records_select($mode, $sql, null, '', $fields) : array();
    $follow_link = new moodle_url('/user/view.php');
} else {
    $header = get_string('search_courses', 'block_helpdesk');
    $criterion = array('fullname' => get_string('fullname'));
    $results = $data ?
        get_courses_search(explode(' ', $data->fullname_terms), 'fullname ASC', 0, 50, $count) :
        array();
    $follow_link = 'participants.php';
}

$context = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_context($context);

$PAGE->navbar->add($header);
$PAGE->set_title($blockname . ': ' .$header);
$PAGE->set_heading($SITE->shortname. ": " . $blockname);
$PAGE->set_url('/blocks/helpdesk/', array('mode' => $mode));

echo $OUTPUT->header();
echo $OUTPUT->heading($header);

$availability = array(
    'contains' => get_string('contains', 'block_helpdesk'),
    'equal' => get_string('equal', 'block_helpdesk'),
    'starts' => get_string('starts', 'block_helpdesk'),
    'ends' => get_string('ends', 'block_helpdesk')
);

$template_data = array(
    'availability' => $availability,
    'criterion' => $criterion,
    'mode' => $mode,
    'data' => $data,
    'results' => $results,
    'follow_link' => $follow_link,
);

$registers = array(
    'function' => array(
        'fullname' => function($params, &$smarty) {
            extract($params);
            return !empty($obj->fullname) ? $obj->fullname : fullname($obj);
        },
        'helplinks' => function($params, &$smarty) use ($mode, $context) {
            extract($params);
            $help = new stdClass;
            $help->{$mode . 'id'} = $obj->id;
            $help->links = array();
            $help->context = $context;
            $help->$mode = $obj;

            events_trigger('helpdesk_' . $mode, $help);
            return implode (' | ', $help->links);
        }
    )
);

quick_template::render("helpdesk_index.tpl", $template_data, 'block_helpdesk', $registers);

echo $OUTPUT->footer();

