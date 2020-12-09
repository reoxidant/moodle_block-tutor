<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace block_tutor\output;

use sirius_student;

class course extends sirius_student
{
    private $courseid;
    private $group;
    private $listData;
    private $courseurl;

    public function __construct($courseid, $courseurl, $group, $listData)
    {
        $this -> courseid = $courseid;
        $this -> courseurl = $courseurl;
        $this -> group = $group;
        $this -> listData = $listData;
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
    public function setCourseListBy($course, $selectList)
    {
        foreach ($course->getGroup() as $groupname => $group_data) {

            $group_students = $this -> getGroupUsersByRole($group_data -> id, $course->getCourseid());

            foreach ($group_students as $userid => $profile) {

                if($selectList == "studentlist" || $selectList == "default")
                {
                    $course->setStudentList(
                        [
                            'studentname' => $profile -> name,
                            'userid' => $userid
                        ],
                        $userid
                    );
                }

                if($selectList == "grouplist" || $selectList == "default")
                {
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
}