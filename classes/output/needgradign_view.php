<?php

namespace block_tutor\output;
defined('MOODLE_INTERNAL') || die();

use customscripts_muiv_students, context_module;

global $CFG;

if (is_file($CFG->dirroot . "/local/customlib.php")) {
    require_once($CFG->dirroot . "/local/customlib.php");
}
require_once($CFG->dirroot . '/mod/assign/locallib.php');

class needgradign_view
{
    private $userid;

    function __construct($userid)
    {
        $this->userid = (int)$userid;
    }

    public function export_for_template($output)
    {
        global $CFG, $DB;

        $return_arr = Array();
        $wwwroot = $CFG->wwwroot;
        $arr_needgrading = $this->get_need_grading();

        $return_arr['count'] = 0;
        foreach ($arr_needgrading as $usersubmit) {
            $studentid = $usersubmit->studentid;

            $context = context_module::instance($usersubmit->cmid);
            if (!has_capability('mod/assign:submit', $context, $studentid) || customscripts_muiv_students::check_hasfindebt($studentid))
                continue;

            $current_arr = Array();
            $current_arr['form_url'] = $wwwroot . '/mod/assign/view.php?id=' . $usersubmit->cmid . '&rownum=0&action=grader&userid=' . $studentid . '&group=' . $usersubmit->groupid . '&treset=1';
            $current_arr['coursename'] = $usersubmit->coursename;
            $current_arr['assignname'] = $usersubmit->assignname;
            $current_arr['courseid'] = $usersubmit->courseid;
            $current_arr['studentname'] = fullname($DB->get_record('user', array('id' => $studentid)));

            $return_arr['data'][] = $current_arr;

            $return_arr['count']++;
        }
        return $return_arr;
    }

    private function get_need_grading()
    {
        global $DB;

        $sql = 'SELECT
					s.id,
					cm.id as cmid,
					c.fullname as coursename,
					s.userid as studentid,
					g.id as groupid,
					c.id as courseid,
					a.name as assignname
				FROM 
					{assign} a
				JOIN {groups} g ON
					a.course = g.courseid
					AND (a.grade > 0 OR a.grade = -4)
					AND g.idnumber NOT LIKE \'nosync%\'
				JOIN {groups_members} gm ON
					g.id = gm.groupid 
				JOIN {assign_submission} s ON
					a.id = s.assignment AND
					gm.userid = s.userid AND
						s.latest = 1 AND
						s.status = :submitted
				JOIN {groups_members} ugm ON
					g.id = ugm.groupid AND
					ugm.userid = :userid 
				JOIN {course} c ON
					a.course = c.id AND
					c.visible = 1
				JOIN {course_modules} cm ON
					cm.course = c.id AND
					cm.instance = a.id AND
					cm.visible = 1
				JOIN {modules} m ON
					m.id = cm.module AND
					m.name = \'assign\'
				JOIN {user} u ON
					u.id = s.userid
				LEFT JOIN {assign_grades} ag ON
					s.assignment = ag.assignment AND
					ag.attemptnumber = s.attemptnumber AND
						s.timemodified IS NOT NULL AND
					s.userid = ag.userid 
				WHERE
						(s.timemodified >= ag.timemodified OR ag.timemodified IS NULL OR ag.grade IS NULL)
				GROUP BY
					s.id, cm.id, c.fullname, a.name, g.name, g.id, s.userid, u.lastname, u.firstname, c.id, a.name
				ORDER BY
					c.fullname, a.name, g.name, u.lastname, u.firstname';
        $params = Array('submitted' => ASSIGN_SUBMISSION_STATUS_SUBMITTED,
            'userid' => $this->userid);

        return $DB->get_records_sql($sql, $params);
    }

}
