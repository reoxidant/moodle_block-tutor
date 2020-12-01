<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

use block_tutor\output\Strategy;

require_once("../studentslist_view.php");

class StrategyHtml implements Strategy
{
    public function get_students(): array
    {
        // TODO: Implement get_students() method.
    }

    private function generateHtmlList($return_arr, $selectList)
    {
        if ($selectList === "grouplist") {
            $html = \html_writer ::start_tag('ol') . $this -> generateGroupList($return_arr) . \html_writer ::end_tag('ol');
        } else if ($selectList === "studentlist") {
            $html = \html_writer ::start_tag('ol') . $this -> generateStudentList($return_arr) . \html_writer ::end_tag('ol');
        }

        return $html;
    }

    private function generateGroupList($return_arr)
    {
        return
            \html_writer ::start_tag('div') .
            \html_writer ::start_tag('h5') . $name . \html_writer ::end_tag('h5') .
            $this -> generateStudentList($return_arr) .
            \html_writer ::end_tag('div');
    }

    private function generateStudentList($return_arr)
    {
        return
            \html_writer ::start_tag('ul') .
            \html_writer ::start_tag('li', array('class' => 'studentrow')) .

            \html_writer ::start_tag('a', array('href' => $studenturl, 'target' => '_blank')) . $studentname . \html_writer ::end_tag('a') .

            \html_writer ::start_tag('i') . $student_leangroup . \html_writer ::end_tag('i') .

            \html_writer ::start_tag('small') . $groupname . \html_writer ::end_tag('small') .

            \html_writer ::start_tag('span', array('class' => 'hasfindebt_info')) .
            get_string("hasfindebt", 'block_tutor') .
            \html_writer ::end_tag('span') .

            $this -> getHtmlStudentData() .

            \html_writer ::end_tag('li') .
            \html_writer ::end_tag('ul');
    }

    private function getHtmlStudentData()
    {
        return
            \html_writer ::start_tag('ul') .
            \html_writer ::start_tag('li') .
            \html_writer ::start_tag("b") .
            \html_writer ::start_tag("a",
                array(
                    'href' => "$mod_url&rownum=0&action=grader&userid={{userid}}&group={{groupid}}&treset=1",
                    'target' => '_blank',
                    'title' => get_string("gotosubmition", 'block_tutor')
                )
            ) .
            \html_writer ::end_tag("a") .
            \html_writer ::end_tag("b") .
            \html_writer ::start_tag("b") . $modgrade . \html_writer ::end_tag("b") .
            \html_writer ::end_tag('li') .
            \html_writer ::end_tag('ul');
    }


}