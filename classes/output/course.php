<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace block_tutor\output;

use sirius_student;

require_once("group.php");
require_once("student.php");

/**
 * Class course
 * @package block_tutor\output
 */
class course extends sirius_student
{
    /**
     * @var
     */
    private $courseid;
    /**
     * @var array[]
     */
    private $arData = array('students' => array(), 'groups' => array());
    /**
     * @var
     */
    private $listData;
    /**
     * @var
     */
    private $courseurl;
    /**
     * @var
     */
    private $courseGroups;

    /**
     * @return null
     */
    public function getCourseid()
    {
        return $this -> courseid;
    }

    /**
     * @return mixed
     */
    public function getCourseurl()
    {
        return $this -> courseurl;
    }

    /**
     * @return null
     */
    public function getGroup()
    {
        return $this -> arData;
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
     * @param $courseurl
     */
    public function setCourseurl($courseurl)
    {
        $this -> courseurl = $courseurl;
    }

    /**
     * @param $courseGroups
     */
    public function setCourseGroups($courseGroups)
    {
        $this -> courseGroups = $courseGroups;
    }

    /**
     * @param $curDataStudent
     */
    public function setStudentList($student)
    {
        $studentVars = get_object_vars($student);
        $this -> listData['students'][$studentVars['studentid']] = $studentVars;
    }

    /**
     * @param mixed $listData
     */
    public function setListData($listData)
    {
        $this -> listData = $listData;
    }

    /**
     * @param $obj_group
     */
    public function setGroupsList($obj_group)
    {
        $groupVars = get_object_vars($obj_group);
        $this -> listData['groups'][$groupVars["name"]] = $groupVars;
    }

    /**
     * @param $courseid
     * @param $group
     * @param $listData
     */
    public function setCourseList()
    {
        foreach ($this -> courseGroups as $groupname => $group_data) {

            $obj_group = new group($group_data -> id, $groupname);

            $group_students = $this -> getGroupUsersByRole($group_data -> id, $this -> getCourseid());

            foreach ($group_students as $userid => $profile) {

                $obj_student = new student($userid, $profile -> name, $profile -> profileurl);

                $this -> setListBy($obj_student, $obj_group);
            }
        }
    }

    /**
     * @param $obj_student
     * @param $obj_group
     */
    private function setListBy($obj_student, $obj_group)
    {
        $this -> setStudentList($obj_student);

        $this -> setGroupsList($obj_group);
    }

    /**
     * @param $selectList
     * @param $studentid
     */
    public function getListByRequest($selectList, $studentid)
    {

    }
}