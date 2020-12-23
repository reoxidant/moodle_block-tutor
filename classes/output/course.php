<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace block_tutor\output;

use MongoDB\Database;
use sirius_student;

require_once("group.php");
require_once("student.php");
require_once("modinfo.php");

/**
 * Class course
 * @package block_tutor\output
 */
class course extends sirius_student
{
    /**
     * @var
     */
    public int $id;

    /**
     * @var string|null
     */
    public ?string $url;

    /**
     * @var array
     */
    public array $groups;

    /**
     * @var array
     */
    public array $listData;

    public array $arrResultData = array('students' => array(), 'groups' => array());

    /**
     * @var dataBaseList
     */
    public dataBaseList $database;

    public function setStudentList($student)
    {
        $studentVars = get_object_vars($student);
        $this -> listData['students'][$studentVars['studentid']] = $studentVars;
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
     */
    public function setCourseList()
    {
        foreach ($this -> groups as $groupname => $group_data) {

            $obj_group = new group($group_data -> id, $groupname);

            $group_students = $this -> getGroupUsersByRole($group_data -> id, $this -> id);

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
     * @param $student_id
     * @param $select_list
     * @throws \dml_exception
     */
    public function setCourseListByRequest($student_id, $select_list)
    {
        foreach ($this -> groups as $groupname => $group_data) {

            if ($select_list == "studentlist")
                $this -> collectDataStudentBy($student_id, $group_data);
        }
    }

    /**
     *
     * @throws \dml_exception
     */
    private function collectDataStudentBy($student_id, $group_data)
    {
        $hasfindebt = sirius_student::check_hasfindebt($student_id);
        $leangroup = self::get_student_leangroup($student_id);
        $mod_info = $this -> get_grade_mod($student_id, $group_data -> id);
        $data = array('coursename' => $group_data -> coursename, 'courseurl' => $this -> url, 'mod_info' => $mod_info);
    }

    /**
     * @param $userid
     * @return false|string
     * @throws \dml_exception
     */
    private static function get_student_leangroup($userid)
    {
        global $DB;
        if ($student_leangroup_fieldid = $DB -> get_record('user_info_field', array('shortname' => 'studygroup'), 'id')) {
            if ($data = $DB -> get_record('user_info_data', array('fieldid' => $student_leangroup_fieldid -> id, 'userid' => $userid), 'data')) {
                if (!isset($data -> data))
                    return false;

                return trim($data -> data);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $obj_student
     * @param $obj_group
     * @param $selectList
     */
    public function setListByRequest($obj_student, $obj_group, $selectList)
    {
        ($selectList == "studentlist") ?
            $this -> setStudentList($obj_student) : $this -> setGroupsList($obj_group);
    }

    /**
     * @param $userid
     * @param $groupid
     * @return array
     */
    private function get_grade_mod($userid, $groupid): array
    {
        $course = ($this->database?:new databaseList())->getCourseFromDB($this->id);
        $modinfo_obj = new modinfo($course);
        return $modinfo_obj -> modinfo_data($userid, $groupid);
    }
}

class databaseList{

    public function getCourseFromDB($courseid)
    {
        global $DB;
        try {
            $course = $DB -> get_record('course', array('id' => $courseid));
        } catch (\dml_exception $e) {
            debugging(sprintf(" Cannot connect to external database: %s", $e -> getMessage()), DEBUG_DEVELOPER);
        }

        return $course;
    }
}