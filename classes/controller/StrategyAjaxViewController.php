<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace controller;

use controller\StudentViewController\Strategy;
use model\Student;
use moodle_url;
use sirius_student;

/**
 * Class StrategyAjax
 * @package Strategy
 */
class StrategyAjaxViewController extends sirius_student implements Strategy
{
    /**
     * @var int
     */
    private int $student_id;
    /**
     * @var string
     */
    private string $select_list;

    /**
     * StrategyAjax constructor.
     * @param $student_id
     * @param $select_list
     */
    public function __construct($student_id, $select_list)
    {
        $this -> student_id = $student_id;
        $this -> select_list = $select_list;
    }

    /**
     * @return array
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function get_students(): array
    {

        if ($this -> select_list == "studentlist") {
            $student = $this -> collectDataStudentBy($this -> student_id);
        } else {
            var_dump("It not time yet!");
        }

        //what need for data view

        //$curuser_hasfindebt = sirius_student::check_hasfindebt($userid);
        //$student_leangroup = self::get_student_leangroup($userid);
        //$mod_info = $this->get_grade_mod($course, $userid, $group_data->id);
        //$data = Array('userid' => $userid, 'coursename' => $coursename, 'courseurl' => $courseurl_return, 'mod_info' => $mod_info);

        /*

        $groups_arr = $this->getUserGroups();

        // if($USER->id == 17810 && isset($USER->realuser) && $USER->realuser == 26102){
        // print_r($groups_arr);die;
        // }

        global $DB;

        foreach ($groups_arr as $courseid => $val) {
            $course = $DB->get_record('course', array('id' => $courseid));

            $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
            foreach ($val as $groupname => $group_data) {

                $coursename = $group_data->coursename;
                $group_students = $this->getGroupUsersByRole($group_data->id, $courseid);
                foreach ($group_students as $userid => $profile) {

                    $needuserid = 40736;

                    if($needuserid == $userid)
                    {
//                        $studentname = $profile->name;
//                        $profileurl = $profile->profileurl;
//
//                        $mod_info = $this->get_grade_mod($course, $userid, $group_data->id);
//
//                        $courseurl_return = $courseurl;
//
//                        $data = Array('userid' => $userid, 'coursename' => $coursename, 'courseurl' => $courseurl_return, 'mod_info' => $mod_info);
//
//                        $return_arr['students'][$userid]['data'][] = $data;

                        $studentname = $profile->name;
                        $profileurl = $profile->profileurl;


                        $mod_info = $this->get_grade_mod($course, $userid, $group_data->id);

                        $courseurl_return = $courseurl;
                        // для письменных работ подменяем ссылку на попытку студента
                        //if(isset($mod_info['modname']) && $mod_info['modname'] == 'assign')
                        //	$courseurl_return = $mod_info['mod_url'] . '&action=grader&userid=' . $userid;

                        $data = Array('userid' => $userid, 'coursename' => $coursename, 'courseurl' => $courseurl_return, 'mod_info' => $mod_info);


                        $student = new student($userid, null, null);
                        $curuser_hasfindebt = $student -> check_student_hasfindebt();
                        $student_leangroup = $student -> set_student_leangroup();
                        // проверка на фин долг

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
            }

            usort($return_arr['students'][$userid]['data'], array('self', 'cmp'));
        }
        $this->sortcmpby = 'studentname';
        usort($return_arr['students'], array('self', 'cmp'));

        // сбрасываем ключи для mustache
        $return_arr['groups'] = array_values($return_arr['groups']);
        foreach ($return_arr['groups'] as $key => $val) {
            $return_arr['groups'][$key]['students'] = array_values($val['students']);
        }
        */

        return [] ?? $return_arr;
    }


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
     *
     * @throws dml_exception
     * @throws \dml_exception
     * @throws \dml_exception
     */
    private function collectDataStudentBy($student_id): student
    {
        $coursesAndGroups = $this -> getStudentCoursesById($student_id);

        $student = new student($student_id, null, null);
        $student -> check_student_hasfindebt();
        $student -> set_student_leangroup();

        foreach ($coursesAndGroups as $courseid => $groups) {
            $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
            foreach ($groups as $group) {
                $modinfo = $student -> set_mod_info($courseid);
                $student -> set_course_data($group -> coursename, $courseurl, $modinfo);
            }
        }

        usort($student -> coursedata, array('self', 'cmp'));

        return $student;
    }

    /**
     * @param $student_id
     * @return array
     * @throws \dml_exception
     */
    private function getStudentCoursesById($student_id): array
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