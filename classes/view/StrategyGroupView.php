<?php
/**
 * Description actions
 * @copyright 2021 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace view;

use moodle_url;

/**
 * Class StrategyGroupView
 * @package view
 */
class StrategyGroupView
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
     * StrategyGroupView constructor.
     * @param $groupData
     */
    public function __construct($groupData)
    {
        $this -> data = $groupData;
    }

    /**
     *
     */
    public function pullHtmlGroupData()
    {
        list("name" => $name) = $this -> data;

        $this -> html =
            \html_writer ::start_tag('h5') . $name . \html_writer ::end_tag('h5');
        try {
            \html_writer ::start_tag("ul") . $this -> createListStudents() . \html_writer ::end_tag("ul");
        } catch (\coding_exception | \moodle_exception $e) {
            debugging('Error creating list students for group ' . $name . ': ' . $e->getMessage());
        }
    }

    /**
     *
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    private function createListStudents()
    {
        list("students" => $studentList) = $this -> data;

        foreach ($studentList as $student) {

            if ($student -> hasfindebt) {
                $classNameForListItem = "studentrow hasfindebt";
                $htmlhasfindebt = $this -> hasfindebt();
            } else {
                $classNameForListItem = "studentrow";
                $htmlhasfindebt = "";
            }

            $this -> html .=
                \html_writer::start_tag("ul").
                \html_writer ::start_tag("li", array("class" => $classNameForListItem)) .
                \html_writer ::start_tag("a", array("href" => $student -> studenturl, "target" => "_blank")) .
                $student -> studentname .
                \html_writer ::end_tag("a") .
                " (" . \html_writer ::start_tag("i") . $student -> leangroup . \html_writer ::end_tag("i") . ") " .
                $htmlhasfindebt .
                $this -> createCourseListInfo($student -> studentdata["course_data"]) .
                \html_writer ::end_tag("li").
                \html_writer::end_tag("ul");
        }
    }

    /**
     * @param $courses
     * @return string
     * @throws \moodle_exception
     */
    private function createCourseListInfo($courses): string
    {
        $list = \html_writer ::start_tag('ul', array("class" => "list-group list-group-flush"));
        foreach ($courses as $course) {
            $list .=
                \html_writer ::start_tag("li", array("class" => "list-group-item")) .
                \html_writer ::start_tag("a", array("href" => new moodle_url('/course/view.php', array('id' => $course -> courseid)), "target" => "_blank")) .
                $course -> coursename .
                \html_writer ::end_tag("a") .
                \html_writer ::end_tag("li");
        }
        $list .= \html_writer ::end_tag("ul");

        return $list;
    }

    /**
     * @return string
     * @throws \coding_exception
     */
    private function hasfindebt(): string
    {
        return
            \html_writer ::start_tag("span", array("class" => "hasfindebt_info")) .
            " (" . get_string("hasfindebt", "block_tutor") . ") " .
            \html_writer ::end_tag("span");
    }
}