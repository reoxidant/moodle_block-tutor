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
        //recursion - 2.1351430416107, 3.3620500564575, 3.0881190299988, 3.3064370155334, 3.1104788780212 14,98
        //for loop - 1.9431018829346, 3.115350961685, 3.126620054245, 3.2085390090942, 3.1251590251923 14,49
        $start = microtime(true);

        $course_data = $this -> getUserGroups();

        $course = new Course();

        foreach ($course_data as $courseid => $group) {
            $course -> id = $courseid;
            $course -> url = new moodle_url('/course/view.php', array('id' => $courseid));
            $course -> groups = $group;
            $course -> setCourseList();
        }

        $time = microtime(true) - $start;
        \core\notification::warning("$time - sec perform operation");

        return $course -> SortAndReturnListData();
    }
}