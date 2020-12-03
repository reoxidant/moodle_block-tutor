<?php
namespace block_tutor\output;
defined('MOODLE_INTERNAL') || die();

use customscripts_muiv_students;
use moodle_url;
use sirius_student;
use Strategy\StrategySelectList;

require_once('Strategy/StrategySelectList.php');

//require_once($CFG->dirroot . "/local/customlib.php");
require_once($CFG->dirroot . '/local/student_lib/locallib.php');

interface Strategy
{
    public function get_students(): array;
}

class studentslist_view extends sirius_student implements Strategy
{
    private $sortcmpby = 'coursename'; // для функции сортировки массива

    private $strategy;

    private function setStrategy(Strategy $strategy)
    {
        $this -> strategy = $strategy;
    }

    public function export_for_template($output) {

        $this->get_students();
    }

	// сортировка студентов
	private function cmp($a, $b)
	{
		return strcasecmp(mb_strtolower($a[$this->sortcmpby]), mb_strtolower($b[$this->sortcmpby]));
	}
		
	// поиск в курсе первого assign или quiz и возврат по нему оценки с данными для перехода
	// вместе с оценкой
	private function get_grade_mod($course, $userid, $groupid) {
		$modinfo_obj = get_fast_modinfo($course);
		$cms = $modinfo_obj->cms;
		
		$return_arr = Array();
		
		foreach($cms as $mod) {
			$modname = $mod->modname;
			
			if($this->check_mod_capability($mod) && ($modname == 'assign' || $modname == 'quiz')){
				$mod_grade = grade_get_grades($course->id, 'mod', $modname, $mod->instance, $userid);
				@$mod_grade = current($mod_grade->items[0]->grades)->grade;
				if(empty($mod_grade))
					continue;
				
				$url = null;
				if($modname == 'assign')
					$url = $mod->url;					
				
				$return_arr['mod_grade'] = (string)intval($mod_grade);
				$return_arr['mod_url'] = $url;
				$return_arr['modname'] = $modname;
				$return_arr['groupid'] = $groupid;
				
				break;
			}
		}
		
		return $return_arr;
	}

	private static function get_student_leangroup($userid) {
		global $DB;
		if($student_leangroup_fieldid = $DB->get_record('user_info_field', array('shortname' => 'studygroup'), 'id')){
			if($data = $DB->get_record('user_info_data', array('fieldid' => $student_leangroup_fieldid->id, 'userid' => $userid), 'data')){
				if(!isset($data->data))
					return false;
							
				return trim($data->data);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

    public function get_students(): array
    {
        $this->setStrategy(new StrategySelectList());
        return $this->strategy->get_students();
    }
}
