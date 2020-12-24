<?php
/**
 * Description actions
 * @copyright 2020 vshapovalov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package PhpStorm
 */

namespace block_tutor\output;


/**
 * Class databaseList
 * @package block_tutor\output
 */
class databaseList
{
    /**
     * @var \moodle_database|null
     */
    private $database;

    /**
     * databaseList constructor.
     */
    public function __construct()
    {
        global $DB;
        $this -> database = $DB;
    }

    /**
     * @param $courseid
     * @return false|mixed|\stdClass
     * @throws \dml_exception
     */
    public function getCourseBy($courseid)
    {
        try {
            return $this -> database -> get_record('course', array('id' => $courseid));
        } catch (dml_exception $e) {
            return debugging(sprintf(" Cannot connect to external database: %s", $e -> getMessage()), DEBUG_DEVELOPER);
        }
    }

    /**
     * @return bool|mixed|\stdClass
     * @throws \dml_exception
     */
    public function getStudentLeanGroup()
    {
        try {
            return $this -> database -> get_record('user_info_field', array('shortname' => 'studygroup'), 'id');
        } catch (dml_exception $e) {
            return debugging(sprintf(" Cannot connect to external database: %s", $e -> getMessage()), DEBUG_DEVELOPER);
        }
    }

    /**
     * @param $leangroup_field_id
     * @param $user_id
     * @return bool|mixed|\stdClass
     * @throws \dml_exception
     */
    public function getUserInfoBy($leangroup_field_id, $user_id)
    {
        try {
            return $this -> database -> get_record('user_info_data', array('fieldid' => $leangroup_field_id, 'userid' => $user_id), 'data');
        } catch (dml_exception $e) {
            return debugging(sprintf(" Cannot connect to external database: %s", $e -> getMessage()), DEBUG_DEVELOPER);
        }
    }

    /**
     * @param $id
     * @return bool|\lang_string|string
     * @throws \dml_exception
     */
    public function getStudentBy($studentid)
    {
        try {
            return $this -> database -> get_record('user', array('id' => $studentid));
        } catch (dml_exception $e) {
            return debugging(sprintf(" Cannot connect to external database: %s", $e -> getMessage()), DEBUG_DEVELOPER);
        }
    }
}