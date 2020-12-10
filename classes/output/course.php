<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace block_tutor\output;

use sirius_student;

//require_once("student.php");

class course extends sirius_student
{
    private $courseid;
    private $group;
    private $listData;
    private $courseurl;

    public function __construct($group)
    {
        $this -> group = $group;
    }

    /**
     * @return null
     */
    public function getCourseid()
    {
        return $this -> courseid;
    }

    public function getCourseurl()
    {
        return $this -> courseurl;
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

    public function setCourseurl($courseurl)
    {
        $this -> courseurl = $courseurl;
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

    /**
     * @param mixed $listData
     */
    public function setListData($listData)
    {
        $this -> listData = $listData;
    }

    public function setGroupsList($groupData, $byKey)
    {
        $this -> listData['groups'][$byKey] = $groupData;
    }

    /**
     * @param $courseid
     * @param $group
     * @param $listData
     */
    public function setCourseListsBy($course)
    {
        foreach ($course->getGroup() as $groupname => $group_data) {

            $group = new group($group_data -> id, $groupname);

            $group_students = $this -> getGroupUsersByRole($group_data -> id, $course->getCourseid());

            foreach ($group_students as $userid => $profile) {

                $student = new student($userid, $profile->name, $profile->profileurl);

                $this->setListBy($course, $student, $group);
            }
        }
    }

    private function setListBy($course, $student, $group)
    {
        $course->setStudentList(
            $student,
            $userid
        );

        $course->setGroupsList(
            $group,
            $groupname
        );
    }

    public function getListByRequest($selectList, $studentid)
    {

    }
}