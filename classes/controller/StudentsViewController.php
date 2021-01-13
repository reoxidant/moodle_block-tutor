<?php

namespace controller;
defined('MOODLE_INTERNAL') || die();

use sirius_student;

require_once('StrategySelectListViewController.php');

/**
 * Interface Strategy
 * @package block_tutor\output
 */
interface Strategy
{
    /**
     * @return array
     */
    public function get_students(): array;
}

/**
 * Class studentslist_view
 * @package block_tutor\output
 */
class StudentsViewController extends sirius_student implements Strategy
{

    /**
     * @var Strategy
     */
    public Strategy $strategy;

    /**
     * @param Strategy $strategy
     */
    public function setStrategy(Strategy $strategy)
    {
        $this -> strategy = $strategy;
    }

    /**
     * @param $output
     * @return array
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_for_template($output): array
    {
        return $this -> get_students();
    }

    /**
     * @return array
     */
    public function get_students(): array
    {
        $this -> setStrategy(new StrategySelectListViewController());
        return $this -> strategy -> get_students();
    }
}
