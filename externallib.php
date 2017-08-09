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
 * External Web Service Template
 *
 * @package    mod_octopus
 * @copyright  2016 SABER Tecnologias Educacionais e Sociais
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");
require_once (dirname(__FILE__) . '/lib.php');

class mod_octopus_external extends external_api {

//    public static function add_thread_parameters() {
//        return new external_function_parameters(
//                array()
//        );
//    }

    public static function new_post_parameters() {
        return new external_function_parameters(
                array('cmid' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'title' => new external_value(PARAM_TEXT, 'The text.'),
            'message' => new external_value(PARAM_TEXT, 'The text.'),
            'user_id' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'type' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'tags' => new external_value(PARAM_TEXT, 'The text.')
                )
        );
    }

    public static function add_comment_parameters() {
        return new external_function_parameters(
                array('message' => new external_value(PARAM_TEXT, 'The text.'),
            'user_id' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'thread_id' => new external_value(PARAM_INT, 'The number to be doubled.')
                )
        );
    }

//    public static function add_post_parameters() {
//        return new external_function_parameters(
//                array()
//        );
//    }

    public static function get_threads_by_user_parameters() {
        return new external_function_parameters(
                //adicionando parametro user_id e cmid
                array('user_id' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'cmid' => new external_value(PARAM_INT, 'The number to be doubled.')
                )
        );
    }

    public static function get_threads_list_parameters() {
        return new external_function_parameters(
                array('cmid' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'limit' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'offset' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'user_id' => new external_value(PARAM_INT, 'The number to be doubled.')
                )
        );
    }

    public static function post_like_parameters() {
        return new external_function_parameters(
                array('user_id' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'post_id' => new external_value(PARAM_INT, 'The number to be doubled.')
                )
        );
    }

    public static function post_dislike_parameters() {
        return new external_function_parameters(
                array('user_id' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'post_id' => new external_value(PARAM_INT, 'The number to be doubled.')
                )
        );
    }

    public static function get_tags_all_parameters() {
        return new external_function_parameters(
                array('cmid' => new external_value(PARAM_INT, 'The number to be doubled.')
                )
        );
    }

    //ajuste necessario verificar com well
    public static function get_thread_head_parameters() {
        return new external_function_parameters(
                array('tid' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'cmid' => new external_value(PARAM_INT, 'The number to be doubled.'),
            'user_id' => new external_value(PARAM_INT, 'The number to be doubled.')
                )
        );
    }

//****************************************************************\\

    public static function new_post($cmid, $title, $message, $user_id, $type, $tags) {
        global $DB;

        $tid = octopus_new_thread($cmid, $title);
        $thread_id = $tid;
        $pid = octopus_new_post(utf8_decode($message), $user_id, $type, $thread_id, 1);
        $tags = explode(",", $tags);
        octopus_add_post_tag($pid, $tags);
        $result['new_post'] = true;
        return $result;
    }

    public static function add_comment($message, $user_id, $thread_id) {
        global $USER, $DB, $CFG;

        $newpost = new stdClass();
        $newpost->message = nl2br(htmlspecialchars($message));
        $newpost->timecreated = time();
        $newpost->user_id = $user_id;
        $newpost->type_message = 3;
        $newpost->thread_id = $thread_id;
        $newpost->is_head = 0;

        $DB->insert_record('octopus_post', $newpost);
        //retorno do comentario
        $cont = 0;
        $thread = octopus_get_thread($thread_id);
        $threads_array = array();
        foreach ($thread->posts as $thr) {
            $new_thread = new stdClass();
            //pegando informacoes do post
            $post = $DB->get_records('octopus_post', array('thread_id' => $thr->id));
            foreach ($post as $post) {
                $message_post = $post->message;
                $post_id = $post->id;
            }

            $new_thread->id = $thr->id;
            $new_thread->message = $thr->message;
            $new_thread->timecreated = $thr->timecreated;
            $new_thread->user_id = $thr->user_id;
            $new_thread->user = $thr->user->firstname . " " . $thr->user->lastname;
            $new_thread->user_image_url = $CFG->wwwroot . "/user/pix.php/" . $thr->user_id . "/f1.jpg";
            $new_thread->type = $thr->type_message;
            $new_thread->likes = $thr->count_likes;
            $new_thread->dislikes = $thr->count_dislikes;
            //avaliacao do user_id
            $avaluat = getStatusLD($user_id, $thr->id);

            if ($avaluat == 1) {
                $like = true;
                $dislike = false;
            } else if ($avaluat == 0) {
                $like = false;
                $dislike = true;
            } else {
                $like = false;
                $dislike = false;
            }

            $new_thread->evaluation = $evaluation_array = array('like' => $like, 'dislike' => $dislike);
            $new_thread->tags = $thr->tags;
            if ($cont == 0) {
                array_push($threads_array, $new_thread);
            }
            $cont++;
        }

        //fim retorno
        $result['add_comment'] = true;
        $result['comment'] = $threads_array;
        return $result;
    }

