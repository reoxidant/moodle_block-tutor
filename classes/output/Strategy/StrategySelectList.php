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

        $course = new course(null, null, array('students' => array(), 'groups' => array()));

        foreach ($course_data as $courseid => $group) {
            $course->setCourseid($courseid);
            $course->setGroup($group);
            $this -> setCourseListBy($course);
        }

        return $course->getListData();
    }

    /**
     * @param $courseid
     * @param $group
     * @param $listData
     */
    private function setCourseListBy($course)
    {
        foreach ($course->getGroup() as $groupname => $group_data) {

            $group_students = $this -> getGroupUsersByRole($group_data -> id, $course->getCourseid());

            foreach ($group_students as $userid => $profile) {

                $course->setStudentList(
                    [
                        'studentname' => $profile -> name,
                        'userid' => $userid
                    ],
                    $userid
                );

                $course->setGroupsList(
                    [
                        'groupid' => $group_data -> id,
                        'name' => $groupname
                    ],
                    $groupname
                );
            }
        }
    }
}