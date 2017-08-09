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
 * Define all the backup steps that will be used by the backup_octopus_activity_task
 *
 * @package   mod_octopus
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete octopus structure for backup, with file and id annotations
 *
 * @package   mod_octopus
 * @category  backup
 * @copyright 2015 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_octopus_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        global $DB;

        $id = optional_param('cm', 0, PARAM_INT);

        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the octopus instance.
        $octopus = new backup_nested_element('octopus', array('id'), array(
            'name', 'intro', 'introformat', 'grade', 'allowranking', 'allowusercreatethreads', 'grade_type'));

        $threads = new backup_nested_element('threads');

        $thread = new backup_nested_element('thread', array('id'), array(
            'title', 'cmid'));

        $posts = new backup_nested_element('posts');

        $post = new backup_nested_element('post', array('id'), array(
            'message',  'user_id', 'type_message',
             'thread_id', 'is_head', 'grade'));

        $tags = new backup_nested_element('tags');

        $tag = new backup_nested_element('tag', array('id'), array('name_tag','cmid', 'parent_tag'));

        $has_tags = new backup_nested_element('has_tags');

        $has_tag = new backup_nested_element('has_tag', array('id'), array('post_id','tag_id'));

        $octopus->add_child($threads);
        $threads->add_child($thread);

        $thread->add_child($posts);
        $posts->add_child($post);

        $octopus->add_child($tags);
        $tags->add_child($tag);

        $post->add_child($has_tags);
        $has_tags->add_child($has_tag);

        $octopus->set_source_table('octopus', array('id' => backup::VAR_ACTIVITYID));

        $query = " SELECT *
                    FROM {octopus_thread}
                    WHERE cmid = $id";
        $query2= " SELECT *
                    FROM {octopus_tag}
                    WHERE cmid = $id";

        if ($userinfo) {
            $thread->set_source_sql($query,
                array(backup::VAR_PARENTID));

            $post->set_source_table('octopus_post', array('thread_id' => backup::VAR_PARENTID));

            $tag->set_source_sql($query2,
                array(backup::VAR_PARENTID));

            $has_tag->set_source_table('octopus_post_has_tag', array('post_id' => backup::VAR_PARENTID));
        }

        $post->annotate_ids('user', 'user_id');
        $post->annotate_ids('user', 'thread_id');
        $thread->annotate_ids('user', 'cmid');


        $octopus->annotate_files('mod_octopus', 'intro', null);

        return $this->prepare_activity_structure($octopus);
    }
}