    public static function get_threads_by_user($user_id, $cmid) {
        global $USER, $DB, $CFG;
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);
        $threads = octopus_get_threads_by_user($user_id, $cmid);

        $threads_array = array();
        foreach ($threads as $thr) {
            $new_thread = new stdClass();
            $cont++;

            $new_thread->id = $thr->id;
            $new_thread->title = $thr->title;
            $new_thread->user = $thr->user;
            $new_thread->user_id = $thr->user_id;
            $new_thread->user_image_url = $CFG->wwwroot . "/user/pix.php/" . $user_id . "/f1.jpg";
            $new_thread->timecreated = $thr->timecreated;
            $new_thread->type = $thr->type;
            $new_thread->likes = $thr->likes;
            $new_thread->dislikes = $thr->dislikes;
            $new_thread->post = $thr->posts;
            $new_thread->tags = $thr->tags;

            array_push($threads_array, $new_thread);
        }

        $all = array('threads' => $threads_array, 'tags' => $tags);
        $result = array();
        $result['threads'] = $threads_array;
        return $result;
    }

    public static function get_threads_list($cmid, $limit, $offset, $user_id) {
        global $USER, $DB, $CFG; //
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //retorno das tags p o kraken
        $query_tags = "SELECT id, name_tag
                    FROM {octopus_tag}                     
                    WHERE cmid = $cmid";

        $tags = $DB->get_records_sql($query_tags);
        $threads = octopus_get_threads_list($cmid, $limit, $offset, $start = null, $end = null);
        $threads_array = array();

        //pegando informacoes da thread
        foreach ($threads as $thr) {
            $new_thread = new stdClass();
            //pegando informacoes do post
            $post = $DB->get_records('octopus_post', array('thread_id' => $thr->id, 'is_head' => 1));
            foreach ($post as $post) {
                $message_post = $post->message;
                $post_id = $post->id;
            }
            $new_thread->id = $thr->id;
            $new_thread->post_id = $post_id;
            $new_thread->title = $thr->title;
            $new_thread->message = $message_post;
            $new_thread->user = $thr->user;
            $new_thread->user_id = $thr->user_id;
            $new_thread->user_image_url = $CFG->wwwroot . "/user/pix.php/" . $thr->user_id . "/f1.jpg";
            $new_thread->timecreated = $thr->timecreated;
            $new_thread->type = $thr->type;
            $new_thread->likes = $thr->likes;
            $new_thread->dislikes = $thr->dislikes;
            //avaliacao do user_id
            $avaluat = getStatusLD($user_id, $post_id);

            if ($avaluat == 1) {
                $like = true;
                $dislike = false;
            } else if ($avaluat == 0) {
                $like = false;
                $dislike = true;
            } else {
                $like = false;
                $dislike = false;
            }

            $new_thread->evaluation = $evaluation_array = array('like' => $like, 'dislike' => $dislike);
            $new_thread->count_posts = $thr->posts;
            $new_thread->tags = $thr->tags;
            //montando array
            array_push($threads_array, $new_thread);
        }

        $all = array('threads' => $threads_array, 'tags' => $tags);
        $result = array();
        $result['tags'] = $tags;
        $result['threads'] = $threads_array;
        return $result;
    }

    public static function post_like($user_id, $post_id) {
        global $DB;

        $like = $DB->get_record('octopus_like', array('user_id' => $user_id, 'post_id' => $post_id));
        $result = array();
        if ($like and $like->type == 0) {
            //curtindo post
            $like->type = 1;
            $like->timecreated = time();
            $DB->update_record('octopus_like', $like);
            $result['like'] = true;
            return $result;
        } elseif ($like and $like->type == 1) {
            //retirando avaliacao
            $DB->delete_records('octopus_like', array('user_id' => $user_id, 'post_id' => $post_id));
            $result['like'] = false;
            return $result;
        } else {

            $like = new stdClass();
            $like->user_id = $user_id;
            $like->post_id = $post_id;
            $like->type = 1;
            $like->timecreated = time();
            $DB->insert_record('octopus_like', $like);
            $result['like'] = true;
            return $result;
        }
    }

    public static function post_dislike($user_id, $post_id) {
        global $DB;

        $like = $DB->get_record('octopus_like', array('user_id' => $user_id, 'post_id' => $post_id));
        $result = array();
        if ($like and $like->type == 1) {
            $like->type = 0;
            $like->timecreated = time();
            $DB->update_record('octopus_like', $like);
            $result['dislike'] = true;
            return $result;
        } elseif ($like and $like->type == 0) {
            $DB->delete_records('octopus_like', array('user_id' => $user_id, 'post_id' => $post_id));
            $result['dislike'] = false;
            return $result;
        } else {
            $like = new stdClass();
            $like->user_id = $user_id;
            $like->post_id = $post_id;
            $like->type = 0;
            $like->timecreated = time();
            $DB->insert_record('octopus_like', $like);
            $result['dislike'] = true;
            return $result;
        }
    }

    public static function get_tags_all($cmid) {
        global $USER, $DB, $CFG;

        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        $query = "SELECT id,name_tag
                    FROM {octopus_tag}                     
                    WHERE cmid = $cmid";

        $re = $DB->get_records_sql($query);
        $array = array();
        foreach ($re as $res) {
            array_push($array, $res);
        }
        $array1 = array(
            'tags' => $array,
        );
        $result = array();
        $result['status'] = true;
        $result['tags'] = $array;
        return $result;
    }
    //ajustes realizados passando o cmid como parametro
    public static function get_thread_head($tid, $user_id,$cmid) {
        global $USER, $DB, $CFG;

        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);
        $thread = octopus_get_thread($tid,$cmid);
        $thread_head = octopus_get_thread_head($tid,$cmid);
        $re = array(
            'title' => $thread->title,
            'thread_head' => $thread_head,
            'thread_posts' => $thread->posts,
        );

        $threads_array = array();
        foreach ($thread->posts as $thr) {
            $new_thread = new stdClass();
            //pegando informacoes do post
            $post = $DB->get_records('octopus_post', array('thread_id' => $thr->id));
            foreach ($post as $post) {
                $message_post = $post->message;
                $post_id = $post->id;
            }
            $new_thread->id = $thr->id;
            $new_thread->message = $thr->message;            
            $new_thread->timecreated = $thr->timecreated;
            $new_thread->user_id = $thr->user_id;
            $new_thread->user = $thr->user->firstname . " " . $thr->user->lastname;
            $new_thread->user_image_url = $CFG->wwwroot . "/user/pix.php/" . $thr->user_id . "/f1.jpg";
            $new_thread->type = $thr->type_message;
            $new_thread->likes = $thr->count_likes;
            $new_thread->dislikes = $thr->count_dislikes;
            //avaliacao do user_id
            $avaluat = getStatusLD($user_id, $thr->id);

            if ($avaluat == 1) {
                $like = true;
                $dislike = false;
            } else if ($avaluat == 0) {
                $like = false;
                $dislike = true;
            } else {
                $like = false;
                $dislike = false;
            }

            $new_thread->evaluation = $evaluation_array = array('like' => $like, 'dislike' => $dislike);
            $new_thread->tags = $thr->tags;
            array_push($threads_array, $new_thread);
        }
        $title=end($threads_array);
        
        //retirando ultimo elemento do array
        array_pop($threads_array);
        $result = array();
        $result['title']=$thread_head->title;
        $result['thread_head'] = $threads_array;
        return $result;
    }

