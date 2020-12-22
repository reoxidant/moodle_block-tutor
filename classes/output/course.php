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
    private $arrResultData = array('students' => array(), 'groups' => array());
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
        return $this -> arrResultData;
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

    public function setCourseListByRequest($student_id, $select_list)
    {
        foreach ($this -> courseGroups as $groupname => $group_data) {

            $mod_info = $this->get_grade_mod($student_id, $group_data->id);

            $data = Array('userid' => $student_id, 'coursename' => $groupname, 'courseurl' => $this->courseurl, 'mod_info' => $mod_info);
        }
    }

    /**
     * @param $obj_student
     * @param $obj_group
     * @param $selectList
     */
    public function setListByRequest($obj_student, $obj_group, $selectList)
    {
        ($selectList == "studentlist")?
            $this -> setStudentList($obj_student) : $this -> setGroupsList($obj_group);
    }

    private function get_grade_mod($userid, $groupid)
    {
        global $DB;
        $course = $DB->get_record('course', array('id' => $this->courseid));

        $modinfo_obj = new modinfo($course);

        return $modinfo_obj->modinfo_data($userid);
    }
}

class modinfo extends sirius_student
{
    private $course_mod_info;
    private $cms;
    private $courseid;

    public function __construct($course)
    {
        $this->courseid = $course->id;
        $this->course_mod_info = get_fast_modinfo($course);
        $this->cms = $this->course_mod_info->cms;
    }

    public function modinfo_data($userid)
    {
        foreach ($this->cms as $mod) {
            $modname = $mod->modname;

            if ($this->check_mod_capability($mod) && ($modname == 'assign' || $modname == 'quiz')) {

                $mod_grade = grade_get_grades($this->courseid, 'mod', $modname, $mod->instance, $userid);
                @$mod_grade = current($mod_grade->items[0]->grades)->grade;

                if (empty($mod_grade))
                    continue;

                $return_arr['mod_grade'] = (string)intval($mod_grade);
                $return_arr['mod_url'] = ($modname == 'assign') ? null : $mod->url;
                $return_arr['modname'] = $modname;
                $return_arr['groupid'] = $groupid;

                break;
            }
        }

        return $return_arr ?? [];
    }
}