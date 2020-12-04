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

/**
 * Class StrategySelectList
 * @package Strategy
 */
class StrategySelectList extends sirius_student implements Strategy
{
    /**
     * @return array[]
     */
    public function get_students(): array
    {
        $course_data = $this -> getUserGroups();

        $listData = array('students' => array(), 'groups' => array());

        foreach ($course_data as $courseid => $group) {
            $this -> getByCourseDataAllStudents($courseid, $group, $listData);
        }

        return $listData;
    }

    //$return_arr['groups'][$groupname]['name'] = $groupname;
    //$return_arr['groups'][$groupname]['groupid'] = $group_data -> id;

    /**
     * @param $courseid
     * @param $group
     * @param $listData
     */
    private function getByCourseDataAllStudents($courseid, $group, &$listData)
    {
        foreach ($group as $groupname => $group_data) {
            $group_students = $this -> getGroupUsersByRole($group_data -> id, $courseid);
            $this -> addNameAndIdEachStudentAt($group_students, $listData);
        }
    }

    /**
     * @param $group_students
     * @param $listData
     */
    private function addNameAndIdEachStudentAt($group_students, &$listData)
    {
        foreach ($group_students as $userid => $profile) {
            $studentname = $profile -> name;
            $listData['students'][$userid] =
                [
                    'studentname' => $studentname,
                    'userid' => $userid
                ];
        }
    }
}