<?php

/**
 *
 */
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '\local\student_lib\locallib.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '\blocks\tutor\classes\controller\StudentsViewController.php');

// Get submitted parameters.
$selectList = required_param('selectList', PARAM_RAW);
$studentId = optional_param('studentId', null, PARAM_INT);
$groupId = optional_param('groupId', null, PARAM_INT);

if ($_POST ?? null) if (is_string($selectList) && (!is_null($studentId) || !is_null($groupId))) {
    $controller = new \controller\StudentsViewController(new \controller\StrategyAjaxViewController($studentId ?? $groupId, $selectList));
    $controller -> strategy -> setData($controller->strategy->get_students());
    $controller -> strategy -> showDataOnThePage();
}
?>