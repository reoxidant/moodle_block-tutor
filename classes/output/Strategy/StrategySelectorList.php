<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

use block_tutor\output\Strategy;

require_once("../studentslist_view.php");

class StrategySelectorList extends sirius_student implements Strategy
{
    private $tablist = '';

    public function __construct($tablist){
        $this->tablist = $tablist;
    }

    public function get_students(): array
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
}