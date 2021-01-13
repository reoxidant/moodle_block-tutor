<?php

namespace block_tutor\output;
defined('MOODLE_INTERNAL') || die();

use controller\StudentsViewController;
use renderable;
use renderer_base;
use templatable;

global $CFG;

require_once($CFG -> dirroot . '/blocks/tutor/lib.php');

if (is_file($CFG -> dirroot . '/local/student_lib/locallib.php')) {
    require_once($CFG -> dirroot . '/local/student_lib/locallib.php');
}

require_once($CFG -> dirroot . "/blocks/tutor/classes/controller/StudentsViewController.php");

/**
 * Class main
 * @package block_tutor\output
 */
class main implements renderable, templatable
{

    /**
     * @var string The tab to display.
     */
    public $tab;
    /**
     * @var
     */
    public $studentlist_type;

    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($data)
    {
        $this -> tab = $data -> tab;
        $this -> studentlist_type = $data -> studentlist_type;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER;

        $needgradignview = new needgradign_view($USER -> id);

        //fall server
//        $studentslistview = new studentslist_view;
        $studentslistview = new StudentsViewController;
        $enrolledview = new enrolled_view;
        $webinarsview = new webinars_view($USER -> id);

        $viewingneedgradign = false;
        $viewingstudentslist = false;
        $viewingenrolled = false;
        $viewingwebinars = false;
        if ($this -> tab == BLOCK_TUTOR_STUDENTSLIST_VIEW) {
            $viewingstudentslist = true;
        } else if ($this -> tab == BLOCK_TUTOR_ENROLLED_VIEW) {
            $viewingenrolled = true;
        } else if ($this -> tab == BLOCK_TUTOR_NEEDGRADIGN_VIEW) {
            $viewingneedgradign = true;
        } else {
            $viewingwebinars = true;
        }

        $studentlist_bystudent = false;
        $studentlist_bygroup = false;
        if ($this -> studentlist_type == BLOCK_TUTOR_STUDENTLIST_BYSTUDENT) {
            $studentlist_bystudent = true;
        } else {
            $studentlist_bygroup = true;
        }

        return [
            'midnight' => usergetmidnight(time()),
            'viewingneedgradign' => $viewingneedgradign,
            'viewingstudentslist' => $viewingstudentslist,
            'viewingenrolled' => $viewingenrolled,
            'viewingwebinars' => $viewingwebinars,
            'needgradignview' => $needgradignview -> export_for_template($output),
//            'studentslistview' => $studentslistview -> export_for_template($output),
            'studentslistview' => $studentslistview -> export_for_template($output),
            'enrolledview' => $enrolledview -> export_for_template($output),
            'webinarsview' => $webinarsview -> export_for_template($output),
            'studentlist_tab' => [
                'bystudent' => $studentlist_bystudent,
                'bygroup' => $studentlist_bygroup
            ]
        ];
    }
}
