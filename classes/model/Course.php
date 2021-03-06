<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace model;

use dml_exception;
use sirius_student;

require_once("Group.php");
require_once("Student.php");
require_once("DatabaseManager.php");

/**
 * Class course
 * @package block_tutor\output
 */
class Course extends sirius_student
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
     * @var DatabaseManager
     */
    public DatabaseManager $database;
    /**
     * @var string
     */
    private string $sortcmpby;

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
     * @throws \moodle_exception
     */
    public function setCourseList(&$student_course)
    {
        foreach ($this -> groups as $groupname => $group_data) {

            $obj_group = new group($group_data -> id, $groupname);

            $obj_group -> students = $this -> getGroupUsersByRole($group_data -> id, $this -> id);

            foreach ($obj_group -> students as $userid => $profile) {

                $student_course[$userid][$group_data -> courseid]["course_data"] = $group_data;
                $student_course[$userid][$group_data -> courseid]["groupid"][] = $group_data -> id;
                $obj_student = new student($userid, $profile -> name, $profile -> profileurl, $groupname, $student_course[$userid]);

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
     * @return array
     */
    public function SortAndReturnListData(): array
    {
        $this -> sortStudentArr();
        $this -> resetKeysMustacheTemplate();

        return $this -> listData;
    }

    /**
     * @param $return_arr
     */
    private function sortStudentArr()
    {
        $this -> sortcmpby = 'studentname';
        if (isset($this -> listData['students']))
            usort($this -> listData['students'], array('self', 'cmp'));
    }

    /**
     * @param $a
     * @param $b
     * @return int|\lt
     */
    private function cmp($a, $b)
    {
        return strcasecmp(mb_strtolower($a[$this -> sortcmpby]), mb_strtolower($b[$this -> sortcmpby]));
    }

    /**
     * @param $return_arr
     */
    private function resetKeysMustacheTemplate()
    {
        if (isset($this -> listData['groups'])) {
            $this -> listData['groups'] = array_values($this -> listData['groups']);
            foreach ($this -> listData['groups'] as $key => $val) {
                if (isset($key, $val['students'])) {
                    $this -> listData['groups'][$key]['students'] = array_values($val['students']);
                }
            }
        }
    }
}