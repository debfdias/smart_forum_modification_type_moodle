<?php

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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    mod_octopus
 * @copyright  2016 SABER Tecnologias Educacionais e Sociais
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'mod_octopus_get_threads_by_user' => array(
                'classname'   => 'mod_octopus_external',
                'methodname'  => 'get_threads_by_user',
                'classpath'   => 'mod/octopus/externallib.php',
                'description' => 'Return threads for user.',
                'type'        => 'read',
        ),
//        'mod_octopus_add_thread' => array(
//                'classname'   => 'mod_octopus_external',
//                'methodname'  => 'add_thread',
//                'classpath'   => 'mod/octopus/externallib.php',
//                'description' => 'Add threads for user.',
//                'type'        => 'read',
//        ),
//        'mod_octopus_add_post' => array(
//                'classname'   => 'mod_octopus_external',
//                'methodname'  => 'add_post',
//                'classpath'   => 'mod/octopus/externallib.php',
//                'description' => 'Add post for user.',
//                'type'        => 'read',
//        ),
        'mod_octopus_new_post' => array(
                'classname'   => 'mod_octopus_external',
                'methodname'  => 'new_post',
                'classpath'   => 'mod/octopus/externallib.php',
                'description' => 'New post',
                'type'        => 'read',
        ),
        'mod_octopus_get_threads_list' => array(
                'classname'   => 'mod_octopus_external',
                'methodname'  => 'get_threads_list',
                'classpath'   => 'mod/octopus/externallib.php',
                'description' => 'Return threads for cmid',
                'type'        => 'read',
        ),
        'mod_octopus_add_comment' => array(
                'classname'   => 'mod_octopus_external',
                'methodname'  => 'add_comment',
                'classpath'   => 'mod/octopus/externallib.php',
                'description' => 'add comment',
                'type'        => 'read',
        ),
        'mod_octopus_post_like' => array(
                'classname'   => 'mod_octopus_external',
                'methodname'  => 'post_like',
                'classpath'   => 'mod/octopus/externallib.php',
                'description' => 'add like to a post',
                'type'        => 'read',
        ),
        'mod_octopus_post_dislike' => array(
                'classname'   => 'mod_octopus_external',
                'methodname'  => 'post_dislike',
                'classpath'   => 'mod/octopus/externallib.php',
                'description' => 'add dislike to a post',
                'type'        => 'read',
        ),
        'mod_octopus_get_tags_all' => array(
                'classname'   => 'mod_octopus_external',
                'methodname'  => 'get_tags_all',
                'classpath'   => 'mod/octopus/externallib.php',
                'description' => 'return threads for user.',
                'type'        => 'read',
        ),
        'mod_octopus_get_thread_head' => array(
                'classname'   => 'mod_octopus_external',
                'methodname'  => 'get_thread_head',
                'classpath'   => 'mod/octopus/externallib.php',
                'description' => 'return threads for head.',
                'type'        => 'read',
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'Octopus' => array(
                'functions' => array ('mod_octopus_get_threads_by_user','mod_octopus_new_post',
                        'mod_octopus_get_threads_list','mod_octopus_add_comment', 'mod_octopus_post_like','mod_octopus_post_dislike','mod_octopus_get_tags_all','mod_octopus_get_thread_head'),
                'restrictedusers' => 0,
                'enabled'=>1,
        )
);
