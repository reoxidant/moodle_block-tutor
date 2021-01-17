<?php
/**
 * Description actions
 * @copyright 2021 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace controller;

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