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
use sirius_student;

/**
 * Class StrategyAjax
 * @package Strategy
 */
class StrategyAjax extends sirius_student implements Strategy
{
    /**
     * @var
     */
    private $student_id;
    /**
     * @var
     */
    private $select_list;

    /**
     * StrategyAjax constructor.
     * @param $queueName
     */
    public function __construct($student_id, $select_list)
    {
        $this -> student_id = $student_id;
        $this -> select_list = $select_list;
    }

    /**
     * @param $student_id
     * @param $selectList
     * @return array
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function get_students(): array
    {
        $studentCourses = $this -> getStudentCoursesById($this->student_id);

        $course = new course();

        //what need for data view

        //$curuser_hasfindebt = sirius_student::check_hasfindebt($userid);
        //$student_leangroup = self::get_student_leangroup($userid);
        //$mod_info = $this->get_grade_mod($course, $userid, $group_data->id);
        //$data = Array('userid' => $userid, 'coursename' => $coursename, 'courseurl' => $courseurl_return, 'mod_info' => $mod_info);
    }

    /**
     * @param $student_id
     * @return array
     */
    private function getStudentCoursesById($student_id)
    {
        $arrCourses = array();

        foreach ($this -> db_course_records_by($student_id) as $key => $value)
            $arrCourses[$value -> courseid][$value -> name] = $value;

        return $arrCourses;
    }

    /**
     * @param $student_id
     * @return array
     * @throws \dml_exception
     */
    private function db_course_records_by($student_id)
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