<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace model;

use dml_exception;
use moodle_url;
use sirius_student;

require_once("Modinfo.php");
require_once("DatabaseManager.php");

/**
 * Class student
 * @package block_tutor\output
 */
class Student extends sirius_student
{
    /**
     * @var int
     */
    public int $studentid;
    /**
     * @var string
     */
    public string $studentname;
    /**
     * @var string
     */
    public string $studenturl;
    /**
     * @var bool
     */
    public bool $hasfindebt;

    /**
     * @var string
     */
    public string $leangroup;


    /**
     * @var string|mixed
     */
    public string $groupname;

    /**
     * @var array
     */
    public array $studentdata;

    public array $mod_data = array();

    /**
     * student constructor.
     * @param $studentid
     * @param $studentname
     * @param $studenturl
     * @throws dml_exception
     * @throws \moodle_exception
     */
    public function __construct($studentid, $studentname, $studenturl, $groupname = "", $courses = array())
    {
        $this -> studentid = $studentid;
        $this -> studentname = $studentname ? $studentname : fullname((new DatabaseManager()) -> getStudentBy($studentid));
        $this -> studenturl = $studenturl ? $studenturl : new moodle_url('/user/profile.php', array('id' => $studentid));
        $this -> groupname = $groupname;
        $this -> studentdata = $courses;
    }

    /**
     *
     * @throws dml_exception
     * @throws dml_exception
     */
    public function set_student_leangroup()
    {
        $leangroup_field_id = ((new DatabaseManager()) -> getStudentLeanGroup()) -> id;
        $data = (new DatabaseManager()) -> getUserInfoBy($leangroup_field_id, $this -> studentid);

        if ($leangroup_field_id && isset($data -> data)) {
            $this->leangroup = trim($data -> data);
        }
    }

    /**
     * @param $courseid
     * @param $groupid
     * @return array
     * @throws dml_exception
     */
    public function set_mod_info($courseid, $groupid)
    {
        $course = (new DatabaseManager()) -> getCourseBy($courseid);
        $this->mod_data = (new modinfo($course)) -> modinfo_data($this -> studentid, $groupid);
    }

    /**
     *
     */
    public function check_student_hasfindebt()
    {
        $this->hasfindebt = sirius_student ::check_hasfindebt($this -> studentid);
    }

    /**
     * @param $studentsCache
     */
    public function get_student_data_from_cache($studentsCache)
    {
        foreach ($studentsCache as $student) {
            if ($student["studentid"] == $this -> studentid) {
                $this -> groupname = $student['groupname'];
                $this -> studentdata = $student['studentdata'];
            } else {
                continue;
            }
        }
    }
}