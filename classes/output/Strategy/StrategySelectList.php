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

class StrategySelectList extends sirius_student implements Strategy
{
    public function get_students(): array
    {
        $return_arr = array('students' => array(), 'groups' => array());

        $course_data = $this -> getUserGroups();

        foreach ($course_data as $courseid => $group) {
            $return_arr['students'] = $this -> getProfileStudentBy($this -> getByCourseDataAllStudents($courseid, $group));
        }
        return $return_arr;
    }

    //$return_arr['groups'][$groupname]['name'] = $groupname;
    //$return_arr['groups'][$groupname]['groupid'] = $group_data -> id;

    private function getByCourseDataAllStudents($courseid, $group): array
    {
        foreach ($group as $groupname => $group_data) {
            $arStudents = $this -> getGroupUsersByRole($group_data -> id, $courseid);
        }
        return $arStudents;
    }

    private function getProfileStudentBy($group_students): array
    {
        $arProfile = [];
        foreach ($group_students as $userid => $profile) {
            $studentname = $profile -> name;
            $arProfile[$userid] =
                [
                    'studentname' => $studentname,
                    'userid' => $userid
                ];
        }
        return $arProfile;
    }
}