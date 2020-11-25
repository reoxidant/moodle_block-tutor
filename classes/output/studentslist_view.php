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
     * @param $student_id
     */
    public function get_students_by_select($student_id)
    {
        if (is_int($student_id)) {
            foreach ($groups_arr as $courseid => $val) {
                $course = $DB -> get_record('course', array('id' => $courseid));

                $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
                foreach ($val as $groupname => $group_data) {
                    $coursename = $group_data -> coursename;
                    $group_students = $this -> getGroupUsersByRole($group_data -> id, $courseid);
                    $this -> getReturnStudentsArr($group_students, $course, $courseurl, $coursename, $return_arr, $group_data, $groupname);
                }

                usort($return_arr['students'][$userid]['data'], array('self', 'cmp'));
            }
        }

        $this->generateHtmlList($return_arr);

        echo json_encode($return_arr);
    }

    private function generateStudentList()
    {
/*
            {{#student_leangroup}}
                (<i>{{student_leangroup}}</i>)
            {{/student_leangroup}}

            {{#groupname}}
                <small> - {{groupname}}</small>
            {{/groupname}}

            {{#hasfindebt}}
                <span class="hasfindebt_info">({{#str}} hasfindebt, block_tutor {{/str}})</span>
            {{/hasfindebt}}

            */
        return
            \html_writer::start_tag('ul').
                \html_writer::start_tag('li').

                    \html_writer::start_tag('a', array('href' => $studenturl, 'target' => '_blank')). $studentname. \html_writer::end_tag('a').

                    \html_writer::start_tag('i'). $student_leangroup. \html_writer::end_tag('i').

                    \html_writer::start_tag('small'). $groupname. \html_writer::end_tag('small').

                    \html_writer::start_tag('span', array('class' => 'hasfindebt_info')).
                        get_string($hasfindebt, 'block_tutor').
                    \html_writer::end_tag('span').

                    $this->getHtmlStudentData().

                \html_writer::end_tag('li').
            \html_writer::end_tag('ul');
    }

    private function getHtmlStudentData()
    {
        return
        \html_writer::start_tag('ul').
            \html_writer::start_tag('li').
        /*<ul>
                {{#data}}
                    <li><a href="{{{courseurl}}}" target="_blank">{{coursename}}</a>
                        {{#mod_info}}
                            {{#mod_url}}
                                - (<b><a href="{{mod_url}}&rownum=0&action=grader&userid={{userid}}&group={{groupid}}&treset=1"
                                target="_blank" title="{{#str}} gotosubmition, block_tutor {{/str}}">{{mod_grade}}</a></b>)
                            {{/mod_url}}
                            {{^mod_url}}
                                - (<b>{{mod_grade}}</b>)
                            {{/mod_url}}
                        {{/mod_info}}
                    </li>
                {{/data}}
            </ul>*/
                \html_writer::start_tag("b").
                    \html_writer::start_tag("a",
                        array(
                            'href' => "$mod_url&rownum=0&action=grader&userid={{userid}}&group={{groupid}}&treset=1",
                            'target' => '_blank',
                            'title' => get_string($gotosubmition, 'block_tutor')
                        )
                    ). 
                    \html_writer::end_tag("a").
                \html_writer::end_tag("b").

            \html_writer::end_tag('li').
        \html_writer::end_tag('ul');
    }


    private function generateGroupList()
    {
        return
        \html_writer::start_tag('div').
            \html_writer::start_tag('h5').$name.\html_writer::end_tag('h5').

        \html_writer::end_tag('div');
    }

    private function generateHtmlList($return_arr)
    {
        $html = \html_writer ::start_tag('ol');
        $html .= \html_writer ::end_tag('ol');

        //grouplist

//        <div>
//            <h5>{{name}}</h5>
//            <ul>
        $html .= $this->generateGroupList();
//            </ul>
//        </div>

        //studentlist

        /*
        <li class="studentrow {{#hasfindebt}}hasfindebt{{/hasfindebt}}">

        </li>*/


/*        $html = "<h1>Расписание дисциплин {$this->user->firstname} {$this->user->lastname}</h1>"
        . \html_writer::start_tag('div', array('class' => 'calendar_table', 'userid' => "{$this->user->id}")) .
        $this->getCalendar()
        . \html_writer::end_tag('div')
        . \html_writer::start_tag('div', array('class' => 'main_container_studtimetable'))
        . \html_writer::start_tag('div', array('class' => 'studtimetable')) .
        $this->getTableBodyHtml()
        . \html_writer::end_tag('div')
        . \html_writer::end_tag('div');*/

        $html .= \html_writer ::start_tag('input', array(
            'type' => "date",
            'class' => "input-start",
            'value' => ($this -> curCalendarDateMin) ? $this -> getDate($this -> curCalendarDateMin, true) : $this -> getDate(),
            'min' => "{$this->getDate()}",
            'max' => "{$this->getDate($this->maxDateCurrentUser, true)}"
        ));
        $html .= \html_writer ::end_tag('input');

        $html .= \html_writer ::start_tag('label', array('class' => "text-end"));
        $html .= "До:";
        $html .= \html_writer ::end_tag('label');
        $html .= \html_writer ::start_tag('input', array(
            'type' => "date",
            'class' => "input-end",
            'value' => ($this -> curCalendarDateMax) ? $this -> getDate($this -> curCalendarDateMax, true) : $this -> getDate($this -> maxDateCurrentUser, true),
            'min' => "{$this->getDate()}",
            'max' => "{$this->getDate($this->maxDateCurrentUser, true)}"
        ));
        $html .= \html_writer ::end_tag('input');

        return $html;
    }

    /**
     * @return array[]
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function get_students()
    {
        global $DB, $USER;

        //$sirius_student = new sirius_student;
        $groups_arr = $this -> getUserGroups();

        $return_arr = array('students' => array(), 'groups' => array());

        $this->getStudentsAndGroupListOfNamesForSelector($groups_arr, $return_arr);

        $this -> sortcmpby = 'studentname';
        usort($return_arr['students'], array('self', 'cmp'));

        // сбрасываем ключи для mustache
        $return_arr['groups'] = array_values($return_arr['groups']);
        foreach ($return_arr['groups'] as $key => $val) {
            $return_arr['groups'][$key]['students'] = array_values($val['students']);
        }

        return $return_arr;
    }

    private function getStudentsAndGroupListOfNamesForSelector($groups_arr, &$return_arr){
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
    }

    /**
     * @param $group_students
     * @param $course
     * @param $courseurl
     * @param $coursename
     * @param $return_arr
     * @param $group_data
     * @param $groupname
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function getReturnStudentsArr(&$group_students, &$course, &$courseurl, &$coursename, &$return_arr, &$group_data, &$groupname)
    {
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
