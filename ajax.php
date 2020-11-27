<?php

use block_tutor\output;

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/output/studentslist_view.php');

// Get submitted parameters.
$selectList = required_param('selectList', PARAM_RAW);
$studentId = optional_param('studentId', null, PARAM_INT);
$groupId = optional_param('groupId', null, PARAM_INT);

if ($_POST ?? null) {
    if (is_string($selectList) && !is_null($selectList) && (!is_null($studentId) || !is_null($groupId))) {
        $studentsList = new output\studentslist_view;
        $studentsList -> getStudentAndGroupData($studentId, $selectList, true);
    }
}

?>