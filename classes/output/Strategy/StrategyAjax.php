<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace Strategy;

use block_tutor\output\course;
use block_tutor\output\Strategy;
use block_tutor\output\student;
use moodle_url;
use sirius_student;

/**
 * Class StrategyAjax
 * @package Strategy
 */
class StrategyAjax extends sirius_student implements Strategy
{
    /**
     * @var int
     */
    private int $student_id;
    /**
     * @var string
     */
    private string $select_list;

    /**
     * StrategyAjax constructor.
     * @param $student_id
     * @param $select_list
     */
    public function __construct($student_id, $select_list)
    {
        $this -> student_id = $student_id;
        $this -> select_list = $select_list;
    }

    /**
     * @return array
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function get_students(): array
    {
        list($studentCourses, $coursesAndGroups) = $this -> getStudentCoursesById($this->student_id);

        $course = new course();

        if ($this->select_list == "studentlist"){
            $student = $this->collectDataStudentBy($this->student_id, $coursesAndGroups);
            foreach ($studentCourses as $courseid => $data) {
                $course -> id = $courseid;
                $course -> url = new moodle_url('/course/view.php', array('id' => $courseid));
                $student -> set_grade_mod($coursesAndGroups);
                $student -> set_course_data($data -> coursename, $course -> url);
                $course -> setStudentList($student);
            }
        } else {
            var_dump("It not time yet!");
        }

        //what need for data view

        //$curuser_hasfindebt = sirius_student::check_hasfindebt($userid);
        //$student_leangroup = self::get_student_leangroup($userid);
        //$mod_info = $this->get_grade_mod($course, $userid, $group_data->id);
        //$data = Array('userid' => $userid, 'coursename' => $coursename, 'courseurl' => $courseurl_return, 'mod_info' => $mod_info);
    }

    /**
     *
     * @throws dml_exception
     * @throws \dml_exception
     * @throws \dml_exception
     */
    private function collectDataStudentBy($student_id, $coursesAndGroups): student
    {
        $student = new student($student_id, null, null);
        $student -> check_student_hasfindebt();
        $student -> set_student_leangroup();
        $student -> set_grade_mod($coursesAndGroups);
        return $student;
    }

    /**
     * @param $student_id
     * @return array
     * @throws \dml_exception
     */
    private function getStudentCoursesById($student_id): array
    {
        $arrCourses = array();

        foreach ($this -> db_course_records_by($student_id) as $key => $value)
        {
            $arrCourses[$value -> courseid] = $value;
            $arrCoursesAndGroups[$value -> courseid][$value->name] = $value;
        }

        return [$arrCourses, $arrCoursesAndGroups];
    }

    /**
     * @param $student_id
     * @return array
     * @throws \dml_exception
     */
    private function db_course_records_by($student_id): array
    {
        global $DB;

        $sql = "SELECT g.id, g.courseid, g.name, c.fullname as coursename
                  FROM
                    {groups} g,
                    {groups_members} gm,
                    {course} c
                  WHERE 
                    g.id = gm.groupid
                    AND c.id = g.courseid
                    AND gm.userid = $student_id
                  ORDER BY g.name, c.fullname;";

        return $DB -> get_records_sql($sql, array($student_id));
    }
}