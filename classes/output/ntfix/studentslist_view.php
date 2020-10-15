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

class studentslist_view extends sirius_student
{
    private $sortcmpby = 'coursename'; // для функции сортировки массива
    private $arCourseData = array();

    public function export_for_template($output)
    {
        return $this -> get_students();
    }

    private function get_students()
    {
        $start = microtime(true);

        $return_arr = array('students' => array(), 'groups' => array());

        $this->arCourseData();

        /*foreach ($courses as $courseid => $courseData)
        {
            $modinfo_obj = get_fast_modinfo($courseData["course_db_record"]);
            $cms = $modinfo_obj -> cms;
            foreach ($cms as $mod)
            {
                $modname = $mod -> modname;
                $usersid = array_keys($courseData["users_id"]);
                $arUsers = array_combine($usersid, $usersid);

                $mod_grade = grade_get_grades($courseid, 'mod', $modname, $mod -> instance, $arUsers);
                @$mod_grade = current($mod_grade -> items[0] -> grades) -> grade;

                if (empty($mod_grade))
                    continue;

                $url = null;
                if ($modname == 'assign')
                    $url = $mod -> url;

                $return_arr['mod_grade'] = (string)intval($mod_grade);
                $return_arr['mod_url'] = $url;
                $return_arr['modname'] = $modname;
                $return_arr['groupid'] = $courseData["users_id"][$courseid];
                break;
            }
        }*/

        /*      $this -> sortcmpby = 'studentname';
              usort($return_arr['students'], array('self', 'cmp'));

              // сбрасываем ключи для mustache
              $return_arr['groups'] = array_values($return_arr['groups']);
              foreach ($return_arr['groups'] as $key => $val) {
                  $return_arr['groups'][$key]['students'] = array_values($val['students']);
              }*/
        $time = microtime(true) - $start;
        \core\notification::warning($time);
        return $return_arr;
    }

    private function arCourseData()
    {
        $this->arCourseData = $this -> getUserGroups();
    }

    private function courseUsersData($group_data, $courseid)
    {
        foreach ($group_students as $userid => $profile) {
            $arCourseData[$courseid]["users_id"][$userid]['name'] = $profile -> name;
            $arCourseData[$courseid]["users_id"][$userid]['profileurl'] = $profile -> profileurl;
            $arCourseData[$courseid]["users_id"][$userid]['hasfindebt'] = sirius_student ::check_hasfindebt($userid);
            $arCourseData[$courseid]["users_id"][$userid]['in_group'] = sirius_student ::get_student_leangroup($userid);
        }
    }

    // сортировка студентов
    private function cmp($a, $b)
    {
        return strcasecmp(mb_strtolower($a[$this -> sortcmpby]), mb_strtolower($b[$this -> sortcmpby]));
    }

    // поиск в курсе первого assign или quiz и возврат по нему оценки с данными для перехода
    // вместе с оценкой
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
