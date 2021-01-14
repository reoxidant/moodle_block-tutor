<?php

/**
 *
 */
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '\local\student_lib\locallib.php');
require_once(__DIR__ . "/classes/controller/StudentsViewController.php");

// Get submitted parameters.
$selectList = required_param('selectList', PARAM_RAW);
$studentId = optional_param('studentId', null, PARAM_INT);
$groupId = optional_param('groupId', null, PARAM_INT);
$view = new controller\StudentsViewController;

if ($_POST ?? null) if (is_string($selectList) && (!is_null($studentId) || !is_null($groupId))) {
    $view -> setStrategy(new \controller\StrategyAjaxViewController($studentId ?? $groupId, $selectList));
    showDataOnThePage($selectList, $view -> strategy -> get_students());
}

function showDataOnThePage($selectList, $data){
    if ($selectList == "studentlist") {
        $view -> setStrategy(new \view\StrategyStudentView($data));
        $view -> strategy -> generateStudentList();
    } else {
        $view -> setStrategy(new \view\StrategyGroupView($data));
        $view -> strategy -> generateGroupList();
    }
}

echo $view -> strategy -> html;

?>