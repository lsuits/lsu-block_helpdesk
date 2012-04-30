<?php

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

