<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace controller;

use dml_exception;
use model\Group;
use model\StructStudentCourse;
use model\Student;
use moodle_url;
use sirius_student;
use view\StrategyGroupView;
use view\StrategyStudentView;

require_once($_SERVER['DOCUMENT_ROOT'] . "/blocks/tutor/classes/view/StrategyStudentView.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/blocks/tutor/classes/view/StrategyGroupView.php");

/**
 * Class StrategyAjax
 * @package Strategy
 */
class StrategyAjaxViewController extends sirius_student implements Strategy
{
    /**
     * @var int
     */
    private int $chosen_id;
    /**
     * @var string
     */
    private string $select_list;

    /**
     * @var array
     */
    private array $data;

    /**
     * @var array
     */
    private array $studentsCache;

    /**
     * StrategyAjax constructor.
     * @param $student_id
     * @param $select_list
     */
    public function __construct($chosen_id, $select_list)
    {
        $this -> chosen_id = $chosen_id;
        $this -> select_list = $select_list;
    }

    /**
     * @return array|void
     * @throws \coding_exception
     */
    public function get_students(): array
    {
        $cache = \cache ::make('block_tutor', 'student_screen_data');
        if ($this -> select_list === "grouplist") {
            if ($this -> studentsCache = $cache -> get('student_screen_data')["groups"]) return $this -> collectGroupTemplateData();
            return array();
        } else if ($this -> select_list === "studentlist") {
            if ($this -> studentsCache = $cache -> get('student_screen_data')["students"]) return $this -> collectStudentTemplateData();
            return array();
        }
    }

    /**
     * @return array
     */
    private function collectStudentTemplateData(): array
    {
        $student_id = $this -> chosen_id;
        $student = $this -> initiateStudentBy($student_id);
        $student -> get_student_data_from_cache($this -> studentsCache);
        $this -> fillCoursesFor($student);

        return (array)$student;
    }

    /**
     * @return array
     * @throws \coding_exception
     */
    private function collectGroupTemplateData(): array
    {
        $group_id = $this -> chosen_id;
        $group = new group($group_id, null);
        $group -> get_groups_data_from_cache($this -> studentsCache);
        $cache = \cache ::make('block_tutor', 'student_screen_data');

        foreach ($group -> students as $group_student) {
            $studentCourse = $cache -> get('student_screen_data')["students"][$group_student -> id];
            $student = $this -> initiateStudentBy($group_student -> id);
            $this -> fillGroupStudentCourse($group_id, $student, $studentCourse);
            $group -> students[$student -> studentid] = $student;
        }

        return (array)$group;
    }

    /**
     * @param $studentid
     * @return Student
     */
    private function initiateStudentBy($studentid): Student
    {
        try {
            $student = new student($studentid, null, null);
        } catch (dml_exception | \moodle_exception $e) {
            debugging($e -> getMessage());
        }
        $student -> check_student_hasfindebt();
        try {
            $student -> set_student_leangroup();
        } catch (dml_exception $e) {
            debugging($e -> getMessage());
        }
        return $student;
    }

    /**
     * @param $group_id
     * @param $student
     * @param $studentCourse
     */
    private function fillGroupStudentCourse($group_id, &$student, &$studentCourse)
    {
        foreach ($studentCourse["studentdata"] as $courseid => $course) {
            if (in_array($group_id, $course['groupid'])) {
                $student -> studentdata["course_data"][] = $course["course_data"];
                try {
                    if ($grade_mod = $student -> set_mod_info($courseid, $group_id)) {
                        $course["course_data"] -> mod_info[] = $grade_mod;
                    }
                } catch (dml_exception $e) {
                    debugging($e -> getMessage());
                }
            }
            continue;
        }
    }

    /**
     * @param $student
     */
    private function fillCoursesFor(&$student)
    {
        foreach ($student -> studentdata as $courseid => $course) {
            try {
                $course["course_data"] -> url = new moodle_url('/course/view.php', array('id' => $courseid));
            } catch (\moodle_exception $e) {
                debugging($e -> getMessage());
            }
            foreach ($course['groupid'] as $groupid) {
                try {
                    if ($grade_mod = $student -> set_mod_info($courseid, $groupid)) {
                        $course["course_data"] -> mod_info[] = $grade_mod;
                    }
                } catch (dml_exception $e) {
                    debugging($e -> getMessage());
                }
            }
        }

        usort($student -> studentdata, array('self', 'cmp'));
    }

    /**
     * @param $selectList
     * @param $data
     * @throws \coding_exception
     */
    public function showDataOnThePage()
    {
        if ($this -> select_list == "studentlist") {
            $studentView = new StrategyStudentView($this -> data);
            $studentView -> pullHtmlStudentData();
        } else {
            $groupView = new StrategyGroupView($this -> data);
            $groupView -> pullHtmlGroupData();
        }
        echo $studentView -> html ?? $groupView -> html;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this -> data = $data;
    }
}