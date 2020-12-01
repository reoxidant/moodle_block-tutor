<?php

namespace block_tutor\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use sirius_student;

//require_once($CFG->dirroot . "/local/customlib.php");

global $CFG;

if (is_file($CFG -> dirroot . '/local/student_lib/locallib.php')) {
    require_once($CFG -> dirroot . '/local/student_lib/locallib.php');
}

/**
 * Class studentslist_view
 * @package block_tutor\output
 */
class studentslist_view extends sirius_student
{
    /**
     * @var string
     */
    private $sortcmpby = 'coursename'; // для функции сортировки массива

    /**
     * @param $output
     * @return array[]
     */
/*    public function export_for_template($output)
    {
        return $this -> get_students();
    }*/

    /**
     * @return array[]
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function get_students()
    {
        $data = $this -> getStudentAndGroupData($studentId = null, $selectList = null, $isRequest = false);

        $this -> sortStudentArr($data);

        $this -> resetKeysMustacheTemplate($data);

        return $data;
    }


    /**
     * @param $studentid
     * @param $isReguest
     * @return array[]
     */
    public function getStudentAndGroupData($studentid, $selectList, $isRequest)
    {
        if($selectList == "studentlist")
        {

        }
        else if($studentid == "grouplist")
        {

        }

        $course_arr = $isRequest ? $this -> getUserCoursesAndGroupsById($studentid) : $this -> getUserGroups();

        return $isRequest ? $this -> handleGroupArrayBy($course_arr, $request_data) : $this -> getSelectorData($course_arr);
    }

    /**
     * @param $course_arr
     * @return array[]
     * @throws \moodle_exception
     */
    private function handleGroupArrayBy($course_arr, $request_data): array
    {
        global $DB, $USER;

        $return_arr = array('students' => array(), 'groups' => array());

        list($studentid, $selectList) = $request_data;

        foreach ($course_arr as $courseid => $group_arr) {

            $course = $DB -> get_record('course', array('id' => $courseid));
            $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

            foreach ($group_arr as $groupname => $group_data) {
                $coursename = $group_data -> coursename;
                $userData = $this -> getGroupUsersByRole($group_data -> id, $courseid, $studentid);

                $mod_info = $this -> get_grade_mod($course, $userid, $group_data -> id);
                $hasfindebt = sirius_student ::check_hasfindebt($userid);
                $student_leangroup = self ::get_student_leangroup($userid);

                $data = array('userid' => $userid, 'coursename' => $coursename, 'courseurl' => $courseurl, 'mod_info' => $mod_info);

                $studentinfo = [
                    'studentname' => $studentProfile -> name,
                    'studenturl' => $studentProfile -> profileurl,
                    'hasfindebt' => $hasfindebt,
                    'groupname' => $groupname,
                    'student_leangroup' => $student_leangroup
                ];

                if($selectList == 'studentlist'){
                    $return_arr['students'][$userid] = $studentinfo;
                    $return_arr['students'][$userid]['data'][] = $data;
                }else{
                    $return_arr['groups'][$groupname]['students'][$userid] = $studentinfo;
                    $return_arr['groups'][$groupname]['name'] = $groupname;
                }
            }
        }

        return $return_arr;
    }

    /**
     * @param $groups_arr
     * @param $return_arr
     */


    /**
     * @param $student_id
     * @param $selectList
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */



    /**
     * @param $return_arr
     */
    private function sortStudentArr(&$return_arr)
    {
        $this -> sortcmpby = 'studentname';
        usort($return_arr['students'], array('self', 'cmp'));
    }

    /**
     * @param $return_arr
     */
    private function resetKeysMustacheTemplate(&$return_arr)
    {
        // сбрасываем ключи для mustache
        $return_arr['groups'] = array_values($return_arr['groups']);
        foreach ($return_arr['groups'] as $key => $val) {
            $return_arr['groups'][$key]['students'] = array_values($val['students']);
        }
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

    // поиск в курсе первого assign или quiz и возврат по нему оценки с данными для перехода
    // вместе с оценкой
    /**
     * @param $course
     * @param $userid
     * @param $groupid
     * @return array
     * @throws \moodle_exception
     */
    private function get_grade_mod($course, $userid, $groupid)
    {
        $modinfo_obj = get_fast_modinfo($course);
        $cms = $modinfo_obj -> cms;

        $return_arr = array();

        foreach ($cms as $mod) {
            $modname = $mod -> modname;

            if ($this -> check_mod_capability($mod) && ($modname == 'assign' || $modname == 'quiz')) {
                $mod_grade = grade_get_grades($course -> id, 'mod', $modname, $mod -> instance, $userid);
                @$mod_grade = current($mod_grade -> items[0] -> grades) -> grade;
                if (empty($mod_grade))
                    continue;

                $url = null;
                if ($modname == 'assign')
                    $url = $mod -> url;

                $return_arr['mod_grade'] = (string)intval($mod_grade);
                $return_arr['mod_url'] = $url;
                $return_arr['modname'] = $modname;
                $return_arr['groupid'] = $groupid;

                break;
            }
        }

        return $return_arr;
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
}