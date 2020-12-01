<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

use block_tutor\output\Strategy;

require_once("../studentslist_view.php");

class StrategySelectorList implements Strategy
{
    private $tablist = '';

    public function __construct($tablist){
        $this->tablist = $tablist;
    }

    public function get_students(): array
    {
        // TODO: Implement get_students() method.
    }
}