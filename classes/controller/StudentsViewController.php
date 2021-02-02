<?php

namespace controller;
defined('MOODLE_INTERNAL') || die();

use sirius_student;

require_once("Strategy.php");
require_once('StrategySelectListViewController.php');
require_once('StrategyAjaxViewController.php');

/**
 * Class studentslist_view
 * @package block_tutor\output
 */
class StudentsViewController extends sirius_student implements Strategy
{
    /**
     * @var
     */
    public Strategy $strategy;

    /**
     * StudentsViewController constructor.
     * @param Strategy $strategy
     */
    public function __construct(Strategy $strategy)
    {
        $this -> strategy = $strategy;
    }

    /**
     * @param $output
     * @return array
     * @throws \coding_exception
     */
    public function export_for_template($output): array
    {


        return $this -> get_students();

//        return $this -> get_students();
    }

    /**
     * @param Strategy $strategy
     */
    public function setStrategy(Strategy $strategy)
    {
        $this -> strategy = $strategy;
    }

    /**
     * @return array
     */
    public function get_students(): array
    {
        return $this -> strategy -> get_students();
    }
}
