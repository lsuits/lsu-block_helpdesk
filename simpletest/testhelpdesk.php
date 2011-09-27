<?php

if(!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->dirroot . '/blocks/helpdesk/lib.php');

class helpdesk_test extends UnitTestCase {
    function test_equality_translator() {
        $expected = array (
            "LIKE '%ell%'",
            "= 'sneebs'",
            "LIKE 'apple%'",
            "LIKE '%sauce'"
        );

        $actual = array (
            hdesk_translate_equality('contains', 'ell'),
            hdesk_translate_equality('equal', 'sneebs'),
            hdesk_translate_equality('starts', 'apple'),
            hdesk_translate_equality('ends', 'sauce')
        );

        foreach(range(0, 3) as $index) {
            $this->assertEqual($expected[$index], $actual[$index]); 
        }
    }

    function test_results_sql() {
        $expected = array (
            "fullname LIKE '%bo%'",
            "username = 'pcali1'",
            "lastname LIKE 'Smi%' AND firstname = 'John'",
            "email LIKE '%@lsu.edu'"
        );

        $build_string = function(array $fields, array $equalities) {
            $data = new stdClass;
            $criterion = array();
            foreach($fields as $field => $value) {
                $data->{$field . '_terms'} = $value;
                $data->{$field . '_equality'} = $equalities[$field];
                $criterion[$field] = $value;
            }
            return hdesk_get_results_sql($data, $criterion);
        };

        $actual = array (
            $build_string(array('fullname' => 'bo'), array('fullname' => 'contains')),
            $build_string(array('username' => 'pcali1'), array('username' => 'equal')),
            $build_string(array('lastname' => 'Smi', 'firstname' => 'John'),
                          array('lastname' => 'starts', 'firstname' => 'equal')),
            $build_string(array('email' => '@lsu.edu'), array('email' => 'ends'))
        );

        foreach(range(0, 3) as $index) {
            $this->assertEqual($expected[$index], $actual[$index]);
        }
    }
}
