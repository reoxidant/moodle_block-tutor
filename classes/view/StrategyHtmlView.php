<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace view;

/**
 * Class StrategyHtmlView
 * @package view
 */
class StrategyHtmlView
{
    /**
     * @param $return_arr
     * @param $selectList
     * @return string
     */
    private function generateHtmlList($return_arr, $selectList)
    {
        if ($selectList === "grouplist") {

        } else if ($selectList === "studentlist") {
            $html = \html_writer ::start_tag('ol') . $this -> generateStudentList($return_arr) . \html_writer ::end_tag('ol');
        }

        return $html;
    }
}