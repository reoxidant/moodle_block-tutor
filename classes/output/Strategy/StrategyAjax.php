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
use moodle_url;
use sirius_student;

require_once("../group.php");
require_once("../course.php");

class StrategyAjax extends sirius_student implements Strategy
{
    /**
     * @param $student_id
     * @param $selectList
     * @return array
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function get_students($student_id, $selectList): array
    {
        $course_data = $this -> getStudentCoursesById($student_id);

        $course = new course(array('students' => array(), 'groups' => array()));

        foreach ($course_data as $courseid => $coursegroup) {
            $course->setCourseid($courseid);
            $course->setCourseurl(new moodle_url('/course/view.php', array('id' => $courseid)));
            $course->setCourseGroups($coursegroup);
            $course->setCourseList();
        }

        return $course->getListData();
    }

    private function getStudentCoursesById($student_id)
    {
        $groupedByCourseidArr = array();

        foreach ($this->db_course_records_by($student_id) as $key => $value)
            $groupedByCourseidArr[$value -> courseid][$value -> name] = $value;

        return $groupedByCourseidArr;
    }

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