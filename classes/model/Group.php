<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace model;

/**
 * Class Group
 * @package model
 */
class Group
{
    /**
     * @var
     */
    public string $groupid;
    /**
     * @var
     */
    public string $name;
    /**
     * @var array|mixed
     */
    public array $students;

    /**
     * Group constructor.
     * @param $groupid
     * @param $name
     */
    public function __construct($groupid, $name = "", $students = array())
    {
        $this -> groupid = $groupid;
        $this -> students = $students;

        if ($name != null) {
            $this -> name = $name;
        }
    }

    /**
     * @param $studentsCache
     */
    public function get_groups_data_from_cache($studentsCache)
    {
        foreach ($studentsCache as $group) {
            if ($group["groupid"] == $this -> groupid) {
                $this -> groupid = $group["groupid"];
                $this -> name = $group["name"];
                $this -> students = $group["students"];
                break;
            } else {
                continue;
            }
        }
    }
}