<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace controller;

use model\Student;
use moodle_url;
use sirius_student;
use view\StrategyGroupView;
use view\StrategyStudentView;

require_once($_SERVER['DOCUMENT_ROOT'] . "/blocks/tutor/classes/view/StrategyStudentView.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/blocks/tutor/classes/view/StrategyGroupView.php");

/**
 * Class StrategyAjax
 * @package Strategy
 */
class StrategyAjaxViewController extends sirius_student implements Strategy
{
    /**
     * @var int
     */
    private int $chosen_id;
    /**
     * @var string
     */
    private string $select_list;

    /**
     * StrategyAjax constructor.
     * @param $student_id
     * @param $select_list
     */
    public function __construct($chosen_id, $select_list)
    {
        $this -> chosen_id = $chosen_id;
        $this -> select_list = $select_list;
    }

    /**
     * @return array
     * @throws \dml_exception
     */
    public function get_students(): array
    {
        $student_id = $this -> chosen_id;
        $student = new student($student_id, null, null);
        $coursesAndGroups = $student -> getStudentCoursesById($student_id);
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

        return (array)$student;
    }

    /**
     * @param $selectList
     * @param $data
     */
    public function showDataOnThePage($data)
    {
        if ($this -> select_list == "studentlist") {
            $studentView = new StrategyStudentView($data);
            $studentView -> generateStudentList();
        } else {
            $groupView = new StrategyGroupView($data);
            $groupView -> generateGroupList();
        }
        echo $studentView -> html ?? $groupView -> html;
    }
}