<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace block_tutor\output;

class group
{
    public $groupid;
    public $name;

    public function __construct($groupid, $name)
    {
        $this->groupid = $groupid;
        $this->name = $name;
    }
}