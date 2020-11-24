<?php

use block_tutor\output;

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/output/studentslist_view.php');

// Get submitted parameters.
$selectList = required_param('selectList', PARAM_BOOL);
$studentId = required_param('studentId', PARAM_INT);

if ($POST ?? null) {
    if (is_bool($selectList) && is_int($studentId) && !is_null($studentId)) {
        $studentsList = new output\studentslist_view;
        $studentsList -> get_students_by_select($studentId);
    }
}

?>