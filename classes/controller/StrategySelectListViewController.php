<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace controller;

use model\Course;
use moodle_url;
use sirius_student;

global $CFG;
require_once($CFG -> dirroot . '/blocks/tutor/classes/model/Course.php');

/**
 * Class StrategySelectList
 * @package Strategy
 */
class StrategySelectListViewController extends sirius_student implements Strategy
{
    /**
     * @return array[]
     * @throws \moodle_exception
     */
    public function get_students(): array
    {
        $course_data = $this -> getUserGroups();

        $course = new Course();

        foreach ($course_data as $courseid => $group) {
            $course -> id = $courseid;
            $course -> url = new moodle_url('/course/view.php', array('id' => $courseid));
            $course -> groups = $group;
            $course -> setCourseList();
        }

        return $course -> SortAndReturnListData();
    }
}