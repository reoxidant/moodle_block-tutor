<?php

namespace block_tutor\output;
defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

/**
 * Class renderer
 * @package block_tutor\output
 */
class renderer extends plugin_renderer_base
{
    /**
     * @param main $main
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_main(main $main)
    {
        return $this -> render_from_template('block_tutor/main', $main -> export_for_template($this));
    }
}
