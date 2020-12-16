<?php

use Strategy\StrategyAjax;

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');

// Get submitted parameters.
$selectList = required_param('selectList', PARAM_RAW);
$studentId = optional_param('studentId', null, PARAM_INT);
$groupId = optional_param('groupId', null, PARAM_INT);

if ($_POST ?? null) {
    if
    (
        is_string($selectList) && !is_null($selectList)
        &&
        (!is_null($studentId) || !is_null($groupId))
    )
    {
//        $view = new output\studentslist_view;
//        die("hello");
//        $view -> setStrategy(new StrategyAjax());
//        echo $view -> strategy -> get_student($studentId, $selectList);
    }
}

?>