<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace block_tutor\output;

class course
{
    private $courseid;
    private $group;
    private $listData;

    public function __construct($courseid, $group, $listData)
    {
        $this->courseid = $courseid;
        $this->group = $group;
        $this->listData = $listData;
    }

    /**
     * @return null
     */
    public function getCourseid()
    {
        return $this -> courseid;
    }

    /**
     * @return null
     */
    public function getGroup()
    {
        return $this -> group;
    }

    /**
     * @return array[]
     */
    public function getListData(): array
    {
        return $this -> listData;
    }

    /**
     * @param mixed $courseid
     */
    public function setCourseid($courseid)
    {
        $this -> courseid = $courseid;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this -> group = $group;
    }

    /**
     * @param $studentData
     * @param $byKey
     */
    public function setStudentList($studentData, $byKey)
    {
        $this -> listData['students'][$byKey] = $studentData;
    }

    public function setGroupsList($groupData, $byKey)
    {
        $this -> listData['groups'][$byKey] = $groupData;
    }
}