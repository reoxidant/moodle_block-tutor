<?php

namespace block_tutor\output;
defined('MOODLE_INTERNAL') || die();

class enrolled_view
{
    public function export_for_template($output)
    {
        global $USER;

        // требуется для просмотра приостановленных на курсе пользователей.
        // иначе будет ошибка при просмотре письменных работ
        //@set_user_preference('grade_report_showonlyactiveenrol', false);

        $userallcourses = (array)enrol_get_all_users_courses($USER->id, true, null, 'fullname');
        foreach ($userallcourses as $cid => &$course) {
            if (!is_siteadmin() && $course->visible != '1') {
                unset($userallcourses[$cid]);
                continue;
            }

            $course->url = course_get_url($cid);
        }
        return array_values($userallcourses);
    }
}
