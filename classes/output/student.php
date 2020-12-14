<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace block_tutor\output;

class student
{
    public $studentid;
    public $studentname;
    public $studenturl;
    public $hasfindebt;

    public function __construct($studentid, $studentname, $studenturl)
    {
        $this->studentid = $studentid;
        $this->studentname = $studentname;
        $this->studenturl = $studenturl;
    }
}