<?php

namespace block_tutor\output;
defined('MOODLE_INTERNAL') || die();

use sirius_student;
use Strategy\StrategySelectList;

require_once('Strategy/StrategySelectList.php');
require_once('course.php');

/**
 * Interface Strategy
 * @package block_tutor\output
 */
interface Strategy
{
    /**
     * @return array
     */
    public function get_students(): array;
}

/**
 * Class studentslist_view
 * @package block_tutor\output
 */
class studentslist_view extends sirius_student implements Strategy
{
    /**
     * @var string
     */
    private $sortcmpby = 'coursename'; // для функции сортировки массива

    /**
     * @var
     */
    public $strategy;

    /**
     * @param Strategy $strategy
     */
    public function setStrategy(Strategy $strategy)
    {
        $this -> strategy = $strategy;
    }

    /**
     * @param $output
     * @return array
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_for_template($output)
    {
        return $this -> get_students();
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

    /**
     * @return array
     */
    public function get_students() : array
    {
        $this -> setStrategy(new StrategySelectList());
        $students = $this -> strategy -> get_students();

        $this -> sortStudentArr($students);

        $this -> resetKeysMustacheTemplate($students);

        return $students;
    }

    /**
     * @param $return_arr
     */
    private function sortStudentArr(&$return_arr)
    {
        $this -> sortcmpby = 'studentname';
        if(isset($return_arr['students']))
            usort($return_arr['students'], array('self', 'cmp'));
    }

    /**
     * @param $return_arr
     */
    private function resetKeysMustacheTemplate(&$return_arr)
    {
        // сбрасываем ключи для mustache

        if(isset($return_arr['groups']))
        {
            $return_arr['groups'] = array_values($return_arr['groups']);
            foreach ($return_arr['groups'] as $key => $val) {
                if(isset($key, $val['students']))
                {
                    $return_arr['groups'][$key]['students'] = array_values($val['students']);
                }
            }
        }
    }
}
