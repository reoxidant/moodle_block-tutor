<?php

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/blocks/tutor/lib.php');

if ($ADMIN->fulltree) {
    //block settings
    $options = [
        BLOCK_TUTOR_NEEDGRADIGN_VIEW => get_string('needgradign', 'block_tutor'),
        BLOCK_TUTOR_STUDENTSLIST_VIEW => get_string('studentslist', 'block_tutor'),
        BLOCK_TUTOR_ENROLLED_VIEW => get_string('enrolled', 'block_tutor')
    ];

    $settings->add(new admin_setting_configselect('block_tutor/defaulttab',
        get_string('defaulttab', 'block_tutor'),
        get_string('defaulttab_desc', 'block_tutor'), 'needgradign', $options));
}
