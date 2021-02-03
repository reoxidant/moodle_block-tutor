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
    private array $data = array();

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

    public function pullHtmlGroupData()
    {
        list("groupid" => $groupid, "name" => $name) = $this -> data;

        $this -> html =
            \html_writer::start_tag('h5') . $name. \html_writer ::end_tag('h5');
            \html_writer::start_tag("ul").$this->createListStudents().\html_writer::end_tag("ul");
    }

    private function createListStudents(){
        list("students" => $studentList) = $this->data;

        foreach ($studentList as $student){
            $this -> html .=
            \html_writer::start_tag("li", array("class" => "studentrow hasfindebt")).
            \html_writer::start_tag("a", array("href" => $student->studenturl, "target" => "_blank")).$student->studentname.\html_writer::end_tag("a")
            .\html_writer::end_tag("li");
        }
    }
}