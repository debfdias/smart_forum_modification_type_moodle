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
 * Define all the restore steps that will be used by the restore_octopus_activity_task
 *
 * @package   mod_octopus
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one octopus activity
 *
 * @package   mod_octopus
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_octopus_activity_structure_step extends restore_activity_structure_step {
//';
    /**
     * Defines structure of path elements to be processed during the restore
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('octopus', '/activity/octopus');
       //$paths[] = new restore_path_element('thread', '/activity/thread');

        if ($userinfo) {
            $paths[] = new restore_path_element('octopus_thread', '/activity/octopus/threads/thread');
            $paths[] = new restore_path_element('octopus_post', '/activity/octopus/threads/thread/posts/post');
            $paths[] = new restore_path_element('octopus_tag', '/activity/octopus/tags/tag');
            $paths[] = new restore_path_element('octopus_has_tag', '/activity/octopus/threads/thread/posts/post/has_tags/has_tag');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_octopus($data) {

        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }

        if (empty($data->timemodified)) {
            $data->timemodified = time();
        }

        if ($data->grade < 0) {
            // Scale found, get mapping.
            $data->grade = -($this->get_mappingid('scale', abs($data->grade)));
        }

        // Create the octopus instance.
        $newitemid = $DB->insert_record('octopus', $data);
        $this->apply_activity_instance($newitemid);
    }

    protected function process_octopus_thread($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $course_id = $this->get_courseid();

        $moduleid = $DB->get_field('modules', 'id', array('name'=>'octopus'));

        $query = "SELECT id FROM {course_modules} WHERE module = $moduleid";
        $teste = $DB->get_records_sql($query);

        foreach ($teste as $value) 
            $array[] = $value->id;

        $data->cmid = $array[count($array) - 1];

        $newitemid = $DB->insert_record('octopus_thread', $data);
        $this->set_mapping('octopus_thread', $oldid, $newitemid);
    }

    protected function process_octopus_post($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }

        $data->thread_id = $this->get_new_parentid('octopus_thread');

        if(!empty($data->thread_id)){
            //$data->thread_id =  $this->get_mappingid('octopus_thread', $data->thread_id);
        }

        $newitemid = $DB->insert_record('octopus_post', $data);
        $this->set_mapping('octopus_post', $oldid, $newitemid);

        if (empty($data->thread_id)) {
            //$DB->set_field('octopus_thread', 'id', $newitemid, array('id' => $data->id));
        }
    }

    protected function process_octopus_tag($data){
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $course_id = $this->get_courseid();

        $moduleid = $DB->get_field('modules', 'id', array('name'=>'octopus'));

        $query = "SELECT id FROM {course_modules} WHERE module = $moduleid";
        $teste = $DB->get_records_sql($query);

        foreach ($teste as $value) 
            $array[] = $value->id;

        $data->cmid = $array[count($array) - 1];

        $newitemid = $DB->insert_record('octopus_tag', $data);
        $this->set_mapping('octopus_tag', $oldid, $newitemid);
    }

    protected function process_octopus_has_tag($data){
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->post_id = $this->get_new_parentid('octopus_post');

        $newitemid = $DB->insert_record('octopus_post_has_tag', $data);
        $this->set_mapping('octopus_post_has_tag', $oldid, $newitemid);
    }

    /**
     * Post-execution actions
     */
    protected function after_execute() {
        // Add octopus related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_octopus', 'intro', null);
    }
}
