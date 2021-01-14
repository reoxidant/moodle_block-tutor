<?php
/**
 * Description actions
 * @copyright 2021 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace view;

class StrategyGroupView
{
    private array $data = (array)null;

    public function __construct($groupData)
    {
        $this -> data = $groupData;
    }

    private function generateGroup()
    {
        $html = \html_writer ::start_tag('ol') . $this -> generateGroupList($return_arr) . \html_writer ::end_tag('ol');
    }

    private function generateGroupList($return_arr)
    {
        return
            \html_writer ::start_tag('div') .
            \html_writer ::start_tag('h5') . $name . \html_writer ::end_tag('h5') .
            //$this -> generateStudentList($return_arr) .
            \html_writer ::end_tag('div');
    }
}