<?php

namespace controller;
defined('MOODLE_INTERNAL') || die();

use sirius_student;

require_once('StrategySelectListViewController.php');

/**
 * Class studentslist_view
 * @package block_tutor\output
 */
class StudentsViewController extends sirius_student implements Strategy
{
    public Strategy $strategy;

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

    public function setStrategy(Strategy $strategy)
    {
        $this->strategy = $strategy;
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
