<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   block_course_module_list
 * @copyright Zeeshan Khan <zeeshankhan08467@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_course_module_list extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_course_module_list');
    }

    public function applicable_formats() {

        return array('course-view-*' => true);
    }

    public function get_content() {

        global $CFG, $USER, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        // Get the current course ID.
        $courseid = $this->page->course->id;

        // Get the list of course modules for this course.
        $modules = $DB->get_records('course_modules', array('course' => $courseid), 'id');

        $output = html_writer::start_tag('ul');

        foreach ($modules as $module) {
            // Get the activity name.
            $activityname = $DB->get_field('modules', 'name', array('id' => $module->module));

            // Get the date of creation.
            $date = date('d-M-Y', $module->added);

            // Get the completion status.
            $completionstatus = '';

            $array = array('coursemoduleid' => $module->id, 'userid' => $USER->id);

            $modulecompletion = $DB->get_record('course_modules_completion', $array);

            if ($modulecompletion) {
                if ($modulecompletion->completionstate == COMPLETION_COMPLETE) {
                    $completionstatus = '- Completed';
                }
            }
            $url = new moodle_url('/mod/' . $activityname . '/view.php', array('id' => $module->id));

            $output .= html_writer::start_tag('li');
            $output .= html_writer::link($url, $module->id . '-' . $activityname . '-' . $date . $completionstatus);
            $output .= html_writer::end_tag('li');
        }

        $output .= html_writer::end_tag('ul');

        $this->content->text = $output;
        $this->content->footer = '';
        return $this->content;
    }
}
