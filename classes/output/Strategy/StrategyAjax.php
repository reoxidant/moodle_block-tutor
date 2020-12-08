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

class StrategyAjax implements Strategy
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

        foreach ($course_data as $courseid => $group) {
            $course = new course(null, null, array('students' => array(), 'groups' => array()));
        }

        echo json_encode($return_arr);

        return [];
    }

    private function getStudentCoursesById($student_id)
    {
        $groupedByCourseidArr = array();

        foreach ($this->db_result_connect($student_id) as $key => $value)
            $groupedByCourseidArr[$value -> courseid][$value -> name] = $value;

        return $groupedByCourseidArr;
    }

    private function db_result_connect($student_id)
    {
        $this->db_connect(
             "SELECT g.id, g.courseid, g.name, c.fullname as coursename
                  FROM
                    {groups} g,
                    {groups_members} gm,
                    {course} c
                  WHERE 
                    g.id = gm.groupid
                    AND c.id = g.courseid
                    AND gm.userid = $student_id
                  ORDER BY g.name, c.fullname;",
            $student_id
        );
    }

    private function db_connect($sql, $student_id)
    {
        global $DB;
        return $DB -> get_records_sql($sql, array($student_id));
    }
}