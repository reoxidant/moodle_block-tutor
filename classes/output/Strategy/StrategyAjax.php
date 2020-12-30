<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace Strategy;

use block_tutor\output\course;
use block_tutor\output\group;
use block_tutor\output\Strategy;
use block_tutor\output\student;
use moodle_url;
use sirius_student;

/**
 * Class StrategyAjax
 * @package Strategy
 */
class StrategyAjax extends sirius_student implements Strategy
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
        $coursesAndGroups = $this -> getStudentCoursesById($this->student_id);

        $course = new course();

        if ($this->select_list == "studentlist"){
            $student = $this->collectDataStudentBy($this->student_id);
            foreach ($coursesAndGroups as $courseid => $groups) {
                $course -> id = $courseid;
                $course -> url = new moodle_url('/course/view.php', array('id' => $course -> id));

                foreach ($groups as $group)
                {
                    $modinfo = $student -> set_mod_info($course -> id);
                    $student -> set_course_data($group -> coursename, $course -> url, $modinfo);
                    $course -> setStudentList($student);
                }
            }
            $datastudent = $student;
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
                        $studentname = $profile->name;
                        $profileurl = $profile->profileurl;

                        $mod_info = $this->get_grade_mod($course, $userid, $group_data->id);

                        $courseurl_return = $courseurl;

                        $data = Array('userid' => $userid, 'coursename' => $coursename, 'courseurl' => $courseurl_return, 'mod_info' => $mod_info);

                        $return_arr['students'][$userid]['data'][] = $data;
                    }
                }
            }

            usort($return_arr['students'][$userid]['data'], array('self', 'cmp'));
        }

        */

        return [] ?? $return_arr;
    }


    private function get_grade_mod($course, $userid, $groupid)
    {
        $modinfo_obj = get_fast_modinfo($course);
        $cms = $modinfo_obj->cms;

        $return_arr = Array();

        foreach ($cms as $mod) {
            $modname = $mod->modname;

            if ($this->check_mod_capability($mod) && ($modname == 'assign' || $modname == 'quiz')) {
                $mod_grade = grade_get_grades($course->id, 'mod', $modname, $mod->instance, $userid);
                @$mod_grade = current($mod_grade->items[0]->grades)->grade;
                if (empty($mod_grade))
                    continue;

                $url = null;
                if ($modname == 'assign')
                    $url = $mod->url;

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
        $student = new student($student_id, null, null);
        $student -> check_student_hasfindebt();
        $student -> set_student_leangroup();
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

        foreach ($this -> db_course_records_by($student_id) as $key => $value)
        {
            $arrCourses[$value -> courseid][$value->name] = $value;
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