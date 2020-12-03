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
        $course_data = $this -> getUserGroups();

        $listData = array('students' => array(), 'groups' => array());

        foreach ($course_data as $courseid => $group) {
            $listData['groups_data'] = $this->getByCourseDataAllStudents($courseid, $group);
            $listData['students'] = $this->getProfileStudentBy($listData['groups_data']);
        }

        return $listData;
    }

    //$return_arr['groups'][$groupname]['name'] = $groupname;
    //$return_arr['groups'][$groupname]['groupid'] = $group_data -> id;

    private function getByCourseDataAllStudents($courseid, $group): array
    {
        $group_students = [];
        foreach ($group as $groupname => $group_data) {
            array_push($group_students, $this -> getGroupUsersByRole($group_data -> id, $courseid));
        }
        return $group_students;
    }

    private function getProfileStudentBy($group_students): array
    {
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