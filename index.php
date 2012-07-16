<?php

require_once('../../config.php');
require_once('lib.php');
require_once('searchform.php');

require_login();

$mode = optional_param('mode', 'course', PARAM_ALPHA);

$blockname = get_string('pluginname', 'block_helpdesk');

if ($mode == 'user') {
    $header = get_string('search_users', 'block_helpdesk');
    $criterion = array(
        'username' => get_string('username'),
        'idnumber' => get_string('idnumber'),
        'firstname' => get_string('firstname'),
        'lastname' => get_string('lastname')
    );
    $fields = 'id, firstname, lastname, idnumber';

    $results = function($data, &$count) use ($criterion, $mode, $fields, $DB) {
        $sql = $data ? hdesk_get_results_sql($data, $criterion) : null;
        $count = $sql ? $DB->count_records_select($mode, $sql) : 0;
        return $sql ? $DB->get_records_select($mode, $sql, null, '', $fields) : array();
    };

    $follow_link = new moodle_url('/user/view.php');
} else {
    $header = get_string('search_courses', 'block_helpdesk');
    $criterion = array('fullname' => get_string('fullname'));
    $results = function($data, &$count) {
        $fullnames = explode(' ', $data->fullname_terms);
        return $data ?
            get_courses_search($fullnames, 'fullname ASC', 0, 50, $count) :
            array();
    };
    $follow_link = 'participants.php';
}

$context = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_context($context);

$PAGE->navbar->add($blockname);
$PAGE->navbar->add($header);
$PAGE->set_title($blockname . ': ' .$header);
$PAGE->set_heading($SITE->shortname. ": " . $blockname);
$PAGE->set_url('/blocks/helpdesk/', array('mode' => $mode));

$availability = array(
    'contains' => get_string('contains', 'block_helpdesk'),
    'equal' => get_string('equal', 'block_helpdesk'),
    'starts' => get_string('starts', 'block_helpdesk'),
    'ends' => get_string('ends', 'block_helpdesk')
);

$form = new helpdesk_searchform(null, array(
    'availability' => $availability,
    'criterion' => $criterion,
    'mode' => $mode
));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/my'));
} else if ($data = $form->get_data()) {
    $return = $results($data, $count);

    if ($count) {
        $table = new html_table();
        $table->head = array(get_string('fullname'));
        if ($mode == 'user') {
            $table->head[] = get_string('idnumber');
        }
        $table->head[] = get_string('action');

        foreach ($results($data, $count) as $obj) {
            $fn = !empty($obj->fullname) ? $obj->fullname : fullname($obj);

            $help = new stdClass;
            $help->{$mode . 'id'} = $obj->id;
            $help->links = array();
            $help->context = $context;
            $help->$mode = $obj;

            events_trigger('helpdesk_' . $mode, $help);

            $url = new moodle_url($follow_link, array('id' => $obj->id));

            $attrs = (isset($obj->visible) && !$obj->visible) ?
                array('class' => 'dimmed') : array();

            $line = array(html_writer::link($url, $fn, $attrs));
            if ($mode == 'user') {
                $line[] = $obj->idnumber;
            }
            $line[] = implode(' | ', $help->links);

            $table->data[] = new html_table_row($line);
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($header);

$form->display();

if ($data) {
    if (empty($count)) {
        $OUTPUT->notification(get_string('no_results', 'block_helpdesk'));
    } else {
        echo html_writer::tag('div',
            html_writer::table($table),
            array('class' => 'box'));
    }
}

echo $OUTPUT->footer();
