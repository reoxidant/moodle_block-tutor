<?php


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/tutor/lib.php');

class block_tutor extends block_base
{

    /**
     * Init.
     */
    public function init()
    {
        $this->title = get_string('pluginname', 'block_tutor');
    }

/*    public function get_required_javascript()
    {
        parent::get_required_javascript();
        $this->page->requires->js_call_amd('block_tutor/init', 'init');
    }*/

    /**
     * Returns the contents.
     *
     * @return stdClass contents of block
     */
    public function get_content()
    {
        global $USER, $DB;

        if (!isloggedin() || !has_capability('block/tutor:view', $this->page->context))
            return false;

        if (isset($this->content)) {
            return $this->content;
        }

        $data = new stdClass;
        // Check if the tab to select wasn't passed in the URL, if so see if the user has any preference.
        if (!$tab = optional_param('tutortab', null, PARAM_ALPHA)) {
            // Check if the user has no preference, if so get the site setting.
            if (!$tab = get_user_preferences('block_tutor_last_tab')) {
                $config = get_config('block_tutor');
                $tab = $config->defaulttab;
            }
        }
        $data->tab = $tab;

        // для переключателя внутри вкладки
        if (!$studentlist_type = get_user_preferences('block_tutor_studentlist_tab')) {
            $studentlist_type = BLOCK_TUTOR_STUDENTLIST_BYGROUP;
        }
        $data->studentlist_type = $studentlist_type;

        $renderable = new \block_tutor\output\main($data);
        $renderer = $this->page->get_renderer('block_tutor');

        $this->content = new stdClass();

        $this->content->text = $renderer->render($renderable);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Locations where block can be displayed.
     *
     * @return array
     */
    public function applicable_formats()
    {
        return array('my' => true);
    }

    /**
     * This block does contain a configuration settings.
     *
     * @return boolean
     */
    public function has_config()
    {
        return true;
    }
}
