<?php

/**
 * @package   block_helpdesk
 * @copyright 2016, Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_helpdesk';
$plugin->version = 2016022912;
$plugin->requires = 2015111600;
$plugin->release = 'v3.0.0';

$plugin->dependencies = array(
    'enrol_ues' => 2016022912,
);