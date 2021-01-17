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
     * @var array
     */
    public array $coursedata;

    /**
     * student constructor.
     * @param $studentid
     * @param $studentname
     * @param $studenturl
     * @throws dml_exception
     */
    public function __construct($studentid, $studentname, $studenturl)
    {
        $this -> studentid = $studentid;
        $this -> studentname = $studentname ? $studentname : fullname((new DatabaseManager()) -> getStudentBy($studentid));
        $this -> studenturl = $studenturl ? $studenturl : new moodle_url('/user/profile.php', array('id' => $studentid));
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
            $this -> leangroup = trim($data -> data);
        }
    }

    /**
     * @param $userid
     * @param $groupid
     * @throws dml_exception
     */
    public function set_mod_info($courseid)
    {
        $course = (new DatabaseManager()) -> getCourseBy($courseid);
        return (new modinfo($course)) -> modinfo_data($this -> studentid, $group -> id);
    }

    /**
     * @param $coursename
     * @param $courseurl
     */
    public function set_course_data($coursename, $courseurl, $modinfo)
    {
        $this -> coursedata[] = ['userid' => $this -> studentid, 'coursename' => $coursename, 'courseurl' => $courseurl, 'mod_info' => $modinfo];
    }

    /**
     *
     */
    public function check_student_hasfindebt()
    {
        $this -> hasfindebt = sirius_student ::check_hasfindebt($this -> studentid);
    }

    /**
     * @param $student_id
     * @return array
     * @throws \dml_exception
     */
    public function getStudentCoursesById($student_id): array
    {
        $arrCourses = array();

        foreach ($this -> db_course_records_by($student_id) as $key => $value) {
            $arrCourses[$value -> courseid][$value -> name] = $value;
        }

        return $arrCourses;
    }

    /**
     * @param $student_id
     * @return array
     * @throws \dml_exception
     */
    private function db_course_records_by($student_id): array
    {
        global $DB;

        $sql = "SELECT g.id, g.courseid, g.name, c.fullname as coursename
                  FROM
                    {groups} g,
                    {groups_members} gm,
                    {course} c
                  WHERE 
                    g.id = gm.groupid
                    AND c.id = g.courseid
                    AND gm.userid = $student_id
                  ORDER BY g.name, c.fullname;";

        return $DB -> get_records_sql($sql, array($student_id));
    }
}