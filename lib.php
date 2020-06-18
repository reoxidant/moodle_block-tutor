<?php
defined('MOODLE_INTERNAL') || die();

/**
 * The needgradign view.
 */
define('BLOCK_TUTOR_NEEDGRADIGN_VIEW', 'needgradign');

/**
 * The needgradign sort.
 */
define('BLOCK_TUTOR_STUDENTLIST_BYGROUP', 'bygroup');
define('BLOCK_TUTOR_STUDENTLIST_BYSTUDENT', 'bystudent');

/**
 * The studentslist view.
 */
define('BLOCK_TUTOR_STUDENTSLIST_VIEW', 'studentslist');

/**
 * The courses enrolled view.
 */
define('BLOCK_TUTOR_ENROLLED_VIEW', 'enrolled');

/**
 * The courses enrolled view.
 */
define('BLOCK_TUTOR_WEBINARS_VIEW', 'webinars');

/**
 * Returns the name of the user preferences as well as the details this plugin uses.
 *
 * @return array
 */
function block_tutor_user_preferences()
{
    $preferences = array();
    $preferences['block_tutor_last_tab'] = array(
        'type' => PARAM_ALPHA,
        'null' => NULL_NOT_ALLOWED,
        'default' => BLOCK_TUTOR_NEEDGRADIGN_VIEW,
        'choices' => array(BLOCK_TUTOR_NEEDGRADIGN_VIEW, BLOCK_TUTOR_STUDENTSLIST_VIEW, BLOCK_TUTOR_ENROLLED_VIEW, BLOCK_TUTOR_WEBINARS_VIEW)
    );
    $preferences['block_tutor_studentlist_tab'] = array(
        'type' => PARAM_ALPHA,
        'null' => NULL_NOT_ALLOWED,
        'default' => BLOCK_TUTOR_STUDENTLIST_BYGROUP,
        'choices' => array(BLOCK_TUTOR_STUDENTLIST_BYGROUP, BLOCK_TUTOR_STUDENTLIST_BYSTUDENT) // ЗНАЧЕНИЯ ДОЛЖНЫ БЫТЬ БЕЗ ЦИФР. ИНАЧЕ НЕ БУДЕТ ЗАПИСЫВАТЬСЯ
    );


    return $preferences;
}

function debug($arr, $die = false)
{
    echo '<pre>';
    var_dump($arr);
    if ($die) die('test');
    echo '</pre>';
}

?>