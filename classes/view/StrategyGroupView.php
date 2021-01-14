<?php
/**
 * Description actions
 * @copyright 2021 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace view;

/**
 * Class StrategyGroupView
 * @package view
 */
class StrategyGroupView
{
    /**
     * @var array
     */
    private array $data = (array)null;

    /**
     * @var string
     */
    public string $html;

    /**
     * StrategyGroupView constructor.
     * @param $groupData
     */
    public function __construct($groupData)
    {
        $this -> data = $groupData;
    }

    /**
     *
     */
    private function generateGroupList()
    {
        $this -> html = \html_writer ::start_tag('ol') . $this -> createListStudents($this -> data) . \html_writer ::end_tag('ol');
    }

    /**
     * @param $groupData
     * @return string
     */
    private function createListStudents($groupData): string
    {
        return
            \html_writer ::start_tag('div') .
            \html_writer ::start_tag('h5') . $name . \html_writer ::end_tag('h5') .
            //$this -> generateStudentList($return_arr) .
            \html_writer ::end_tag('div');
    }
}