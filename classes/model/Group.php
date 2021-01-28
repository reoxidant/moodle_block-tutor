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
     * Group constructor.
     * @param $groupid
     * @param $name
     */
    public function __construct($groupid, $name)
    {
        $this -> groupid = $groupid;
        $this -> name = $name;
    }
}