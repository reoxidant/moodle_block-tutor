<?php
/**
 * Description actions
 * @copyright 2021 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace view;

/**
 * Class StrategyStudentView
 * @package view
 */
class StrategyStudentView
{
    /**
     * @var array
     */
    private array $data = array();

    /**
     * @var string
     */
    public string $html;

    /**
     * StrategyStudentView constructor.
     * @param $studentData
     */
    public function __construct($studentData)
    {
        $this -> data = $studentData;
    }

    /**
     * @throws \coding_exception
     */
    public function pullHtmlStudentData()
    {
        list(
            "studenturl" => $studenturl,
            "studentname" => $studentname,
            "leangroup" => $leangroup,
            "hasfindebt" => $hasfindebt,
            "groupname" => $groupname
            ) = $this -> data;

        if ($hasfindebt) {
            $classNameForListItem = "studentrow hasfindebt";
            $htmlhasfindebt = $this -> hasfindebt();
        } else {
            $classNameForListItem = "studentrow";
            $htmlhasfindebt = "";
        }

        $this -> html =
            \html_writer ::start_tag('ul') .
            \html_writer ::start_tag('li', array('class' => $classNameForListItem)) .
            //html_writer::link($url, 'Some text to display', array('target' => '_blank'));
            \html_writer ::start_tag('a', array('href' => $studenturl, 'target' => '_blank'))
            . " $studentname " .
            \html_writer ::end_tag('a') .

            $this -> leangroup($leangroup) .

            $this -> groupname($groupname) .

            $htmlhasfindebt .

            $this -> studentCourseDataBy() .

            \html_writer ::end_tag('li') .
            \html_writer ::end_tag('ul');
    }

    /**
     * @return string
     * @throws \coding_exception
     */
    private function hasfindebt(): string
    {
        return
            \html_writer ::start_tag('span', array('class' => 'hasfindebt_info')) .
            " (" . get_string("hasfindebt", 'block_tutor') . ")" .
            \html_writer ::end_tag('span');
    }

    /**
     * @param $student_leangroup
     * @return string
     */
    private function leangroup($student_leangroup): string
    {
        return
            \html_writer ::start_tag('i') .
            "(" . $student_leangroup . ")" .
            \html_writer ::end_tag('i');
    }

    /**
     * @param $student_groupname
     * @return string
     */
    private function groupname($student_groupname): string
    {
        return
            " - " .
            \html_writer ::start_tag('small') .
            $student_groupname .
            \html_writer ::end_tag('small');
    }

    /**
     * @return string
     * @throws \coding_exception
     */
    private function studentCourseDataBy(): string
    {
        list("studentid" => $userid, "groupid" => $groupid, "coursedata" => $coursedata) = $this -> data;

//             <ul>
//                {{#data}}
//                    <li><a href="{{{courseurl}}}" target="_blank">{{coursename}}</a>
//                    {{#mod_info}}
//                        {{#mod_url}}
//                            - (<b><a href="{{mod_url}}&rownum=0&action=grader&userid={{userid}}&group={{groupid}}&treset=1" target="_blank" title="{{#str}} gotosubmition, block_tutor {{/str}}">{{mod_grade}}</a></b>)
//                        {{/mod_url}}
//                        {{^mod_url}}
//                            - (<b>{{mod_grade}}</b>)
//                        {{/mod_url}}
//                    {{/mod_info}}
//                    </li>
//                {{/data}}
//            </ul>

        $html_course = "";

        foreach ($coursedata as $course) {
            $html_course .=
                \html_writer ::start_tag('ul') .
                \html_writer ::start_tag('li') .
                \html_writer ::start_tag('a', array('href' => $studenturl, 'target' => '_blank')) . " {$course["coursename"]} " . \html_writer ::end_tag("a") .
                \html_writer ::start_tag("b") .
                \html_writer ::start_tag("a",
                    array(
                        'href' => "{$course['mod_info']}&rownum=0&action=grader&userid=$userid&group=$groupid&treset=1",
                        'target' => '_blank',
                        'title' => get_string("gotosubmition", 'block_tutor')
                    )
                ) .
                \html_writer ::end_tag("a") .
                \html_writer ::end_tag("b") .
                (!$data = $course['mod_info']) ? \html_writer ::start_tag("b") . $data['modgrade'] : "" .
                    \html_writer ::end_tag("b") .
                    \html_writer ::end_tag('li') .
                    \html_writer ::end_tag('ul');
        }

        return $html_course;
    }

    /**
     * @param $data
     * @return string
     */
    private function modinfo($data = array()): string
    {
        if (!empty($data)) {
            return \html_writer ::start_tag("b") . $data['modgrade'] . \html_writer ::end_tag("b");
        } else {
            return "";
        }
    }
}