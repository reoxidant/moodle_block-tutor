<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace Strategy;

use block_tutor\output\Strategy;
use sirius_student;

class StrategySelectorList extends sirius_student implements Strategy
{
    public function get_students(): array
    {
        $return_arr = array('students' => array(), 'groups' => array());

        $course_data = $this -> getUserGroups();

        foreach ($course_data as $courseid => $group) {
            $group_students = $this -> getByGroupDataAllStudents($group);
            $return_arr['students'] = $this -> getProfileStudentBy($group_students);
        }
        return $return_arr;
    }

    //$return_arr['groups'][$groupname]['name'] = $groupname;
    //$return_arr['groups'][$groupname]['groupid'] = $group_data -> id;

    private function getByGroupDataAllStudents($group): array
    {
        foreach ($group as $groupname => $group_data) {
            return $this -> getGroupUsersByRole($group_data -> id, $courseid);
        }
    }

    private function getProfileStudentBy($group_students) : array
    {
        foreach ($group_students as $userid => $profile) {
            $studentname = $profile -> name;
            return [
                $userid => [
                    'studentname' => $studentname,
                    'userid' => $userid
                ]
            ];
        }
    }
}