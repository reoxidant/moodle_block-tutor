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
$controller = new controller\StudentsViewController;

if ($_POST ?? null) if (is_string($selectList) && (!is_null($studentId) || !is_null($groupId))) {
    $controller -> setStrategy(new \controller\StrategyAjaxViewController($studentId ?? $groupId, $selectList));
    showDataOnThePage($selectList, $controller -> strategy -> get_students());
}

/**
 * @param $selectList
 * @param $data
 */
function showDataOnThePage($selectList, $data)
{
    if ($selectList == "studentlist") {
        $controller -> setStrategy(new \view\StrategyStudentView($data));
        $controller -> strategy -> generateStudentList();
    } else {
        $controller -> setStrategy(new \view\StrategyGroupView($data));
        $controller -> strategy -> generateGroupList();
    }
    echo $controller -> strategy -> html;
}

?>