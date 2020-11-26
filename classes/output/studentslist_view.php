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
    public function export_for_template($output)
    {
        return $this -> get_students();
    }

    /**
     * @return array[]
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function get_students()
    {
        $data = $this -> generateMainDataBy($studentid, false);

        $this -> sortStudentArr($data);

        $this -> resetKeysMustacheTemplate($data);

        return $data;
    }


    /**
     * @param $studentid
     * @param $isReguest
     * @return array[]
     */
    private function generateMainDataBy($studentid, $isReguest)
    {
        global $DB, $USER;

        $course_arr = $isReguest ? $this -> getUserCoursesAndGroupsById($studentid) : $this -> getUserGroups();

        return $isReguest ? $this -> handleGroupArrayBy($course_arr) : $this -> getSelectorData($course_arr);
    }

    /**
     * @param $course_arr
     * @return array[]
     * @throws \moodle_exception
     */
    private function handleGroupArrayBy($course_arr): array
    {
        $return_arr = array('students' => array(), 'groups' => array());

        foreach ($course_arr as $courseid => $group_arr) {

            $course = $DB -> get_record('course', array('id' => $courseid));
            $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

            foreach ($group_arr as $groupname => $group_data) {

                $coursename = $group_data -> coursename;
                $group_students = $this -> getGroupUsersByRole($group_data -> id, $courseid);

                $this -> handleStudentsBy($group_students, $course, $courseurl, $coursename, $return_arr);
            }
        }

        return $return_arr;
    }

    /**
     * @param $group_students
     * @param $course
     * @param $courseurl
     * @param $coursename
     * @param $return_arr
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function handleStudentsBy($group_students, $course, $courseurl, $coursename, &$return_arr)
    {
        foreach ($group_students as $userid => $profile) {
            $studentname = $profile -> name;
            $profileurl = $profile -> profileurl;

            $mod_info = $this -> get_grade_mod($course, $userid, $group_data -> id);

            $courseurl_return = $courseurl;

            $data = array('userid' => $userid, 'coursename' => $coursename, 'courseurl' => $courseurl_return, 'mod_info' => $mod_info);

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

    /**
     * @param $groups_arr
     * @param $return_arr
     */
    private function getSelectorData($groups_arr): array
    {
        $return_arr = array('students' => array(), 'groups' => array());

        foreach ($groups_arr as $courseid => $val) {
            foreach ($val as $groupname => $group_data) {
                $group_students = $this -> getGroupUsersByRole($group_data -> id, $courseid);
                foreach ($group_students as $userid => $profile) {
                    $studentname = $profile -> name;

                    $return_arr['students'][$userid]['studentname'] = $studentname;
                    $return_arr['students'][$userid]['userid'] = $userid;
                    $return_arr['groups'][$groupname]['name'] = $groupname;
                    $return_arr['groups'][$groupname]['groupid'] = $group_data -> id;
                }
            }
        }

        return $return_arr;
    }

    /**
     * @param $student_id
     * @param $selectList
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function get_students_by_request($student_id, $selectList)
    {
        global $DB, $USER;

        try {
            if (is_int($student_id) && is_string($selectList)) {
                $course_data = $this -> getUserCoursesAndGroupsById($student_id);

                foreach ($course_data as $courseid => $group) {
                    $course = $DB -> get_record('course', array('id' => $courseid));
                    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

                    foreach ($group as $groupname => $group_data) {
                        $coursename = $group_data -> coursename;
                        $group_students = $this -> returnArr($group_data -> id, $courseid);
                        /*$this -> getReturnStudentsArr($group_students, $course, $courseurl, $coursename, $return_arr, $group_data, $groupname);*/
                    }
                    usort($return_arr['students'][$userid]['data'], array('self', 'cmp'));
                }
            }

            //$this -> generateHtmlList($return_arr, $selectList);

            echo json_encode($return_arr);

        } catch (Exception $e) {
            echo 'Выброшено исключение: ', $e -> getMessage(), "\n";
        }
    }

    /**
     * @return string
     * @throws \coding_exception
     */
    private function generateStudentList($return_arr)
    {
        return
            \html_writer ::start_tag('ul') .
            \html_writer ::start_tag('li', array('class' => 'studentrow')) .

            \html_writer ::start_tag('a', array('href' => $studenturl, 'target' => '_blank')) . $studentname . \html_writer ::end_tag('a') .

            \html_writer ::start_tag('i') . $student_leangroup . \html_writer ::end_tag('i') .

            \html_writer ::start_tag('small') . $groupname . \html_writer ::end_tag('small') .

            \html_writer ::start_tag('span', array('class' => 'hasfindebt_info')) .
            get_string("hasfindebt", 'block_tutor') .
            \html_writer ::end_tag('span') .

            $this -> getHtmlStudentData() .

            \html_writer ::end_tag('li') .
            \html_writer ::end_tag('ul');
    }

    /**
     * @return string
     * @throws \coding_exception
     */
    private function getHtmlStudentData()
    {
        return
            \html_writer ::start_tag('ul') .
            \html_writer ::start_tag('li') .
            \html_writer ::start_tag("b") .
            \html_writer ::start_tag("a",
                array(
                    'href' => "$mod_url&rownum=0&action=grader&userid={{userid}}&group={{groupid}}&treset=1",
                    'target' => '_blank',
                    'title' => get_string("gotosubmition", 'block_tutor')
                )
            ) .
            \html_writer ::end_tag("a") .
            \html_writer ::end_tag("b") .
            \html_writer ::start_tag("b") . $modgrade . \html_writer ::end_tag("b") .
            \html_writer ::end_tag('li') .
            \html_writer ::end_tag('ul');
    }


    /**
     * @return string
     * @throws \coding_exception
     */
    private function generateGroupList($return_arr)
    {
        return
            \html_writer ::start_tag('div') .
            \html_writer ::start_tag('h5') . $name . \html_writer ::end_tag('h5') .
            $this -> generateStudentList($return_arr) .
            \html_writer ::end_tag('div');
    }

    /**
     * @param $return_arr
     * @param $requestTabName
     * @return string
     * @throws \coding_exception
     */
    private function generateHtmlList($return_arr, $selectList)
    {
        if ($selectList === "grouplist") {
            $html = \html_writer ::start_tag('ol') . $this -> generateGroupList($return_arr) . \html_writer ::end_tag('ol');
        } else if ($selectList === "studentlist") {
            $html = \html_writer ::start_tag('ol') . $this -> generateStudentList($return_arr) . \html_writer ::end_tag('ol');
        }

        return $html;
    }


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