//****************************************************************\\

    public static function get_threads_by_user_returns() {

        return new external_single_structure(
                array(
            'threads' => new external_multiple_structure(
                    new external_single_structure(
                    array(
                'id' => new external_value(PARAM_INT, 'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true'
                ),
                'title' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user_id' => new external_value(PARAM_INT, 'the value of the option, 
                                                            this param is validated in the external function.'
                ),
                'user_image_url' => new external_value(PARAM_TEXT, 'the value of the option, user_image_url
                                                            this param is validated in the external function.'
                ),
                'timecreated' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'type' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'likes' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'dislikes' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'post' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'tags' => new external_multiple_structure(
                        new external_single_structure(
                        array(
                    'id' => new external_value(PARAM_INT, 'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true
                            '),
                    'name_tag' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                    )
                        )
                        ))
                    )
                    ))
                )
        );
    }

    public static function new_post_returns() {

        return new external_function_parameters(
                array(
            'new_post' => new external_value(PARAM_BOOL, 'some group id')
                )
        );
    }

    public static function get_threads_list_returns() {

        return new external_single_structure(
                array(
            'tags' => new external_multiple_structure(
                    new external_single_structure(
                    array(
                'id' => new external_value(PARAM_INT, 'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true
                            '),
                'name_tag' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                )
                    )
                    )),
            'threads' => new external_multiple_structure(
                    new external_single_structure(
                    array(
                'id' => new external_value(PARAM_INT, 'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true'
                ),
                'post_id' => new external_value(PARAM_INT, 'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true'
                ),
                'title' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'message' => new external_value(PARAM_RAW, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user_id' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user_image_url' => new external_value(PARAM_TEXT, 'the value of the option, user_image_url
                                                            this param is validated in the external function.'
                ),
                'timecreated' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'type' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'likes' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'dislikes' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'evaluation' =>
                new external_single_structure(
                        array(
                    'like' => new external_value(PARAM_BOOL, 'The name of the custom field'),
                    'dislike' => new external_value(PARAM_BOOL, 'The value of the custom field')
                        )
                ),
                'count_posts' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'tags' => new external_multiple_structure(
                        new external_single_structure(
                        array(
                    'id' => new external_value(PARAM_INT, 'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true
                            '),
                    'name_tag' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                    )
                        )
                        ))
                    )
                    ))
                )
        );
    }

//    public static function add_post_returns() {
//        return new external_function_parameters(
//                array(
//            'add_post' => new external_value(PARAM_BOOL, 'some group id')
//                )
//        );
//    }
//    public static function add_thread_returns() {
//        return new external_value(PARAM_TEXT, 'olha a água mineral');
//    }

    public static function add_comment_returns() {
        return new external_function_parameters(
                array(
            'add_comment' => new external_value(PARAM_BOOL, 'some group id'),
            'comment' => new external_multiple_structure(
                    new external_single_structure(
                    array(
                'id' => new external_value(PARAM_INT, 'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true'
                ),
                'message' => new external_value(PARAM_RAW, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user_id' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user_image_url' => new external_value(PARAM_TEXT, 'the value of the option, user_image_url
                                                            this param is validated in the external function.'
                ),
                'timecreated' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'type' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'likes' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'dislikes' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'evaluation' =>
                new external_single_structure(
                        array(
                    'like' => new external_value(PARAM_BOOL, 'The name of the custom field'),
                    'dislike' => new external_value(PARAM_BOOL, 'The value of the custom field')
                        )
                ),
                    )
                    ))
                )
        );
    }

    public static function post_like_returns() {

        return new external_function_parameters(
                array(
            'like' => new external_value(PARAM_BOOL, 'some group id')
                )
        );
    }

    public static function post_dislike_returns() {
//        return new external_value(PARAM_TEXT, 'adiciona like ao post');
        return new external_function_parameters(
                array(
            'dislike' => new external_value(PARAM_BOOL, 'some group id')
                )
        );
    }

    public static function get_tags_all_returns() {
        return new external_single_structure(
                array(
            'tags' => new external_multiple_structure(
                    new external_single_structure(
                    array(
                'id' => new external_value(PARAM_INT, 'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true
                            '),
                'name_tag' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                )
                    )
                    ))
                )
        );
    }

    public static function get_thread_head_returns() {
        return new external_single_structure(
                array(
            'title' => new external_value(PARAM_TEXT, 'return all tags'
            ),
            'thread_head' => new external_multiple_structure(
                    new external_single_structure(
                    array(
                'id' => new external_value(PARAM_INT, 'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true'
                ),
                'message' => new external_value(PARAM_RAW, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user_id' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'user_image_url' => new external_value(PARAM_TEXT, 'the value of the option, user_image_url
                                                            this param is validated in the external function.'
                ),
                'timecreated' => new external_value(PARAM_TEXT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'type' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'likes' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'dislikes' => new external_value(PARAM_INT, 'the value of the option,
                                                            this param is validated in the external function.'
                ),
                'evaluation' =>
                new external_single_structure(
                        array(
                    'like' => new external_value(PARAM_BOOL, 'The name of the custom field'),
                    'dislike' => new external_value(PARAM_BOOL, 'The value of the custom field')
                        )
                ),
//                'tags' => new external_multiple_structure(
//                        new external_single_structure(
//                        array(
//                    'id' => new external_value(PARAM_INT, 'The allowed keys (value format) are:
//                                        discussionsubscribe (bool); subscribe to the discussion?, default to true
//                            '),
//                    'name_tag' => new external_value(PARAM_TEXT, 'the value of the option,
//                                                            this param is validated in the external function.'
//                    )
//                        )
//                        ))
                    )
                    ))
                )
        );
    }

}
