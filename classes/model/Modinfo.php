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
    /**
     * @var \course_modinfo|null
     */
    private ?\course_modinfo $course_mod_info;
    /**
     * @var array|\cm_info[]
     */
    private array $cms;
    /**
     * @var int
     */
    private int $courseid;

    /**
     * @var int
     */
    public int $userid;

    /**
     * Modinfo constructor.
     * @param $course
     */
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

    /**
     * @param $userid
     * @param $groupid
     * @return array
     */
    public function modinfo_data($userid, $groupid): array
    {
        $this -> userid = $userid;

        $return_arr = array();

        foreach ($this -> cms as $mod) {
            $modname = $mod -> modname;

            $access = $this -> check_mod_capability($mod);

            if ($access && ($modname == 'assign' || $modname == 'quiz')) {

                $mod_grade = grade_get_grades($this -> courseid, 'mod', $modname, $mod -> instance, $userid);
                @$mod_grade = current($mod_grade -> items[0] -> grades) -> grade;

                if (empty($mod_grade))
                    continue;

                $return_arr['mod_grade'] = (string)intval($mod_grade);
                $return_arr['mod_url'] = ($modname == 'assign') ? $mod -> url : null;
                $return_arr['modname'] = $modname;
                $return_arr['groupid'] = $groupid;

                break;
            }
        }

        return $return_arr;
    }

    /**
     * @param $modinfo
     * @return bool
     */
    public function check_mod_capability($modinfo): bool
    {
        $context = \context_module ::instance($modinfo -> id);
        if (empty($context -> id))
            return false;

        $enrolled = is_enrolled($context, $this -> userid, '', true);

        return is_siteadmin($this -> userid) || ($modinfo -> visible == 1 && $modinfo -> deletioninprogress == 0 && $modinfo -> visibleoncoursepage == 1 && $enrolled);
    }
}