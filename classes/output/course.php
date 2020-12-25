<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace block_tutor\output;

use dml_exception;
use sirius_student;

require_once("group.php");
require_once("student.php");
require_once("databaseList.php");

/**
 * Class course
 * @package block_tutor\output
 */
class course extends sirius_student
{
    /**
     * @var int
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

    /**
     * @var array|array[]
     */
    public array $arrResultData = array('students' => array(), 'groups' => array());

    /**
     * @var dataBaseList
     */
    public dataBaseList $database;

    /**
     * @param $student
     */
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
     * @throws dml_exception
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
     * @return array
     * @return array
     * @throws dml_exception
     */
    public function setCourseListByRequest($student_id, $select_list): array
    {
        if ($select_list == "studentlist"){
            $this->collectDataStudentBy($student_id);
        } else {
            var_dump("It not time yet!");
        }

        return $this -> listData;
    }

    /**
     *
     * @throws dml_exception
     */
    private function collectDataStudentBy($student_id)
    {
        $student = new student($student_id, null, null);
        $student -> check_student_hasfindebt();
        $student -> set_student_leangroup();
        $student -> set_grade_mod($this -> id, $group_data -> id);
        $student -> set_course_data($group_data -> coursename, $this -> url);
        $this -> setStudentList($student);
    }
}