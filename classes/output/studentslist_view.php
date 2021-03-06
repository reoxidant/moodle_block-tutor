<?php

namespace block_tutor\output;
defined('MOODLE_INTERNAL') || die();

use moodle_url;
use sirius_student;

//require_once($CFG->dirroot . "/local/customlib.php");
require_once($CFG -> dirroot . '/local/student_lib/locallib.php');

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
    public function export_for_template($output): array
    {
        try {
            return $this -> get_students();
        } catch (\dml_exception | \moodle_exception $e) {
            // Catch the re-thrown exception.
        }
    }

    /**
     * @return array[]
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function get_students(): array
    {
        global $DB, $USER;

        //$sirius_student = new sirius_student;
        $groups_arr = $this -> getUserGroups();

        $return_arr = array('students' => array(), 'groups' => array());

        // if($USER->id == 17810 && isset($USER->realuser) && $USER->realuser == 26102){
        // print_r($groups_arr);die;
        // }
        foreach ($groups_arr as $courseid => $val) {
            $course = $DB -> get_record('course', array('id' => $courseid));

            $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
            foreach ($val as $groupname => $group_data) {

                $coursename = $group_data -> coursename;
                $group_students = $this -> getGroupUsersByRole($group_data -> id, $courseid);
                foreach ($group_students as $userid => $profile) {
                    $studentname = $profile -> name;
                    $profileurl = $profile -> profileurl;


                    $mod_info = $this -> get_grade_mod($course, $userid, $group_data -> id);

                    $courseurl_return = $courseurl;
                    // для письменных работ подменяем ссылку на попытку студента
                    //if(isset($mod_info['modname']) && $mod_info['modname'] == 'assign')
                    //	$courseurl_return = $mod_info['mod_url'] . '&action=grader&userid=' . $userid;

                    $data = array('userid' => $userid, 'coursename' => $coursename, 'courseurl' => $courseurl_return, 'mod_info' => $mod_info);

                    // проверка на фин долг
                    $curuser_hasfindebt = sirius_student ::check_hasfindebt($userid);
                    $student_leangroup = self ::get_student_leangroup($userid);
                    $return_arr['students'][$userid]['studentname'] = $studentname;
                    $return_arr['students'][$userid]['studenturl'] = $profileurl;
                    $return_arr['students'][$userid]['hasfindebt'] = $curuser_hasfindebt;
                    $return_arr['students'][$userid]['groupname'] = $groupname;
                    $return_arr['students'][$userid]['student_leangroup'] = $student_leangroup;
                    $return_arr['students'][$userid]['data'][] = $data;

                    $return_arr['groups'][$groupname]['students'][$userid]['studentname'] = $studentname;
                    $return_arr['groups'][$groupname]['students'][$userid]['studenturl'] = $profileurl;
                    $return_arr['groups'][$groupname]['students'][$userid]['hasfindebt'] = $curuser_hasfindebt;
                    $return_arr['groups'][$groupname]['students'][$userid]['student_leangroup'] = $student_leangroup;
                    $return_arr['groups'][$groupname]['students'][$userid]['data'][] = $data;
                    $return_arr['groups'][$groupname]['name'] = $groupname;
                }
            }

            usort($return_arr['students'][$userid]['data'], array('self', 'cmp'));
        }
        $this -> sortcmpby = 'studentname';
        usort($return_arr['students'], array('self', 'cmp'));

        // сбрасываем ключи для mustache
        $return_arr['groups'] = array_values($return_arr['groups']);
        foreach ($return_arr['groups'] as $key => $val) {
            $return_arr['groups'][$key]['students'] = array_values($val['students']);
        }

        $debugData = $return_arr;

        return $return_arr;
    }

    // сортировка студентов

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
    private function get_grade_mod($course, $userid, $groupid): array
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
