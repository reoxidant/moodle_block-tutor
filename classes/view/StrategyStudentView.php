<?php
/**
 * Description actions
 * @copyright 2021 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace view;

class StrategyStudentView
{
    private array $data = (array)null;

    public function __construct($studentData)
    {
        $this -> data = $studentData;
    }

    private function generateStudentList()
    {
        return
            \html_writer ::start_tag('ul') .
            \html_writer ::start_tag('li', array('class' => 'studentrow')) .

            \html_writer ::start_tag('a', array('href' => $studenturl, 'target' => '_blank'))
            . $studentname .
            \html_writer ::end_tag('a') .

            \html_writer ::start_tag('i')
            . $student_leangroup .
            \html_writer ::end_tag('i') .

            \html_writer ::start_tag('small')
            . $groupname .
            \html_writer ::end_tag('small') .

            \html_writer ::start_tag('span', array('class' => 'hasfindebt_info')) .
            get_string("hasfindebt", 'block_tutor') .
            \html_writer ::end_tag('span') .

            $this -> getCourseListData() .

            \html_writer ::end_tag('li') .
            \html_writer ::end_tag('ul');
    }

    private function getCourseListData()
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