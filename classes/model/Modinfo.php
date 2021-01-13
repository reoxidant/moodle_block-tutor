<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace model;

use sirius_student;

/**
 * Class modinfo
 * @package block_tutor\output
 */
class Modinfo extends sirius_student
{
    private ?\course_modinfo $course_mod_info;
    private array $cms;
    private int $courseid;

    public function __construct($course)
    {
        $this -> courseid = $course -> id;
        try {
            $this -> course_mod_info = get_fast_modinfo($course);
        } catch (\moodle_exception $e) {
            debugging(sprintf("Cannot returns reference to full info about modules in course: %s", $e -> getMessage()), DEBUG_DEVELOPER);
        }
        if (isset($this -> course_mod_info)) {
            $this -> cms = $this -> course_mod_info -> cms;
        }
    }

    public function modinfo_data($userid, $groupid): array
    {
        foreach ($this -> cms as $mod) {
            $modname = $mod -> modname;
            $access = $this -> check_mod_capability($mod);

            if ($access && ($modname == 'assign' || $modname == 'quiz')) {

                $mod_grade = grade_get_grades($this -> courseid, 'mod', $modname, $mod -> instance, $userid);
                //@$mod_grade = current($mod_grade -> items[0] -> grades) -> grade;

                if (empty($mod_grade))
                    continue;

                $return_arr['mod_grade'] = (string)intval($mod_grade);
                $return_arr['mod_url'] = ($modname == 'assign') ? null : $mod -> url;
                $return_arr['modname'] = $modname;
                $return_arr['groupid'] = $groupid;

                break;
            }
        }

        return $return_arr ?? [];
    }
}