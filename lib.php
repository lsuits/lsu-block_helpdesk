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
 *
 * @package    block_helpdesk
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function hdesk_get_results_sql($data, $criterion) {
    global $DB;

    $keys = array_keys($criterion);
    $use_only = array_filter($keys, function($value) use($data) {
        return !empty($data->{$value . '_terms'});
    });

    // Generate sql from key submission
    $sql = implode(" AND ", array_map(function($k) use ($data) {
        $term = $data->{$k . '_terms'};
        $equality = $data->{$k . '_equality'};
        return $k . ' '. hdesk_translate_equality($equality, $term);
    }, $use_only)); 

    return $sql;
}

function hdesk_translate_equality($equality, $term) {
    $safe_term = addslashes($term);
    switch ($equality) {
        case 'contains':
            return "LIKE '%{$safe_term}%'";
        case 'equal':
            return "= '{$safe_term}'";
        case 'starts':
            return "LIKE '{$safe_term}%'";
        case 'ends':
            return "LIKE '%{$safe_term}'";
    }
}

