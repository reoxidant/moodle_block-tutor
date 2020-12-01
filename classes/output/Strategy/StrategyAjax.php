<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

use block_tutor\output\Strategy;

require_once("../studentslist_view.php");

class StrategyAjax implements Strategy
{
    public function get_students(): array
    {
        // TODO: Implement get_students() method.
    }

    public function get_students_by_request($student_id, $selectList)
    {
        global $DB, $USER;

        try {
            if (is_int($student_id) && is_string($selectList)) {
                $course_data = $this -> getUserCoursesAndGroupsById($student_id);

                foreach ($course_data as $courseid => $group) {
                    $course = $DB -> get_record('course', array('id' => $courseid));
                    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

                    foreach ($group as $groupname => $group_data) {
                        $coursename = $group_data -> coursename;
                        $group_students = $this -> returnArr($group_data -> id, $courseid);
                        /*$this -> getReturnStudentsArr($group_students, $course, $courseurl, $coursename, $return_arr, $group_data, $groupname);*/
                    }
                    usort($return_arr['students'][$userid]['data'], array('self', 'cmp'));
                }
            }

            //$this -> generateHtmlList($return_arr, $selectList);

            echo json_encode($return_arr);

        } catch (Exception $e) {
            echo 'Выброшено исключение: ', $e -> getMessage(), "\n";
        }
    }
}