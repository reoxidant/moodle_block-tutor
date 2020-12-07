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

class course
{
    private $courseid;
    private $group;
    private $listData;

    public function __construct($courseid, $group, $listData)
    {
        $this->courseid = $courseid;
        $this->group = $group;
        $this->listData = $listData;
    }

    /**
     * @return null
     */
    public function getCourseid()
    {
        return $this -> courseid;
    }

    /**
     * @return null
     */
    public function getGroup()
    {
        return $this -> group;
    }

    /**
     * @return array[]
     */
    public function getListData(): array
    {
        return $this -> listData;
    }

    /**
     * @param mixed $courseid
     */
    public function setCourseid($courseid)
    {
        $this -> courseid = $courseid;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this -> group = $group;
    }

    /**
     * @param $studentData
     * @param $byKey
     */
    public function setStudentList($studentData, $byKey)
    {
        $this -> listData['students'][$byKey] = $studentData;
    }
}

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

        return $listData;
    }

    //$return_arr['groups'][$groupname]['name'] = $groupname;
    //$return_arr['groups'][$groupname]['groupid'] = $group_data -> id;

    /**
     * @param $courseid
     * @param $group
     * @param $listData
     */
    private function setCourseListBy($course)
    {
        foreach ($course->getGroup() as $groupname => $group_data) {
            $group_students = $this -> getGroupUsersByRole($group_data -> id, $course->getCourseid());
            $this -> setCourseListStudentsBy($group_students, $course);
        }
    }

    /**
     * @param $group_students
     * @param $listData
     */
    private function setCourseListStudentsBy($group_students, $course)
    {
        foreach ($group_students as $userid => $profile) {
            $studentname = $profile -> name;
            $studentData = ['studentname' => $studentname, 'userid' => $userid];
            $course->setStudentList($studentData, $userid);
        }
    }
}