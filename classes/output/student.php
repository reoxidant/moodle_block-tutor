<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace block_tutor\output;

/**
 * Class student
 * @package block_tutor\output
 */
class student
{
    /**
     * @var int
     */
    public int $studentid;
    /**
     * @var string
     */
    public string $studentname;
    /**
     * @var string
     */
    public string $studenturl;
    /**
     * @var bool
     */
    public bool $hasfindebt;

    /**
     * @var array
     */
    public array $data;

    /**
     * student constructor.
     * @param $studentid
     * @param $studentname
     * @param $studenturl
     */
    public function __construct($studentid, $studentname, $studenturl)
    {
        $this -> studentid = $studentid;
        $this -> studentname = $studentname;
        $this -> studenturl = $studenturl;
    }
}