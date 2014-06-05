<?php

class block_helpdesk extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_helpdesk');
    }

    function applicable_formats() {
        return array('site' => true, 'my' => true, 'course' => false);
    }

    function get_content() {
        global $CFG;

        if($this->content !== NULL) {
            return $this->content;
        }

        $context = context_system::instance();
        if(!has_capability('block/helpdesk:viewenrollments', $context)) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $search_course = get_string('search_courses', 'block_helpdesk');
        $search_users = get_string('search_users', 'block_helpdesk');

        $gen_link = function($mode) {
            return html_writer::link(
                new moodle_url('/blocks/helpdesk/', array('mode' => $mode)),
                get_string("search_{$mode}s", 'block_helpdesk')
            );
        };

        $this->content->items[] = $gen_link('course');
        $this->content->items[] = $gen_link('user');

        return $this->content;
    }
}
