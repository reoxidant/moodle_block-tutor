<?php

namespace block_tutor\output;
defined('MOODLE_INTERNAL') || die();

use context_module, moodle_url;

class webinars_view
{
    private $userid;

    function __construct($userid)
    {
        $this->userid = (int)$userid;
    }

    public function export_for_template($output)
    {

        $return_arr = Array();
        $arr_userwebinars = $this->get_user_webinars();

        $return_arr['count'] = 0;
        $return_arr['webinars'] = Array();
        foreach ($arr_userwebinars as $webinarobj) {
            $context = context_module::instance($webinarobj->cmid);

            if (!has_capability('mod/bigbluebuttonbn:join', $context, $this->userid))
                continue;

            $courseid = $webinarobj->courseid;

            $current_arr = Array();
            $current_arr['cmurl'] = new moodle_url('/mod/bigbluebuttonbn/view.php', array('id' => $webinarobj->cmid));
            $current_arr['cmname'] = $webinarobj->name;
            $current_arr['description'] = trim($webinarobj->intro);
            $current_arr['courseurl'] = new moodle_url('/course/view.php', array('id' => $webinarobj->courseid));

            $return_arr['webinars'][$courseid]['coursename'] = $webinarobj->coursename;
            $return_arr['webinars'][$courseid]['data'][] = $current_arr;
            $return_arr['count']++;
        }
        $return_arr['webinars'] = array_values($return_arr['webinars']);

        return $return_arr;
    }

    private function get_user_webinars()
    {
        global $DB;

        $sql = 'SELECT
					mdl_course_modules.id AS cmid,
					mdl_bigbluebuttonbn.name,
					mdl_bigbluebuttonbn.intro,
					mdl_course.fullname AS coursename,
					mdl_course.id AS courseid
				FROM
					mdl_user_enrolments
				JOIN mdl_enrol ON mdl_user_enrolments.enrolid = mdl_enrol.id AND mdl_enrol.enrol = \'manual\' AND mdl_user_enrolments.status = 0
				JOIN mdl_course ON mdl_enrol.courseid = mdl_course.id
				JOIN mdl_course_modules ON mdl_course.id = mdl_course_modules.course
				JOIN mdl_modules ON mdl_course_modules.module = mdl_modules.id AND mdl_modules.name = \'bigbluebuttonbn\' AND mdl_course_modules.visible = 1
				JOIN mdl_bigbluebuttonbn ON mdl_course_modules.instance = mdl_bigbluebuttonbn.id
				WHERE
					mdl_user_enrolments.userid = :userid
				ORDER BY 
					mdl_course.fullname, mdl_bigbluebuttonbn.name';
        $params = Array('userid' => $this->userid);

        return $DB->get_records_sql($sql, $params);
    }

}
