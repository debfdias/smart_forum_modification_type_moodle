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
 * Library of interface functions and constants for module octopus
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the octopus specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_octopus
 * @copyright  SABER Tecnologias Educacionais e Sociais
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Example constant, you probably want to remove this :-)
 */
define('OCTOPUS_ULTIMATE_ANSWER', 42);

/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function octopus_supports($feature) {

    switch($feature) {
        case FEATURE_MOD_INTRO :
            return true;
        case FEATURE_SHOW_DESCRIPTION :
            return true;
        case FEATURE_GRADE_HAS_GRADE :
            return true;
        case FEATURE_BACKUP_MOODLE2 :
            return true;
        default :
            return null;
    }
}

/**
 * Saves a new instance of the octopus into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $octopus Submitted data from the form in mod_form.php
 * @param mod_octopus_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted octopus record
 */
function octopus_add_instance(stdClass $octopus, mod_octopus_mod_form $mform = null) {
    global $DB;

    $octopus->timecreated = time();

    // You may have to add extra stuff in here.
    $octopus->id = $DB->insert_record('octopus', $octopus);

    //octopus_grade_item_update($octopus);

    return $octopus->id;
}

/**
 * Updates an instance of the octopus in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $octopus An object from the form in mod_form.php
 * @param mod_octopus_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function octopus_update_instance(stdClass $octopus, mod_octopus_mod_form $mform = null) {
    global $DB;

    $octopus->timemodified = time();
    $octopus->id = $octopus->instance;

    // You may have to add extra stuff in here.
    $result = $DB->update_record('octopus', $octopus);

    octopus_grade_item_update($octopus);

    return $result;
}

/**
 * Removes an instance of the octopus from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function octopus_delete_instance($id) {
    global $DB;

    if (!$octopus = $DB->get_record('octopus', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here.

    $DB->delete_records('octopus', array('id' => $octopus->id));

    octopus_grade_item_delete($octopus);

    return true;
}




//======================================= NOTA PREMIACAO ==================================
function octopus_add_instance_premiacao(stdClass $octopus, mod_octopus_mod_form $mform = null) {
    global $DB;

    $octopus->timecreated = time();

    // You may have to add extra stuff in here.

    $octopus->id = $DB->insert_record('octopus', $octopus);

    octopus_grade_item_update_premiacao($octopus);

    return $octopus->id;
}

/**
 * Updates an instance of the octopus in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $octopus An object from the form in mod_form.php
 * @param mod_octopus_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function octopus_update_instance_premiacao(stdClass $octopus, mod_octopus_mod_form $mform = null) {
    global $DB;

    $octopus->timemodified = time();
    $octopus->id = $octopus->instance;

    // You may have to add extra stuff in here.

    $result = $DB->update_record('octopus', $octopus);

    octopus_grade_item_update_premiacao($octopus);

    return $result;
}

/**
 * Removes an instance of the octopus from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function octopus_delete_instance_premiacao($id) {
    global $DB;

    if (!$octopus = $DB->get_record('octopus', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here.

    $DB->delete_records('octopus', array('id' => $octopus->id));

    octopus_grade_item_delete_premiacao($octopus);

    return true;
}
//======================================= NOTA PREMIACAO ==================================




/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $octopus The octopus instance record
 * @return stdClass|null
 */
function octopus_user_outline($course, $user, $mod, $octopus) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * It is supposed to echo directly without returning a value.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $octopus the module instance record
 */
function octopus_user_complete($course, $user, $mod, $octopus) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in octopus activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function octopus_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link octopus_print_recent_mod_activity()}.
 *
 * Returns void, it adds items into $activities and increases $index.
 *
 * @param array $activities sequentially indexed array of objects with added 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 */
function octopus_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0, $groupid = 0) {
}

/**
 * Prints single activity item prepared by {@link octopus_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added 'cmid' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users' full names
 */
function octopus_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function octopus_cron() {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * For example, this could be array('moodle/site:accessallgroups') if the
 * module uses that capability.
 *
 * @return array
 */
function octopus_get_extra_capabilities() {
    return array();
}

/* Gradebook API */

/**
 * Is a given scale used by the instance of octopus?
 *
 * This function returns if a scale is being used by one octopus
 * if it has support for grading and scales.
 *
 * @param int $octopusid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given octopus instance
 */
function octopus_scale_used($octopusid, $scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('octopus', array('id' => $octopusid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of octopus.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any octopus instance
 */
function octopus_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('octopus', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given octopus instance
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $octopus instance object with extra cmidnumber and modname property
 * @param bool $reset reset grades in the gradebook
 * @return void
 */
function octopus_grade_item_update(stdClass $octopus, $grades = false) {
    //os dados sao inseridos na tabela mdl_grade_items
    global $CFG;

    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }

    $params = array('itemname' => $octopus->name, 'idnumber' => $octopus->cmidnumber);

    if (!$octopus->assessed or $octopus->scale == 0) {
        $params['gradetype'] = GRADE_TYPE_NONE;
        //echo 'grade_item - if1 <br>';
    } else if ($octopus->scale > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $octopus->scale;
        $params['grademin']  = 0;

    } else if ($octopus->scale < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$octopus->scale;

    }

    if ($grades  === 'reset') {
        echo 'grade_item - notas: '.$grades. '<br>';
        $params['reset'] = true;
        $grades = NULL;
    }

    return grade_update('mod/octopus', $octopus->course, 'mod', 'octopus', $octopus->id, 0, $grades, $params);
}

/**
 * Delete grade item for given octopus instance
 *
 * @param stdClass $octopus instance object
 * @return grade_item
 */
function octopus_grade_item_delete($octopus) {
    global $CFG;
    require_once ($CFG->libdir . '/gradelib.php');

    return grade_update('mod/octopus', $octopus->course, 'mod', 'octopus', $octopus->id, 0, null, array('deleted' => 1));
}

function octopus_grade_item_delete_premiacao($octopus) {
    global $CFG;
    require_once ($CFG->libdir . '/gradelib.php');

    return grade_update('mod/octopus', $octopus->course, 'mod', 'octopus', $octopus->id, 0, null, array('deleted' => 1));
}

/**
 * Update octopus grades in the gradebook
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $octopus instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 */
function octopus_update_grades(stdClass $octopus, $userid = 0, $nullifnone = true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    if (!$octopus->assessed) {
        octopus_grade_item_update($octopus);

        //echo 'update_grades - if <br>';

    } else if ($grades = octopus_get_user_grades($octopus, $userid)) {
        octopus_grade_item_update($octopus, $grades);
//         } else if (
//        $grades = octopus_get_user_grades_premiacao($octopus,$userid)) {
//        octopus_grade_item_update($octopus, $grades);

    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = NULL;

        octopus_grade_item_update($octopus, $grade);

    } else {
        octopus_grade_item_update($octopus);
    }
}





function octopus_get_user_grades($octopus, $userid = 0) {
    global $CFG, $DB, $USER;

    if($userid) {
        $query = "  SELECT p.user_id as userid, AVG(p.grade) as grade
                    FROM {octopus_post} p
                    JOIN {octopus_thread} t ON t.id = p.thread_id
                    WHERE p.grade != -1 AND p.user_id = $userid AND t.cmid = $octopus->cmidnumber
                    GROUP BY p.user_id ";

    }
    else {
        $query = "  SELECT p.user_id as userid, AVG(p.grade) as grade
                    FROM {octopus_post} p
                    JOIN {octopus_thread} t ON t.id = p.thread_id
                    WHERE p.grade != -1 AND t.cmid = $octopus->cmidnumber
                    GROUP BY p.user_id ";
    }

    $grades = $DB->get_records_sql($query);

    foreach($grades as $key => $grade) {
        $grades[$key]->feedback = 'Manual';
        $grades[$key]->feedbackformat = 1; // FORMAT_HTML Plain HTML (with some tags stripped)
        $grades[$key]->rawgrade = $grade->grade; // A number that is limited to the maxgrade column setting in grade_items table
        $grades[$key]->octopus_id = $octopus->id; // The unique index id of your module's main DB table
        $grades[$key]->timemodified = time();
        $grades[$key]->userid = $grade->userid;
        $grades[$key]->usermodified = $USER->id;
    }

    return $grades;
}



//========================================
function octopus_get_grade_allwranking($cmid) {
    global $DB;

    $query = "  SELECT o.*
                FROM mdl_octopus o
                JOIN mdl_course_modules cm ON cm.instance = o.id
                WHERE cm.id = $cmid ";

    $octopus = $DB->get_record_sql($query);

    if($octopus->allowranking == 1)
        return true;
    else
        return false;

}
//funcao que testa se a notificacao por email esta ativada
function octopus_get_notification($cmid) {
    global $DB;

    $query = " SELECT o.*
                FROM mdl_octopus o
                JOIN mdl_course_modules cm ON cm.instance = o.id
                WHERE cm.id = $cmid ";

    $octopus = $DB->get_record_sql($query);

    if($octopus->notification == 1)
        return true;
    else
        return false;

}



function octopus_get_student_notcreatethreadsusers($cmid) {
    global $DB;

    $query = "  SELECT o.*
                FROM mdl_octopus o
                JOIN mdl_course_modules cm ON cm.instance = o.id
                WHERE cm.id = $cmid ";

    $octopus = $DB->get_record_sql($query);

    //1 - permite user para criar threads
    //0 - nao permite aluno user criar threads
    if($octopus->allowusercreatethreads == 1)
        return true;
    else
        return false;

}


function octopus_grade_item_update_premiacao(stdClass $octopus, $grades = false) {
    //os dados sao inseridos na tabela mdl_grade_items
    global $CFG;

    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }

    $params = array('itemname' => $octopus->name, 'idnumber' => $octopus->cmidnumber);

    if (!$octopus->assessed or $octopus->scale == 0) {
        $params['gradetype'] = GRADE_TYPE_NONE;
        //echo 'grade_item - if1 premiacao<br>';
    } else if ($octopus->scale > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $octopus->scale;
        $params['grademin']  = 0;

    } else if ($octopus->scale < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$octopus->scale;
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }

    return grade_update('mod/octopus', $octopus->course, 'mod', 'octopus', $octopus->id, 0, $grades, $params);
}




function octopus_update_grades_premiacao(stdClass $octopus, $userid = 0, $nullifnone = true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    if (!$octopus->assessed) {
        octopus_get_user_grades_premiacao($octopus);

         } else if (
        $grades = octopus_get_user_grades_premiacao($octopus,$userid)) {
        octopus_grade_item_update($octopus, $grades);


    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = NULL;

        octopus_get_user_grades_premiacao($octopus, $grade);

    } else {
        octopus_get_user_grades_premiacao($octopus);
    }
}

//chamada de funcao p calculo de premiacao
function octopus_get_user_grades_premiacao($octopus, $userid = 0) {
    global $CFG, $DB, $USER;

    if($userid) {
        $query = "SELECT user_id, recompensa_total as grade, cmid
                    FROM {octopus_reward_users}
                    WHERE user_id = $userid AND cmid = $octopus->cmidnumber
                    ";

    }
    else {
        $query = "SELECT user_id, recompensa_total as grade, cmid
                    FROM {octopus_reward_users}
                    WHERE cmid = $octopus->cmidnumber ";
    }

    $grades = $DB->get_records_sql($query);
    foreach($grades as $key => $grade) {

        $grades[$key]->feedback = 'Premiacao';
        $grades[$key]->feedbackformat = 1; // FORMAT_HTML Plain HTML (with some tags stripped)
        $grades[$key]->rawgrade = $grade->grade; // A number that is limited to the maxgrade column setting in grade_items table
        $grades[$key]->octopus_id = $octopus->id; // The unique index id of your module's main DB table
        $grades[$key]->timemodified = time();
        $grades[$key]->userid = $grade->user_id;
        $grades[$key]->usermodified = $USER->id;

    }

    return $grades;
}




function octopus_get_cmid_grade($user_id,$course_id){
    global $DB;

    //SELECT DISTINCT c.id as id_curso, rs.userid, c.fullname, m.id as id_modulo, o.name,
    $query = " SELECT DISTINCT cm.id as cmid
                FROM mdl_role_assignments rs
                INNER JOIN mdl_context e ON rs.contextid=e.id
                INNER JOIN  mdl_course c ON c.id = e.instanceid
                INNER JOIN mdl_course_modules cm ON cm.course = c.id
                INNER JOIN mdl_modules m ON m.id = cm.module
                INNER JOIN mdl_octopus o ON o.course = c.id
                WHERE rs.userid = ".$user_id." AND m.name = 'octopus' AND c.id = ".$course_id." AND cm.course = ".$course_id." GROUP BY cm.id ";


    $cmid = $DB->get_records_sql($query);
    $cmid->cmid = $cmid->cmid;

    return $cmid;
}



//===================================================================

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function octopus_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for octopus file areas
 *
 * @package mod_octopus
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function octopus_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the octopus file areas
 *
 * @package mod_octopus
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the octopus's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function octopus_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options = array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}

/* Navigation API */

/**
 * Extends the global navigation tree by adding octopus nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the octopus module instance
 * @param stdClass $course current course record
 * @param stdClass $module current octopus instance record
 * @param cm_info $cm course module information
 */
function octopus_extend_navigation(navigation_node $navref, stdClass $course, stdClass $module, cm_info $cm) {
    // TODO Delete this function and its docblock, or implement it.
}

/**
 * Extends the settings navigation with the octopus settings
 *
 * This function is called when the context for the page is a octopus module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav complete settings navigation tree
 * @param navigation_node $octopusnode octopus administration node
 */
function octopus_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $octopusnode = null) {
    // TODO Delete this function and its docblock, or implement it.
}

// ==================== NOSSAS FUNÇÕES A PARTIR DAQUI ====================

$path_moodle = $CFG->dirroot;
//caminho base do moodle;

function octopus_redirect($url) {
    header("Location: $url");
    exit();
}




function octopus_get_user($user_id) {
    global $DB;

    $user = $DB->get_record('user', array('id' => $user_id), 'id, username, firstname, lastname, email, picture');
    return $user;
}

function octopus_get_post($post_id) {
    global $DB;

    $post = $DB->get_record('octopus_post', array('id' => $post_id));
    return $post;
}

function octopus_get_thread_tags($thread_id) {
    global $DB;

    $head = octopus_get_thread_head($thread_id);
    $query = "  SELECT t.*
                FROM {octopus_tag} t
                JOIN {octopus_post_has_tag} pht ON t.id = pht.tag_id
                WHERE pht.post_id = $head->id ";

    $tags = $DB->get_records_sql($query);

    return $tags;
}




function octopus_get_threads($cmid) {
    global $DB;

    $posts = $DB->get_records('octopus_thread', array('cmid' => $cmid));
    return $posts;
}




function octopus_get_threads_list($cmid, $maximo, $inicio, $start = null, $end = null) {
    global $DB;
    $t0 = time();

    $query = "  SELECT t.*
                FROM {octopus_thread} t
                JOIN {octopus_post} p ON p.thread_id = t.id
                WHERE cmid = $cmid AND p.is_head = 1
                ORDER BY p.timecreated DESC
                LIMIT $inicio, $maximo ";


    //$threads = $DB->get_records('octopus_thread', array('cmid' => $cmid));
    $threads = $DB->get_records_sql($query);

    foreach ($threads as $thread) {
        $head = octopus_get_thread_head($thread->id);
        $user = octopus_get_user($head->user_id);
        $likes = octopus_get_thread_likes($thread->id, 1);
        $dislikes = octopus_get_thread_likes($thread->id, 0);
        $posts = octopus_get_num_posts($thread->id);
        $tag = octopus_get_thread_tags($thread->id);

        $thread->user = $user->firstname . ' ' . $user->lastname;
        $thread->user_id = $user->id;
        $thread->timecreated = $head->timecreated;
        $thread->type = $head->type_message;
        $thread->likes = $likes;
        $thread->dislikes = $dislikes;
        $thread->posts = $posts;
        $thread->tags = $tag;
    }

    if(isset($start) && isset($end)) {
        $arr = array();
        foreach($threads as $thread)
            if($thread->timecreated > $start && $thread->timecreated < $end)
                $arr[] = $thread;
        $threads = $arr;
    }

    //return array_reverse($threads);
    return $threads;
}

function get_posts_from_contacts($cmid, $user_id, $maximo, $inicio, $start = null, $end = null){
    
    global $DB;
    $t0 = time();
    $arr = array();
    
    $c_query = "SELECT user_id2
                 FROM {octopus_contact}
                 WHERE user_id1 = $user_id and cmid = $cmid";
    
    $contacts =  $DB->get_records_sql($c_query);
    
    
    
    foreach ($contacts as $c){
           
        
            $query = "  SELECT t.*
                FROM {octopus_thread} t
                JOIN {octopus_post} p ON p.thread_id = t.id
                WHERE cmid = $cmid AND p.is_head = 1 AND p.user_id = $c->user_id2
                ORDER BY p.timecreated DESC
                LIMIT $inicio, $maximo ";


            //$threads = $DB->get_records('octopus_thread', array('cmid' => $cmid));
            $threads = $DB->get_records_sql($query);
            
            
            foreach ($threads as $thread) {
                $head = octopus_get_thread_head($thread->id);
                $user = octopus_get_user($head->user_id);
                $likes = octopus_get_thread_likes($thread->id, 1);
                $dislikes = octopus_get_thread_likes($thread->id, 0);
                $posts = octopus_get_num_posts($thread->id);
                $tag = octopus_get_thread_tags($thread->id);

                $thread->user = $user->firstname . ' ' . $user->lastname;
                $thread->user_id = $user->id;
                $thread->timecreated = $head->timecreated;
                $thread->type = $head->type_message;
                $thread->likes = $likes;
                $thread->dislikes = $dislikes;
                $thread->posts = $posts;
                $thread->tags = $tag;
                
                $arr[] = $thread;
                
            }

           
        
    }
    
     $threads = $arr;

    

    //return array_reverse($threads);
    return $threads;
    
}


function get_posts_from_contacts_total($cmid, $user_id, $start = null, $end = null){
    
    global $DB;
    $t0 = time();
    $arr = array();
    
    $c_query = "SELECT user_id2
                 FROM {octopus_contact}
                 WHERE user_id1 = $user_id";
    
    $contacts =  $DB->get_records_sql($c_query);
    
    
    
    foreach ($contacts as $c){
           
        
            $query = "  SELECT t.*
                FROM {octopus_thread} t
                JOIN {octopus_post} p ON p.thread_id = t.id
                WHERE cmid = $cmid AND p.is_head = 1 AND p.user_id = $c->user_id2
                ORDER BY p.timecreated DESC";


            //$threads = $DB->get_records('octopus_thread', array('cmid' => $cmid));
            $threads = $DB->get_records_sql($query);

            foreach ($threads as $thread) {
                                
                $arr[] = $thread;
                
            }

    }
    
     $threads = $arr;

    

    //return array_reverse($threads);
    return count($threads);
    
}



function octopus_get_threads_list_user($cmid, $maximo, $inicio, $user_id, $start = null, $end = null) {
    global $DB;
    $t0 = time();

    $query = "  SELECT t.*
                FROM {octopus_thread} t
                JOIN {octopus_post} p ON p.thread_id = t.id
                WHERE cmid = $cmid AND p.is_head = 1 AND p.user_id = $user_id
                ORDER BY p.timecreated DESC
                LIMIT $inicio, $maximo ";


    //$threads = $DB->get_records('octopus_thread', array('cmid' => $cmid));
    $threads = $DB->get_records_sql($query);

    foreach ($threads as $thread) {
        $head = octopus_get_thread_head($thread->id);
        $user = octopus_get_user($head->user_id);
        $likes = octopus_get_thread_likes($thread->id, 1);
        $dislikes = octopus_get_thread_likes($thread->id, 0);
        $posts = octopus_get_num_posts($thread->id);
        $tag = octopus_get_thread_tags($thread->id);

        $thread->user = $user->firstname . ' ' . $user->lastname;
        $thread->user_id = $user->id;
        $thread->timecreated = $head->timecreated;
        $thread->type = $head->type_message;
        $thread->likes = $likes;
        $thread->dislikes = $dislikes;
        $thread->posts = $posts;
        $thread->tags = $tag;
    }

    if(isset($start) && isset($end)) {
        $arr = array();
        foreach($threads as $thread)
            if($thread->timecreated > $start && $thread->timecreated < $end)
                $arr[] = $thread;
        $threads = $arr;
    }

    //return array_reverse($threads);
    return $threads;
}


function octopus_get_threads_list_user_total($cmid, $user_id, $start = null, $end = null) {
    global $DB;
    $t0 = time();

    $query = "  SELECT t.*
                FROM {octopus_thread} t
                JOIN {octopus_post} p ON p.thread_id = t.id
                WHERE cmid = $cmid AND p.is_head = 1 AND p.user_id = $user_id
                ORDER BY p.timecreated DESC";


    //$threads = $DB->get_records('octopus_thread', array('cmid' => $cmid));
    $threads = $DB->get_records_sql($query);

    foreach ($threads as $thread) {
        $head = octopus_get_thread_head($thread->id);
        $user = octopus_get_user($head->user_id);
        $likes = octopus_get_thread_likes($thread->id, 1);
        $dislikes = octopus_get_thread_likes($thread->id, 0);
        $posts = octopus_get_num_posts($thread->id);
        $tag = octopus_get_thread_tags($thread->id);

        $thread->user = $user->firstname . ' ' . $user->lastname;
        $thread->user_id = $user->id;
        $thread->timecreated = $head->timecreated;
        $thread->type = $head->type_message;
        $thread->likes = $likes;
        $thread->dislikes = $dislikes;
        $thread->posts = $posts;
        $thread->tags = $tag;
    }

    if(isset($start) && isset($end)) {
        $arr = array();
        foreach($threads as $thread)
            if($thread->timecreated > $start && $thread->timecreated < $end)
                $arr[] = $thread;
        $threads = $arr;
    }

    //return array_reverse($threads);
    return $threads;
}



function octopus_new_thread($cmid, $title) {
    global $DB;

    $newthread = new stdClass();
    $newthread->cmid = $cmid;
    $newthread->title = $title;

    return $DB->insert_record('octopus_thread', $newthread);
}



function octopus_get_thread($tid, $cmid) {
    global $DB;

    $thread = $DB->get_record('octopus_thread', array('id' => $tid, 'cmid' => $cmid));
    $thread->head = octopus_get_thread_head($tid);
    $thread->timecreated = $thread->head->timecreated;
    $thread->type = $thread->head->type_message;
    $thread->user = octopus_get_user($thread->head->user_id);
    $thread->posts = octopus_get_posts_from_thread($tid, false);
    $thread->tags = octopus_get_thread_tags($tid);
    $thread->count_likes = octopus_get_thread_likes($tid, 1);
    $thread->count_dislikes = octopus_get_thread_likes($tid, 0);
    $thread->count_posts = octopus_get_num_posts($thread->id);


    return $thread;
}




function octopus_get_posts_from_thread($tid, $post_search,  $first = true) {
    global $DB;
    
     
    if($post_search != 0){
       $post_query = " AND id =".$post_search;
       
    }else{
       $post_query = ""; 
       
    }

    //$posts = $DB->get_records('octopus_post', array('thread_id' => $tid));
    $posts = $DB->get_records_sql('SELECT * FROM mdl_octopus_post WHERE thread_id = '.$tid.' '.$post_query.' ORDER BY timecreated DESC');
    if(!$first) {
        // Retirando a head da thread.
        $posts = array_slice($posts, 0,-1);
    }

    foreach($posts as $post) {
        $post->user = octopus_get_user($post->user_id);
        $post->count_likes = octopus_get_post_likes_num($post->id);
        $post->count_dislikes = octopus_get_post_dislikes_num($post->id);
    }

    return $posts;
}





function octopus_new_post($message, $user_id, $type, $thread_id, $is_head = 0) {
    global $DB;

    $newpost = new stdClass();
    $newpost->message = utf8_encode($message);
    $newpost->timecreated = time();
    $newpost->user_id = $user_id;
    $newpost->type_message = $type;
    $newpost->thread_id = $thread_id;
    $newpost->is_head = $is_head;

    return $DB->insert_record('octopus_post', $newpost);
}




function octopus_new_private_message($message, $user_id, $to_id, $cmid){
    global $DB;

    $pmessage = new stdClass();
    $pmessage->private_message = $message;
    $pmessage->from_id = $user_id;
    $pmessage->to_id = $to_id;
    $pmessage->timecreated = time();
    //TIMEZONE

    $pmessage->flag = 0;
    $pmessage->cmid = $cmid;

    return $DB->insert_record('octopus_private_message', $pmessage);

}




function octopus_get_private_message($from_id, $to_id, $cmid){
    global $DB;

    $messages = $DB->get_records_sql('SELECT u.id, u.firstname, p.from_id, p.to_id, p.private_message, p.from_id, p.to_id, p.id, p.timecreated
                                    FROM mdl_octopus_private_message p
                                    INNER JOIN mdl_user u ON p.from_id = u.id
                                    INNER JOIN mdl_user u2 ON p.to_id = u2.id
                                    WHERE cmid = '.$cmid.' AND from_id = '.$from_id.' AND to_id = '.$to_id.' OR (from_id = '.$to_id.' AND to_id = '.$from_id.')' );


    return $messages;
}




function octopus_like_post($user_id, $post_id) {
    global $DB;

    $like = $DB->get_record('octopus_like', array('user_id' => $user_id, 'post_id' => $post_id));

    if ($like and $like->type == 0) {# ja existe dislike
        $like->type = 1;
        $like->timecreated = time();
        return $DB->update_record('octopus_like', $like);
    } elseif ($like and $like->type == 1) {# ja existe like
        return $DB->delete_records('octopus_like', array('user_id' => $user_id, 'post_id' => $post_id));
    } else {# ainda nao existe nada
        $like = new stdClass();
        $like->user_id = $user_id;
        $like->post_id = $post_id;
        $like->type = 1;
        $like->timecreated = time();

        return $DB->insert_record('octopus_like', $like);
    }
}





function octopus_dislike_post($user_id, $post_id) {
    global $DB;

    $like = $DB->get_record('octopus_like', array('user_id' => $user_id, 'post_id' => $post_id));

    if ($like and $like->type == 1) {# ja existe like
        $like->type = 0;
        $like->timecreated = time();
        return $DB->update_record('octopus_like', $like);
    } elseif ($like and $like->type == 0) {# ja existe dislike
        return $DB->delete_records('octopus_like', array('user_id' => $user_id, 'post_id' => $post_id));
    } else {# ainda nao existe nada
        $like = new stdClass();
        $like->user_id = $user_id;
        $like->post_id = $post_id;
        $like->type = 0;
        $like->timecreated = time();

        return $DB->insert_record('octopus_like', $like);
    }
}



function octopus_post_has_this_tag($tag_id) {
    global $DB;

//    $qnt = $DB->get_record('octopus_post_has_tag', array('tag_id' => $tag_id));

    $query = "  SELECT *
                FROM {octopus_post_has_tag}
                WHERE tag_id = $tag_id LIMIT 1; ";

    $records = $DB->get_records_sql($query);

    return count($records);
}

function octopus_get_post_likes_num($post_id) {
    global $DB;

    $likes = $DB->get_records('octopus_like', array('post_id' => $post_id, 'type' => 1));
    return count($likes);
}





function octopus_get_post_dislikes_num($post_id) {
    global $DB;

    $dislikes = $DB->get_records('octopus_like', array('post_id' => $post_id, 'type' => 0));
    return count($dislikes);
}




function octopus_online_users($cmid) {
    global $DB;

    $t = time() - (5 * 60);
    $query = "  SELECT DISTINCT(user_id)
                FROM {octopus_log}
                WHERE time >= $t AND cmid = $cmid ";

    $records = $DB->get_records_sql($query);

    return count($records);
}

#Pesquisa retorna  posts, tags, titulo e posts relacionados a busca feita com os parametros passados
function octopus_filter_params() {
}





#retorna a quantidade de likes e dislikes de uma thread
#type recebe 1 para likes, 0 para dislikes e 2 para todos
function octopus_get_thread_likes($tid, $type = 2) {
    global $DB;

    $posts = $DB->get_records('octopus_post', array('thread_id' => $tid,'is_head'=>1), '', 'id');
    //print_r($posts);

    $ids = array();
    foreach ($posts as $post) {
        $ids[] = $post->id;
    }
    
   

    if($type == 0 || $type == 1)
        $t = " type = $type AND ";
    else
        $t = '';
   
    $ids_imploded = implode(',', $ids);
    
    
    
    $likes = $DB->get_records_sql("SELECT * FROM {octopus_like} WHERE ".$t." post_id IN (".$ids_imploded." ) ");

    return count($likes);
}




#retorna a quantidade posts de uma thread
function octopus_get_num_posts($posts) {
    global $DB;

    $numposts = $DB->get_records('octopus_post', array('thread_id' => $posts));

    return count($numposts) - 1; // Subtrai o primeiro post
}




#recupera status de anonimato do aluno.
function octopus_get_visible_flag($user, $cmid) {
    global $DB;

    return $DB->get_record_sql('SELECT p.private, u.firstname FROM mdl_octopus_private_profile p, mdl_user u
                                 WHERE p.user_id = ' . $user . ' AND p.cmid = ' . $cmid . ' AND u.id = ' . $user . ' ');
}




/* ALTERA A VISIBILIDADE DAS INFORMAÇÕES DO USUÁRIO. SE VISÍVEL TROCA PARA INVISÍVEL, E VIRSE VERSA*/
function octopus_set_visible_flag($user_id, $cmid){
    global $DB;

    $numprofile = $DB->get_record('octopus_private_profile', array('user_id' => $user_id, 'cmid' => $cmid));

    if ($numprofile->private == 0) {

        $update = "UPDATE mdl_octopus_private_profile SET private = 1 WHERE user_id = $user_id AND cmid = $cmid";
        return $DB->execute($update);

    }elseif($numprofile->private == 1){
        $update = "UPDATE mdl_octopus_private_profile SET private = 0 WHERE user_id = $user_id AND cmid = $cmid";
        return $DB->execute($update);

    } elseif($numprofile->private == '') {
//         $profile = new stdClass();
//         $profile->user_id = $user_id;
//         $profile->private = 1;
//         $profile->cmid = $cmid;
//         return $DB->insert_record('octopus_private_profile', $profile);
    }

}



//CADASTRA INFOS DEFAULT QUANDO UMA NOVA INSTACIA DO OCTOPUS É CRIADA. ESTA FUNCAO DEFINE COMO PADRÃO AS INFORMAÇES DO USUÁRIO COMO INVISIVEL NO RANKING
function octopus_set_profile($user_id,$cmid){
    global $DB;
    $numprofile = $DB->get_records('octopus_private_profile', array('user_id' => $user_id, 'cmid' => $cmid));

     $numprofile=count($numprofile);

     if($numprofile == 0){
         $profile = new stdClass();
         $profile->user_id = $user_id;
         $profile->private = 1;
         $profile->cmid = $cmid;
         return $DB->insert_record('octopus_private_profile', $profile);

     }
}



function octopus_set_activities($cmid){
    global $DB;
    $numprofile = $DB->get_records('octopus_set_peso_activities', array('cmid' => $cmid));
    $numprofile=count($numprofile);

    if($numprofile == 0){
         $profile = new stdClass();
         $profile->cmid = $cmid;
         $profile->peso_like = 1;
         $profile->peso_comentario = 1;
         $profile->peso_pergunta = 1;
         $profile->course_id = 0;
         return $DB->insert_record('octopus_set_peso_activities', $profile);

     }
}



//INFORMAÇÃO DO USUÁRIO NO RANKING. TORNA AS INFORMAÇÕES ( NOME DO USUÁRIO) VISÍVEL NO RANKING
function octopus_set_not_visible_flag($user_id, $cmid){
    global $DB;

    $numprofile = $DB->get_records('octopus_private_profile', array('user_id' => $user_id, 'cmid' => $cmid));
    if ($numprofile) {

        $update = "UPDATE mdl_octopus_private_profile SET private = 0 WHERE user_id = $user_id AND cmid = $cmid";
        return $DB->execute($update);

    } else {
        $profile = new stdClass();
        $profile->user_id = $user_id;
        $profile->private = 0;
        $profile->cmid = $cmid;
        return $DB->insert_record('octopus_private_profile', $profile);
    }

}



/* PREENCHE O FILTRO VISÃO ALUNO E RELATÓRIO COM OS ESTADOS*/
function octopus_get_estados_ajax($cod_regiao){
    global $DB;

    $query = " SELECT id, nome FROM quest_estados WHERE id_regiao = ".$cod_regiao." ORDER BY nome ";

    $sql = $DB->get_records_sql($query);
    return $sql;

}


/* PREENCHE O FILTRO VISÃO ALUNO E RELATÓRIO COM AS CIDADES*/
function octopus_get_cidades_ajax($cod_estados){
    global $DB;

    $query = " SELECT id, nome FROM quest_cidades WHERE estados_id = ".$cod_estados." ORDER BY nome ";

    $sql = $DB->get_records_sql($query);
    return $sql;

}


/* RETORNA INFORMAÇÕES DA TAG PESQUISADA/SELECIONADA */
function octopus_get_tag_info($user, $cmid) {
    global $DB;

    $infos = $DB->get_records_sql('UPDATE mdl_octopus_private_profile p SET private = 0 WHERE p.user_id = ' . $user . ' AND p.cmid = ' . $cmid . ' ');
    return $infos;

}




/* RETORNA A TAG COM MAIS CURTIDAS E IMPRIMA NA CORTINA DE TAGS*/
 function octopus_get_threads_tag_cortina($cmid,$tag_id){
   global $DB;

   $query = " SELECT  t.id as thread_id
              FROM mdl_octopus_thread t
              JOIN mdl_octopus_post p ON p.thread_id = t.id
              LEFT JOIN mdl_user u ON u.id = p.user_id
              LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
              LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
              LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
              LEFT JOIN quest_cidades c ON c.id = ue.municipio
              LEFT JOIN quest_estados e ON e.id = ue.estado
              WHERE t.cmid = ".$cmid." AND tag.id = ".$tag_id."  ";

   $sql = $DB->get_records_sql($query);
   return $sql;

 }



// EXIBE A POSICAO DO ALUNO NO RANKING
// function octopus_position($cmid, $course_id, $user_id){
//     global $DB;

//     $position = $DB->get_records_sql('SELECT COUNT(*) + 1  as colocacao FROM mdl_octopus_points WHERE cmid = '.$cmid.' AND total_pnts > (SELECT total_pnts
//                                        FROM mdl_octopus_points WHERE user_id = '.$user_id.' AND course_id = '.$course_id.' AND cmid = '.$cmid.') ');
//     return $position;
// }
function octopus_position($cmid, $course_id, $user_id){
    global $DB;

    $position = $DB->get_records_sql('SELECT COUNT(*)+1 as colocacao FROM mdl_octopus_points WHERE cmid = '.$cmid.' AND total_pnts > (SELECT total_pnts
                                       FROM mdl_octopus_points WHERE user_id = '.$user_id.' AND course_id = '.$course_id.' AND cmid = '.$cmid.') ');
    

    foreach ($position as $colocacao) {
        $num= $colocacao->colocacao;
    }

    if($num==0){
        $position = $DB->get_records_sql('SELECT COUNT(*)+1 as colocacao FROM mdl_octopus_points WHERE cmid = '.$cmid.' AND total_pnts > (SELECT total_pnts
                                       FROM mdl_octopus_points WHERE user_id = '.$user_id.' AND course_id = '.$course_id.' AND cmid = '.$cmid.') ');
    }
    return $position;
}



/* CONTA A QUANTIDADE DE 'PONTOS' (mensagem, curtidas, comentários) DOS USUÁRIOS. */
function octopus_list_ranking_users($cmid){
    global $DB;

    $users = $DB->get_records_sql('SELECT CONCAT(u.firstname, " ", u.lastname) as nome,
                        u.id as user_id,
                        count(p.message) as qnt_message,
                        count(l.post_id) as qnt_likes
                                    FROM mdl_role_assignments ra
                                    LEFT JOIN mdl_context ctx ON ctx.id = ra.contextid
                                    LEFT JOIN mdl_course c ON c.id = ctx.instanceid
                                    LEFT JOIN mdl_user u ON u.id = ra.userid
                                    LEFT JOIN mdl_course_modules cm ON cm.course = c.id
                                    LEFT JOIN mdl_modules m ON m.id = cm.module

                                    LEFT JOIN mdl_octopus_post p ON p.user_id = u.id
                                    LEFT JOIN mdl_octopus_thread tr ON tr.id = p.thread_id
                                    LEFT JOIN mdl_octopus_like l ON l.post_id = p.id

                                    /*LEFT JOIN (SELECT count(type_message) as comentarios, thread_id as t, user_id FROM mdl_octopus_post WHERE type_message = 3 GROUP BY user_id) as pp ON pp.t = tr.id*/

                                    WHERE cm.id = '.$cmid.' AND tr.cmid = '.$cmid.'  GROUP by u.id  ORDER by qnt_message DESC');

    return $users;
}




function octopus_list_ranking_users_comments($cmid, $user_id){
    global $DB;

    $user = $DB->get_records_sql('SELECT count(p.id) as comentarios, type_message  FROM mdl_octopus_post p
                                    INNER JOIN mdl_octopus_thread t ON t.id = p.thread_id
                                    WHERE type_message = 3 AND t.cmid = '.$cmid.' ');

    return $user;
}




function octopus_list_ranking_user($cmid, $user_id){
    global $DB;

    $user = $DB->get_records_sql('SELECT CONCAT(u.firstname, " ", u.lastname) as nome,
                        u.id as user_id,
                        count(p.message) as qnt_message
                        FROM mdl_user u
                        LEFT JOIN mdl_octopus_post p ON p.user_id = u.id
                        LEFT JOIN mdl_octopus_thread tr ON tr.id = p.thread_id
                        LEFT JOIN mdl_octopus_like l ON l.post_id = p.id
                        WHERE cmid = '.$cmid.' AND tr.cmid = '.$cmid.' AND p.user_id = '.$user_id.' AND type_message !=3 GROUP by u.id  ORDER by qnt_message DESC');

    return $user;
}



/* RETORNA A QUANTIDADE DE POSTAGENS - my_ranking.php */
function octopus_get_ranking_user_message($cmid, $user_id){
    global $DB;

    $user = $DB->get_records_sql('SELECT CONCAT(u.firstname, " ", u.lastname) as nome,
                        u.id as user_id,
                        count(p.message) as qnt_message
                        FROM mdl_user u
                        LEFT JOIN mdl_octopus_post p ON p.user_id = u.id
                        LEFT JOIN mdl_octopus_thread tr ON tr.id = p.thread_id
                        LEFT JOIN mdl_octopus_like l ON l.post_id = p.id
                        WHERE cmid = '.$cmid.' AND tr.cmid = '.$cmid.' AND p.user_id = '.$user_id.' AND type_message !=3 GROUP by u.id  ORDER by qnt_message DESC');

    return $user;
}



/* RETORNA A QUANTIDADE DE CURTIDAS - my_ranking.php */
function octopus_get_ranking_like_user($cmid, $user_id){
    global $DB;

    $count_likes = $DB->get_records_sql(" SELECT count(post_id) as qnt_like, p.id
                                FROM mdl_octopus_post p
                                LEFT JOIN mdl_octopus_like l ON l.post_id = p.id
                                LEFT JOIN mdl_octopus_thread t ON t.id = p.thread_id
                                where p.user_id = ".$user_id." AND l.type = 1 AND t.cmid = ".$cmid." ");

     return $count_likes;
}



/* RETORNA A QUANTIDADE DE COMENTARIOS - my_ranking.php */
function octopus_list_ranking_user_comments($cmid, $user_id){
    global $DB;

    $user = $DB->get_records_sql(' SELECT count(p.id) as comentarios, type_message  FROM mdl_octopus_post p
                                    INNER JOIN mdl_octopus_thread t ON t.id = p.thread_id
                                    WHERE type_message = 3 AND user_id = '.$user_id.' AND t.cmid = '.$cmid.' ');

    return $user;
}



function octopus_get_ranking_values($cmid, $atividade){
    global $DB;

    $ranking = $DB->get_records_sql('SELECT * FROM mdl_octopus_set_peso_grade
        WHERE cmid = '.$cmid.' and itemname_id ='.$atividade.'');

    return $ranking;
}




function octopus_get_crud_ranking($cmid,$course_id){
    global $DB;

    $r = $DB->get_records_sql('SELECT g.peso_prova, a.id, cmid, a.peso_like, a.peso_comentario, a.peso_pergunta, g.itemname_id
                                    FROM mdl_octopus_set_peso_activities a
                                    LEFT JOIN mdl_octopus_set_peso_grade g ON g.id_set_peso = a.id
                                    WHERE a.cmid = '.$cmid.' AND a.course_id = '.$course_id.' ');

    return $r;
}



/* CADSTRA OS PESOS DO RANKING */
function octopus_peso_ranking($cmid, $curtida, $resposta, $pergunta, $prova, $atividade, $course_id){
    global $DB;

    $r = $DB->get_record('octopus_set_peso_activities',array('cmid' => $cmid));

    if($r){
        
        $update = 'UPDATE mdl_octopus_set_peso_activities
                   SET peso_like = '.$curtida.',
                   peso_comentario = '.$resposta.',
                   peso_pergunta = '.$pergunta.'
                   WHERE cmid = '.$cmid.' ';

        return $DB->execute($update);


    }else{
        
        $ranking = new stdClass();
        $ranking->cmid = $cmid;
        $ranking->peso_like = $curtida;
        $ranking->peso_comentario = $resposta;
        $ranking->peso_pergunta = $pergunta;
        $ranking->course_id = $course_id;
        $ranking->itemname_id = 0;

        return $DB->insert_record('octopus_set_peso_activities',$ranking);

    }
}


function octopus_update_peso_ranking($cmid, $curtida, $resposta, $pergunta, $prova, $atividade, $course_id){
    global $DB;

    $r = $DB->get_record('octopus_set_peso_activities',array('cmid' => $cmid));

    if($r){
        
        $update = 'UPDATE mdl_octopus_set_peso_activities
                   SET peso_like = '.$curtida.',
                   peso_comentario = '.$resposta.',
                   peso_pergunta = '.$pergunta.'
                   WHERE cmid = '.$cmid.' ';

        return $DB->execute($update);


    }
}


function octopus_set_peso_grade($atividade,$cmid,$course_id,$peso_prova){
   global $DB;

    $query = $DB->get_record('octopus_set_peso_grade', array('cmid' => $cmid));
    if($query){
        $update = "UPDATE mdl_octopus_set_peso_grade SET
                   peso_prova = ".$peso_prova.",itemname_id=".$atividade."  WHERE cmid = ".$cmid." ";

        return $DB->execute($update);

    }else{
        $data = new stdClass();
        $data->itemname_id = $atividade; //id da atividade do curso no moodle
        $data->cmid = $cmid;
        $data->course_id = $course_id;
        $data->peso_prova = $peso_prova;

        return $DB->insert_record('octopus_set_peso_grade',$data);
    }

}


function octopus_set_reward($cmid, $course_id, $autorizar_data, $flag, $curtida, $curtidaQnt, $comentario, $comentarioQnt, $postagens, $postagensQnt){
    global $DB;

    $data = $DB->get_record('octopus_reward', array('cmid' => $cmid, 'course_id' => $course_id));
    if($data){

        $update = "UPDATE mdl_octopus_reward SET
                    flag = $flag,
                    curtida = $curtida,
                    curtidaQnt = $curtidaQnt,
                    comentario = $comentario,
                    comentarioQnt = $comentarioQnt,
                    postagens = $postagens,
                    postagensQnt = $postagensQnt
                    WHERE cmid = $cmid AND course_id = $course_id ";

        return $DB->execute($update);

    }else{

        $data = new stdClass();
        $data->cmid = $cmid;
        $data->course_id = $course_id;
        $data->timecreated = time();
        $data->timeauthorize = $autorizar_data;
        $data->flag = 0;
        $data->curtida = $curtida;
        $data->curtidaQnt = $curtidaQnt;
        $data->comentario = $comentario;
        $data->comentarioQnt = $comentarioQnt;
        $data->postagens = $postagens;
        $data->postagensQnt = $postagensQnt;

        return $DB->insert_record('octopus_reward', $data);
    }

}


/* FUNCAO PREENCHE OS CAMPOS DO FORMULARIO DE CADASTRO DE RECOMPENSA */
function octopus_get_reward($cmid){
    global $DB;

    $query = $DB->get_records_sql("SELECT timeauthorize, timecreated, curtida, curtidaqnt, comentario, comentarioqnt, postagens, postagensqnt
                                    FROM mdl_octopus_reward  WHERE cmid = ".$cmid." ");

    return $query;
}


/* SELECIONA A QUANTIDADE DE POSTS, COMENTÁRIOS E LIKES, CALCULA COM O PESO ATRIBUÍDO (RECOMPENSA) */
function octopus_get_reward_users($cmid,$user_id){
    global $DB;

    $query = $DB->get_records_sql(" SELECT timeauthorize, timecreated, curtida, curtidaqnt, comentario, comentarioqnt, postagens, postagensqnt,
                                    p.pnts_like, p.pnts_post, p.pnts_comentario, p.user_id
                                    FROM mdl_octopus_reward r
                                    LEFT JOIN mdl_octopus_points p ON p.cmid = r.cmid
                                    WHERE p.user_id = ".$user_id." AND r.cmid = ".$cmid." AND p.cmid = ".$cmid." ");

    return $query;
}



function octopus_save_reward_users($cmid,$user_id,$premiacao){
    global $DB;

    $query = $DB->get_record('octopus_reward_users', array('cmid' => $cmid, 'user_id' => $user_id));
    if($query){

        $update = " UPDATE mdl_octopus_reward_users SET recompensa_total = ".$premiacao." WHERE cmid = ".$cmid." AND user_id = ".$user_id." ";
        $DB->execute($update);
    }else{

        $data = new stdClass();
        $data->cmid = $cmid;
        $data->course_id = 1;
        $data->user_id = $user_id;
        $data->recompensa_total = $premiacao;

        $DB->insert_record('octopus_reward_users',$data);
    }
}

function octopus_get_type_grades($cmid){
     $query = $DB->get_record('octopus', array('cmid' => $cmid));
     return $query;
}



function octopus_get_pesos_ranking($cmid){
    global $DB;

    $query = $DB->get_records_sql("SELECT * FROM mdl_octopus_set_peso_activities WHERE cmid = ".$cmid." ");

    return $query;
}


/* RETORNA OS USUARIOS CADASTRADOS NO PLUGIN */
function octopus_get_users_plugin($cmid){
    global $DB;

    $users = $DB->get_records_sql("SELECT CONCAT(u.firstname, ' ', u.lastname) as nome, u.id as user_id2
                                    FROM mdl_role_assignments ra
                                    INNER JOIN mdl_context ctx ON ctx.id = ra.contextid
                                    INNER JOIN mdl_course c ON c.id = ctx.instanceid
                                    INNER JOIN mdl_user u ON u.id = ra.userid
                                    INNER JOIN mdl_course_modules cm ON cm.course = c.id
                                    INNER JOIN mdl_modules m ON m.id = cm.module
                                    WHERE cm.id = ".$cmid." ");

    return $users;
}


function octopus_verify($cmid, $course_id){
    global $DB;

    $sql = $DB->get_records_sql('SELECT rd.curtida, rd.curtidaQnt, rd.postagens, rd.postagensQnt, rd.comentario, rd.comentarioQnt, p.pnts_like, p.pnts_comentario,
                p.pnts_post, p.total_pnts, p.user_id, u.id, u.firstname
                                FROM mdl_user u
                                LEFT JOIN mdl_octopus_points p ON p.user_id = u.id
                                LEFT JOIN mdl_octopus_reward rd ON rd.course_id = p.course_id
                                WHERE rd.cmid = '.$cmid.' AND p.course_id = '.$course_id.' ');

    return $sql;

}



function octopus_save_user_points($cmid,$course_id,$user_id,$like,$pnt_post,$pnt_comentario){
    global $DB;
    if($pnt_post){
        
    }else{
        $pnt_post=0;
    }

    $data = $DB->get_record('octopus_points', array('cmid' => $cmid, 'user_id' => $user_id, 'course_id' => $course_id));

    if($data->cmid == $cmid && $data->user_id == $user_id && $data->course_id == $course_id){

        $total_pnts = $like + $pnt_post + $pnt_comentario;

        $update = "UPDATE {octopus_points} SET
                    pnts_like = {$like},
                    pnts_post = {$pnt_post},
                    pnts_comentario = {$pnt_comentario},
                    total_pnts = {$total_pnts}
                    WHERE cmid = {$cmid} AND user_id = {$user_id} AND course_id = {$course_id} ";


       //print_r($update);
       return $DB->execute($update);

    }else{
        $total_pnts = $like + $pnt_post + $pnt_comentario;
        $pnts = new stdClass();
        $pnts->cmid = $cmid;
        $pnts->course_id = $course_id;
        $pnts->user_id = $user_id;
        $pnts->pnts_like = $like;
        $pnts->pnts_post = $pnt_post;
        $pnts->pnts_comentario = $pnt_comentario;
        $pnts->total_pnts = $total_pnts;

        //print_r($pnts);
        return $DB->insert_record('octopus_points', $pnts);
    }


}


/* RETORNA A PONTUACAO DO ALUNO NO RANKING */
function octopus_get_user_point($cmid,$user_id,$course_id){
    global $DB;

    $p = $DB->get_records_sql(' SELECT format(pnts_post * r.peso_pergunta, 2) as pnt_post, total_pnts,
                                format(pnts_like * r.peso_like, 2) as pnt_like,
                                format(pnts_comentario * r.peso_comentario, 2) as pnt_comentario, p.user_id
                                FROM mdl_octopus_points p
                                LEFT JOIN mdl_octopus_set_peso_activities r ON r.cmid = p.cmid
                                WHERE p.cmid = '.$cmid.' AND p.user_id = '.$user_id.' AND p.course_id = '.$course_id.'
                                AND r.cmid = '.$cmid.' ' );

    return $p;

}


/* RETORNA A PONTUACAO DO "ALUNOS" NO RANKING */
function octopus_get_users_point($cmid,$course_id){
    global $DB;

    $p = $DB->get_records_sql('SELECT distinct CONCAT(u.firstname, " ", u.lastname) as nome, total_pnts,
                            format(pnts_post * r.peso_pergunta, 2) as pnts_post,
                            format(pnts_like * r.peso_like, 2) as pnts_like,
                            format(pnts_comentario * r.peso_comentario, 2) as pnts_comentario,
                            p.user_id, pp.private,p.user_id
                        FROM mdl_octopus_points p
                        LEFT JOIN mdl_user u ON u.id = p.user_id
                        LEFT JOIN mdl_octopus_set_peso_activities r ON r.cmid = p.cmid
                        LEFT JOIN mdl_octopus_private_profile pp ON pp.user_id = u.id
                        WHERE p.cmid = '.$cmid.' AND p.course_id = '.$course_id.'
                        AND r.cmid = '.$cmid.' AND pp.cmid = '.$cmid.'
                        ORDER BY total_pnts DESC');

    return $p;

}


/* RETORNA A PONTUACAO DO "ALUNOS" NO RANKING */
function octopus_get_users_point_limit($cmid,$course_id, $inicio, $offset){
    global $DB;

    $p = $DB->get_records_sql('SELECT distinct CONCAT(u.firstname, " ", u.lastname) as nome, total_pnts,
                            format(pnts_post * r.peso_pergunta, 2) as pnts_post,
                            format(pnts_like * r.peso_like, 2) as pnts_like,
                            format(pnts_comentario * r.peso_comentario, 2) as pnts_comentario,
                            p.user_id, pp.private,p.user_id
                        FROM mdl_octopus_points p
                        LEFT JOIN mdl_user u ON u.id = p.user_id
                        LEFT JOIN mdl_octopus_set_peso_activities r ON r.cmid = p.cmid
                        LEFT JOIN mdl_octopus_private_profile pp ON pp.user_id = u.id
                        WHERE p.cmid = '.$cmid.' AND p.course_id = '.$course_id.'
                        AND r.cmid = '.$cmid.' AND pp.cmid = '.$cmid.'
                        ORDER BY total_pnts DESC LIMIT '.$inicio.', '.$offset.'');

    return $p;

}



/* RETORNA OS PESOS DAS ATIVIDADES (LIKES, COMENTARIOS, POSTAGENS),
PARA EXIBIR COMO PARTE INFORMATIVA NA COLOCAÇÃO DO USUARIO E NA COLOCAÇÃO GERAL */
function octopus_get_activities($cmid){
    global $DB;

    return $DB->get_record('octopus_set_peso_activities', array('cmid' => $cmid));
}



/* RETORNA O(S) PESO(S) DA(S) ATIVIDADE(S) DO MOODLE, CASO EXISTA.
PARA SER EXIBIDA COMO PARTE INFORMATIVA NA COLOCAÇÃO DO USUARIO E NA COLOCAÇÃO GERAL */
function octopus_get_grade_activities($cmid){
    global $DB;

    return $DB->get_record('octopus_set_peso_grade', array('cmid' => $cmid));
}



/* VERIFICA NA TABELA DO MOODLE SE JÁ REALIZOU ALGUMA ATIVIDADE */
function octopus_save_user_moodle_grade($cmid,$course_id,$itemname_id,$user_id,$grade,$nome_atividade){
    global $DB;

    $data = $DB->get_record('octopus_points_grade_moodle', array('user_id' => $user_id, 'cmid' => $cmid));

    if($data->user_id == $user_id){

    }else{
        $data = new stdClass();
        $data->cmid = $cmid;
        $data->course_id = $course_id;
        $data->itemname_id = $itemname_id;
        $data->user_id = $user_id;
        $data->grade = $grade;
        $data->nome_atividade = $nome_atividade;


        return $DB->insert_record('octopus_points_grade_moodle', $data);
    }

}



/* RETORNA A NOTA DO ALUNO PARA EXIBIR NO RANKING */
function octopus_get_grade_user($course_id,$user_id){
    global $DB;

    $grades = $DB->get_records_sql('SELECT i.id as itemname_id, itemname, itemtype, courseid, finalgrade, userid
                                    FROM mdl_grade_items i
                                    LEFT JOIN
                                    (SELECT itemid, finalgrade, userid FROM mdl_grade_grades where userid = '.$user_id.' ) as g ON g.itemid = i.id
                                    WHERE i.courseid = '.$course_id.' ');

    return $grades;

}



/* PEGA A MEDA DOS ALUNOS NO MOODLE PARA EXIBIR NO RANKING*/
function octopus_get_grade_users($course_id){
    global $DB;

    $grade_users = $DB->get_records_sql('SELECT DISTINCT i.itemname, i.itemtype, format(g.finalgrade * gr.peso_prova,2) as media, g.userid, gr.peso_prova
                                    FROM mdl_grade_items i
                                    LEFT JOIN mdl_grade_grades g ON i.id=g.itemid
                                    LEFT JOIN mdl_octopus_set_peso_grade gr ON gr.course_id = i.courseid
                                    WHERE i.courseid = '.$course_id.' AND i.itemtype = "course" ');

    return $grade_users;
}


/* EXIBE AS ATIVIDADES PARA QUE O ADMINISTRADOR ATRIBUA PESO A CADA ATIVIDADE */
function octopus_activity($course_id){
    global $DB;

    $query = $DB->get_records_sql("SELECT id, itemname, itemtype, gradetype, scaleid, courseid FROM mdl_grade_items WHERE courseid = ".$course_id." AND itemname IS NOT NULL");
    return $query;
}


/* RETORNA AS REGIAOES PARA PREENCHER O SELECT BOX DOS FILTROS (VISAO USUARIO E ADMIN) */
function octopus_estados(){
    global $DB;

    $estados = $DB->get_records_sql('SELECT id, sigla, nome FROM quest_estados ORDER BY sigla');

    return $estados;
}


//RETORNA AS REGIAOES PARA PREENCHER O SELECT BOX DOS FILTROS (VISAO USUARIO E ADMIN)
 function octopus_regiao(){
    global $DB;

    $regiao = $DB->get_records_sql('SELECT id, sigla_regiao, nome_regiao FROM quest_regiao ORDER BY sigla_regiao');

    return $regiao;
}

//RETORNA OS PAÍSES PARA PREENCHER O SELECT BOX DOS FILTROS (VISAO USUARIO E ADMIN)
 function octopus_pais(){
    global $DB;

    $pais = $DB->get_records_sql('SELECT paisId, paisNome FROM quest_pais ORDER BY paisId');

    return $pais;
}


/* RETORNA AS REGIAOES PARA PREENCHER O SELECT BOX DOS FILTROS (VISAO USUARIO) */
function octopus_get_cbo(){
    global $DB;

    $cbo = $DB->get_records_sql('SELECT cbo_num, cbo_nome FROM quest_user_cbo ORDER BY cbo_nome');

    return $cbo;

}



//RETORNA QUANTAS VEZES A TAG FOI UTILIZADA. OU SEJA, EM QUANTOS POSTS ELA FOI UTILIZADA.
function octopus_count_tag_posts($cmid, $tag_id){
    global $DB;

    $contagem = 0;
    $tag_id = $tag_id + 0;

    $tags = $DB->get_records_sql('SELECT count(tag.tag_id) as qnt_tag
                                  FROM mdl_octopus_tag t
                                  INNER JOIN mdl_octopus_post_has_tag tag ON tag.tag_id = t.id
                                  WHERE t.cmid = '.$cmid.' AND t.id='.$tag_id);

    foreach ($tags as $object) {
        //echo $contagem;
        $contagem = $object->qnt_tag;
        //	echo " > ". $contagem;
    }

    return $contagem;

}


function octopus_get_users($cmid) {
    global $DB;

    $all_users = $DB->get_records_sql('SELECT user_id, u.firstname FROM mdl_octopus_post p INNER JOIN mdl_user u ON u.id = p.user_id
                                        INNER JOIN mdl_octopus_thread tr ON tr.id = p.thread_id
                                        WHERE tr.cmid = ' . $cmid . ' GROUP BY p.user_id ');

    return $all_users;
}


function octopus_get_users_name($cmid,$to_id){
    global $DB;

    $user = $DB->get_records_sql('SELECT distinct firstname FROM mdl_user u
        INNER JOIN mdl_octopus_private_message pm ON pm.to_id = u.id
        WHERE pm.cmid = '.$cmid.' AND to_id = '.$to_id.' ');

    foreach($user as $u){
        echo $u->firstname;
    }

}


#ESTA CONSULTA LISTA OS ALUNOS QUE ESTAO MATRICULADOS NO MODULO.
//function octopus_get_contacts_module($cmid, $user_id){
//    global $DB;
//
//    $t = time() - (5 * 60);
//
//    $query = "  SELECT CONCAT(u.firstname, ' ', u.lastname) as nome, qnt_messages, u.id as user_id2, from_id,
//                pm.to_id as sent_message_to,
//                IF(u.id IN (SELECT DISTINCT user_id
//                            FROM {octopus_log}
//                            WHERE time > $t AND cmid = $cmid), 'online', 'offline') as status
//                FROM {role_assignments} ra
//                INNER JOIN {context} ctx ON ctx.id = ra.contextid
//                INNER JOIN {course} c ON c.id = ctx.instanceid
//                INNER JOIN {user} u ON u.id = ra.userid
//                INNER JOIN {course_modules} cm ON cm.course = c.id
//                INNER JOIN {modules} m ON m.id = cm.module
//                LEFT JOIN (
//                    SELECT count(private_message) as qnt_messages, from_id, to_id, flag
//                    FROM {octopus_private_message}
//                    WHERE cmid = $cmid and to_id = $user_id and flag = 0
//                ) as pm ON pm.from_id = u.id
//                LEFT JOIN {octopus_log} log ON log.user_id = u.id
//
//                WHERE cm.id = $cmid and u.id != $user_id GROUP by u.id ";
//
//    $users = $DB->get_records_sql($query);
//
//    return $users;
//}


//function octopus_get_contacts_module($cmid, $user_id){
//    global $DB;
//
//    $t = time() - (5 * 60);
//
//    $query = "  SELECT CONCAT(u.firstname, ' ', u.lastname) as nome, qnt_messages, u.id as user_id2, from_id,
//                pm.to_id as sent_message_to 
//                 IF(u.id IN (SELECT DISTINCT user_id
//                            FROM {octopus_online}
//                            WHERE time > $t AND cmid = $cmid), 'online', 'offline') as status
//                FROM {octopus_user_preferences} up                
//                INNER JOIN {user} u ON u.id = up.user_id                
//                LEFT JOIN (
//                    SELECT count(private_message) as qnt_messages, from_id, to_id, flag
//                    FROM {octopus_private_message}
//                    WHERE cmid = $cmid and to_id = $user_id and flag = 0
//                ) as pm ON pm.from_id = u.id
//                LEFT JOIN {octopus_log} log ON log.user_id = u.id
//                WHERE up.cmid = $cmid and u.id != $user_id GROUP by u.id ";
//
//    $users = $DB->get_records_sql($query);
//
//    return $users;
//}

function octopus_get_contacts_module($cmid, $user_id){
    global $DB;

    $t = time() - (5 * 60);

    $query = "  SELECT CONCAT(u.firstname, ' ', u.lastname) as nome, qnt_messages, u.id as user_id2, from_id,
                pm.to_id as sent_message_to,
                IF(u.id IN (SELECT DISTINCT user_id
                            FROM {octopus_online}
                            WHERE time > $t AND cmid = $cmid), 'online', 'offline') as status
                FROM {role_assignments} ra
                INNER JOIN {context} ctx ON ctx.id = ra.contextid
                INNER JOIN {course} c ON c.id = ctx.instanceid
                INNER JOIN {user} u ON u.id = ra.userid
                INNER JOIN {course_modules} cm ON cm.course = c.id
                INNER JOIN {modules} m ON m.id = cm.module
                LEFT JOIN (
                    SELECT count(private_message) as qnt_messages, from_id, to_id, flag
                    FROM {octopus_private_message}
                    WHERE cmid = $cmid and to_id = $user_id and flag = 0
                ) as pm ON pm.from_id = u.id
                LEFT JOIN {octopus_online} onl ON onl.user_id = u.id

                WHERE cm.id = $cmid and u.id != $user_id GROUP by u.id ";

    $users = $DB->get_records_sql($query);

    return $users;
}


function octopus_get_my_contacts($cmid, $user_id1){
   global $DB;

    $t = time() - (5 * 60);

    $query = "  SELECT CONCAT(u.firstname, ' ', u.lastname) as nome, c.user_id2, pm.qnt_messages,
                IF(u.id IN (SELECT DISTINCT user_id
                            FROM mdl_octopus_online
                            WHERE time > $t AND cmid = $cmid), 'online', 'offline'
                ) as status
                FROM {octopus_contact} c
                JOIN {user} u ON u.id = c.user_id2
                LEFT JOIN (
                    SELECT count(private_message) as qnt_messages, from_id, to_id FROM mdl_octopus_private_message
                    WHERE cmid = $cmid AND to_id = $user_id1 AND flag = 0
                ) as pm ON pm.from_id = u.id
                WHERE c.user_id1 = $user_id1 AND c.cmid = $cmid";

    $contacts = $DB->get_records_sql($query);

    $user = core_user::get_user($user_id1);
    //email_to_user($user, $user, "query", '', $query);

   return $contacts;
}


function octopus_follow_contacts($cmid,$user_id1,$user_id2){
    global $DB;

    $follow = $DB->get_record('octopus_contact', array('user_id1' => $user_id1, 'user_id2' => $user_id2, 'cmid' => $cmid));

    if($follow->user_id1 == "" and $follow->user_id2 == ""){ //segue um contato
        $data = new stdClass();
        $data->user_id1 = $user_id1;
        $data->user_id2 = $user_id2;
        $data->cmid = $cmid;

        return $DB->insert_record('octopus_contact',$data);


    }elseif($follow->user_id2 == $user_id2){ //deixa de seguir um contato.
        return $DB->delete_records('octopus_contact', array('user_id1' => $user_id1, 'user_id2' => $user_id2, 'cmid' => $cmid));

    }else{
        echo 'null';
    }

}

function octopus_my_contact($cmid,$user_id1,$user_id2){
    global $DB;

    $follow = $DB->get_record('octopus_contact', array('user_id1' => $user_id1, 'user_id2' => $user_id2, 'cmid' => $cmid));

    if($follow->user_id1 == "" and $follow->user_id2 == ""){ //segue um contato
        
        return true;   

    }else{
        return false;
    }

}


/* vai exibir o total de msgs em cima da cortina. */
function octopus_notify_private_message($user_id, $cmid){
    global $DB;

    $notify = $DB->get_records_sql('SELECT id as num_notificacao, to_id, from_id FROM {octopus_private_message} WHERE flag = 0 AND to_id = '.$user_id.' AND cmid = '.$cmid.' GROUP BY from_id');

    return count($notify);

}


function octopus_private_message_received_notification($user_id,$cmid){
    global $DB;

    $flag = 0;

    $query = $DB->get_records_sql('SELECT distinct pm.id, pm.private_message, count(pm.from_id) as qtt_message, pm.flag, c.user_id1, c.user_id2 as user_sent FROM mdl_octopus_private_message pm
                                    INNER JOIN mdl_octopus_contact c ON c.user_id2 = pm.from_id
                                    WHERE pm.to_id = '.$user_id.' AND pm.flag = 0 AND pm.cmid = '.$cmid.' GROUP BY from_id');
    return $query;

}

#marca notificacao como lida
function octopus_read_notifications_private_message($user_id, $cmid) {
    global $DB;

    $query = "UPDATE {octopus_private_message} SET flag = 1 WHERE to_id = {$user_id} AND cmid = {$cmid} ";
    return $DB->execute($query);
}


#notidicar quando uma nova mensagem é recebida. Obs: equipe front implementar janelinha mágica aparecendo..
function octopus_push_notification() {

}


function octopus_search_contacts($search,$cmid){
    global $DB;

    $search = $DB->get_records_sql('SELECT u.firstname, u.id FROM mdl_octopus_contact c INNER JOIN mdl_user u ON u.id = c.user_id2 WHERE u.firstname like = "%' .$search. '%" AND c.cmid = '.$cmid.' ');
    return $search;
}


function octopus_search($search,$cmid){
    global $DB;
//    $search = utf8_encode($search);
   $search = $DB->get_records_sql('SELECT distinct p.id, u.firstname, p.message, tr.title, t.name_tag, p.user_id, tag.post_id, tr.id as thread, p.thread_id, p.timecreated, p.type_message, tag.tag_id, t.id as tag, tr.cmid, p.is_head
                                            FROM {octopus_post} p
                                            inner join {octopus_thread} tr on tr.id = p.thread_id
                                            INNER JOIN {user} u ON u.id = p.user_id
                                            left join {octopus_tag} t on t.cmid = tr.cmid
                                            left join {octopus_post_has_tag} tag on tag.tag_id = t.id
                                            WHERE tr.cmid = '.$cmid.' AND (p.message like "%'.$search.'%" COLLATE utf8_general_ci or tr.title like "%'.$search.'%" COLLATE utf8_general_ci or t.name_tag like "%'.$search.'%" COLLATE utf8_general_ci or u.firstname like "%'.$search.'%" COLLATE utf8_general_ci or t.name_tag like "%'.$search.'%" COLLATE utf8_general_ci)
                                            group by p.message ORDER BY p.timecreated DESC');

    return $search;
}



function octopus_search_limited($search,$cmid, $inicio, $offset){
    global $DB;
//    $search = utf8_encode($search);
   $search = $DB->get_records_sql('SELECT distinct p.id, u.firstname, p.message, tr.title, t.name_tag, p.user_id, tag.post_id, tr.id as thread, p.thread_id, p.timecreated, p.type_message, tag.tag_id, t.id as tag, tr.cmid, p.is_head
                                            FROM {octopus_post} p
                                            inner join {octopus_thread} tr on tr.id = p.thread_id
                                            INNER JOIN {user} u ON u.id = p.user_id
                                            left join {octopus_tag} t on t.cmid = tr.cmid
                                            left join {octopus_post_has_tag} tag on tag.tag_id = t.id
                                            WHERE tr.cmid = '.$cmid.' AND (p.message like "%'.$search.'%" COLLATE utf8_general_ci or tr.title like "%'.$search.'%" COLLATE utf8_general_ci or t.name_tag like "%'.$search.'%" COLLATE utf8_general_ci or u.firstname like "%'.$search.'%" COLLATE utf8_general_ci or t.name_tag like "%'.$search.'%" COLLATE utf8_general_ci)
                                            group by p.message ORDER BY p.timecreated DESC LIMIT '.$inicio.','.$offset.'');

    return $search;
}
    


#adiciona atividade na tabela log
function octopus_add_log_activity($uid, $cmid, $page, $ip) {
    global $DB;

    $log = new stdClass();

    $log->user_id = $uid;
    $log->cmid = $cmid;
    $log->page = $page;
    $log->ip = $ip;
    $log->time = time();
    octopus_online_time($uid, $cmid);
    //funcao que atualiza tabela online
    return $DB->insert_record('octopus_log', $log);
}

#adiciona atualiza a tabela online
function octopus_online_time($user_id, $cmid) {
    global $DB;

    if($online = $DB->get_records('octopus_online', array('cmid' => $cmid,'user_id' => $user_id)))
     {    
        $online_update = new stdClass();
        foreach ($online as $on){
            $online_update->id = $on->id;        
            $online_update->user_id = $on->user_id;
            $online_update->cmid = $on->cmid;        
            $online_update->time = time();     
    }      
       $DB->update_record('octopus_online', $online_update);            
    }
    else {
        $online = new stdClass();
        $online->user_id = $user_id;
        $online->cmid = $cmid;        
        $online->time = time();
        return  $DB->insert_record('octopus_online', $online);
    }
    
}

#retorna as notificações de um determinado usuario
#$opt = 1 (apenas nao lidas), 2 (apenas lidas) e 0 (todas)
function octopus_get_notifications($user_id, $opt = 0,$cmid) {
    global $DB;
    //echo "cmiiiid:".$cmid;
    if ($opt == 1) {
        $query =    "SELECT n.*, p.thread_id, t.title, p.message, CONCAT(u.firstname,' ', u.lastname) as from_name, n.flag " .
                    "FROM {octopus_notification} n " .
                    "JOIN {octopus_post} p ON n.post_id = p.id " .
                    "JOIN {user} u ON n.from_id = u.id " .
                    "JOIN {octopus_thread} t ON p.thread_id = t.id " .
                    "WHERE n.to_id = $user_id AND n.flag = 0 AND t.cmid = $cmid ORDER BY time DESC";
    }
    elseif ($opt == 2) {
        $query =    "SELECT n.*, p.thread_id, t.title, p.message, CONCAT(u.firstname,' ', u.lastname) as from_name, n.flag " .
                    "FROM {octopus_notification} n " .
                    "JOIN {octopus_post} p ON n.post_id = p.id " .
                    "JOIN {user} u ON n.from_id = u.id " .
                    "JOIN {octopus_thread} t ON p.thread_id = t.id " .
                    "WHERE n.to_id = $user_id AND n.flag = 1 AND t.cmid = $cmid ORDER BY time DESC";
    }
    else {
        $query =    "SELECT n.*, p.thread_id, t.title, p.message, CONCAT(u.firstname,' ', u.lastname) as from_name, n.flag " .
                    "FROM {octopus_notification} n " .
                    "JOIN {octopus_post} p ON n.post_id = p.id " .
                    "JOIN {user} u ON n.from_id = u.id " .
                    "JOIN {octopus_thread} t ON p.thread_id = t.id " .
                    "WHERE n.to_id = $user_id AND t.cmid = $cmid ORDER BY time DESC";
    }

    $notifications = $DB->get_records_sql($query);

    return $notifications;
}

function octopus_qntd_comentarios($cmid){
      global $DB;
      $query="Select DISTINCT thread_id as Thread, count(p.id) as qnt_post from mdl_octopus_post p "
              . "INNER JOIN mdl_octopus_thread t ON t.id = p.thread_id where type_message = 3 AND t.cmid = $cmid Group BY thread_id order by qnt_post desc limit 6";
      $likes = $DB->get_records_sql($query);
       foreach($likes as $thread) {
       $threads[] = octopus_get_thread($thread->thread, $cmid);
        }
      return $threads;
}

function octopus_qntd_curtidas($cmid){
      global $DB;
      $query="SELECT name_tag,count(l.post_id) as qnt_curtidas FROM mdl_octopus_tag t LEFT JOIN mdl_octopus_post_has_tag tag ON tag.tag_id = t.id LEFT JOIN mdl_octopus_post p ON p.id = tag.post_id LEFT JOIN mdl_user u ON u.id = p.user_id LEFT JOIN mdl_octopus_like l ON l.post_id = tag.post_id WHERE t.cmid = $cmid GROUP BY tag.tag_id ORDER BY qnt_curtidas DESC LIMIT 3";
      $likes = $DB->get_records_sql($query);
      return $likes;
}

#retorna a quantidade de notificações de um determinado usuario
#$opt = 1 (apenas nao lidas), 2 (apenas lidas) e 0 (todas)
function octopus_get_notifications_num($user_id, $opt = 1,$cmid) {
    global $DB;

    if ($opt == 1) {
        $query =    "SELECT n.* " .
                    "FROM {octopus_notification} n " .
                    "JOIN {octopus_post} p ON n.post_id = p.id " .
                    "JOIN {octopus_thread} t ON t.id = p.thread_id " .
                    "WHERE n.to_id = $user_id AND n.flag = 0 AND t.cmid = $cmid ";
    }
    elseif ($opt == 2) {
        $query =    "SELECT n.* " .
                    "FROM {octopus_notification} n " .
                    "JOIN {octopus_post} p ON n.post_id = p.id " .
                    "JOIN {octopus_thread} t ON t.id = p.thread_id " .
                    "WHERE n.to_id = $user_id AND n.flag = 1 AND t.cmid = $cmid ";
    }
    else {
        $query =    "SELECT n.* " .
                    "FROM {octopus_notification} n " .
                    "JOIN {octopus_post} p ON n.post_id = p.id " .
                    "JOIN {octopus_thread} t ON t.id = p.thread_id " .
                    "WHERE n.to_id = $user_id AND t.cmid = $cmid ";
    }

    $notifications = $DB->get_records_sql($query);

    return count($notifications);
}

#marca notificacao como lida
function octopus_read_notifications($user_id) {
    global $DB;

    $query = "UPDATE {octopus_notification} SET flag = 1 WHERE to_id = {$user_id} ";
    return $DB->execute($query);
}

#cadastra nova notificacao (type = 'comment', 'like', 'dislike')
function octopus_new_notification($from_id, $post_id, $type) {
    global $DB;

    $p = $DB->get_record('octopus_post', array('id'=>$post_id));
    // Condicao para nao gerar notificacao do proprio user
    if($p->user_id != $from_id) {
        $n = new stdClass();

        $n->from_id = $from_id;
        $n->to_id = $p->user_id;
        $n->post_id = $post_id;
        $n->type = $type;
        $n->time = time();
        $n->flag = 0;

        return $DB->insert_record('octopus_notification', $n);
    }
    else
        return false;
}

function octopus_get_thread_head($thread_id) {
    global $DB;

    $query = "SELECT * FROM {octopus_post} WHERE thread_id = $thread_id AND is_head = 1 LIMIT 1";
    $head = $DB->get_record_sql($query);

    $head->user = octopus_get_user($head->user_id);
    $head->count_likes = octopus_get_post_likes_num($head->id);
    $head->count_dislikes = octopus_get_post_dislikes_num($head->id);

    return $head;
}

function octopus_get_tags($cmid) {
    global $DB;

    $tags = $DB->get_records('octopus_tag', array('cmid' => $cmid), $sort='name_tag');

    return $tags;
}

function octopus_new_rec($name, $fonte, $link, $type, $tag, $cmid) {
    global $DB;

    $rec = new stdClass();
    $rec->name_rec = $name;
    $rec->fonte_rec = $fonte;
    $rec->link_rec = $link;
    $rec->type_rec = $type;
    $rec->tag_id   = $tag;
    $rec->cmid = $cmid;

    return $DB->insert_record('octopus_recomendacao', $rec);
}

function octopus_add_rec($type, $cmid) {
    global $DB;

    $rec = new stdClass();
    $rec->type_rec = $type;
    $rec->cmid     = $cmid;

    return $DB->insert_record('octopus_recomendacao_add', $rec);
}

function octopus_new_tag($name, $parent, $cmid) {
    global $DB;

    $tag = new stdClass();
    $tag->name_tag = $name;
    $tag->parent_tag = $parent;
    $tag->cmid = $cmid;

    return $DB->insert_record('octopus_tag', $tag);
}

function octopus_delete_tag($tagid) {
    global $DB;

    $to_be_deleted = $DB->get_record('octopus_tag', array('id' => $tagid));

    $rd = $DB->delete_records('octopus_tag', array('id' => $tagid));

    $ru = $DB->execute("UPDATE {octopus_tag} SET parent_tag = {$to_be_deleted->parent_tag} WHERE parent_tag = {$tagid}");
    return $ru;
}

function octopus_update_tag($tagid, $name, $parent, $cmid) {
    global $DB;

    $tag = new StdClass();
    $tag->id = $tagid;
    $tag->name_tag = $name;
    $tag->parent_tag = $parent;
    $tag->cmid = $cmid;

    return $DB->update_record('octopus_tag', $tag);
}

function octopus_add_post_tag($post_id, $tags) {
    global $DB;

    $query = "INSERT INTO {octopus_post_has_tag} (post_id, tag_id) VALUES ";
    $values = '';

    foreach($tags as $tag) {
        $values .= "($post_id, $tag),";
    }

    $query .= trim($values, ',');

    return $DB->execute($query);
}


function octopus_get_threads_by_user($user_id, $cmid, $start = null, $end = null) {
    global $DB;

    if(isset($start) && isset($end))
        $cond = "AND (timecreated > $start AND timecreated < $end) ";
    else
        $cond = '';

    $query1 = "SELECT thread_id FROM {octopus_post} WHERE user_id = $user_id AND is_head = 1 $cond ";
    $query2 = "SELECT * FROM {octopus_thread} WHERE id IN ($query1) AND cmid = $cmid ORDER BY id DESC";

    $threads = $DB->get_records_sql($query2);

    foreach ($threads as $thread) {
        $head = octopus_get_thread_head($thread->id);
        $user = octopus_get_user($head->user_id);
        $likes = octopus_get_thread_likes($thread->id, 1);
        $dislikes = octopus_get_thread_likes($thread->id, 0);
        $posts = octopus_get_num_posts($thread->id);
        $tag = octopus_get_thread_tags($thread->id);

        $thread->user = $user->firstname . ' ' . $user->lastname;
        $thread->user_id = $user->id;
        $thread->timecreated = $head->timecreated;
        $thread->type = $head->type_message;
        $thread->likes = $likes;
        $thread->dislikes = $dislikes;
        $thread->posts = $posts;
        $thread->tags = $tag;
    }

    return $threads;
}

function octopus_thread_has_new_post($thread_id, $start = null, $end = null) {
    $posts = octopus_get_posts_from_thread($thread_id);
    $start = isset($start) ? $start : 0;
    $end = isset($end) ? $end : time();

    $p = array();
    foreach($posts as $post)
        if($post->timecreated > $start && $post->timecreated < $end)
            $p[] = $post;

    if(count($p) > 0)
        return true;
    else
        return false;
}

function octopus_get_user_interests($user_id, $cmid) {
    global $DB;

    $query = "  SELECT DISTINCT(t.id), t.name_tag
                FROM mdl_octopus_tag t
                JOIN mdl_octopus_post_has_tag pht ON t.id = pht.tag_id
                JOIN mdl_octopus_post p ON pht.post_id = p.id
                WHERE p.user_id = $user_id AND p.is_head = 1 AND t.cmid = $cmid ";

    $interests = $DB->get_records_sql($query);

    return $interests;
}

function octopus_get_user_interests_rec($user_id, $cmid) {
    global $DB;

    $query = "  SELECT DISTINCT(t.id), t.name_tag
                FROM mdl_octopus_tag t
                JOIN mdl_octopus_post_has_tag pht ON t.id = pht.tag_id
                JOIN mdl_octopus_post p ON pht.post_id = p.id AND p.type_message = 1
                WHERE p.user_id = $user_id AND p.is_head = 1 AND t.cmid = $cmid ";

    $interests = $DB->get_records_sql($query);

    return $interests;
}

//cmid adicionado, verificar
function octopus_get_related_threads($thread_id, $cmid) {
    global $DB;

    $query = "  SELECT DISTINCT(t.id), t.name_tag, t.cmid
                FROM {octopus_tag} t
                JOIN {octopus_post_has_tag} pht ON t.id = pht.tag_id
                JOIN {octopus_post} p ON pht.post_id = p.id
                WHERE p.thread_id = $thread_id AND p.is_head = 1 ";

    $tags = $DB->get_records_sql($query);

    $tags_array = array();
    foreach($tags as $tag)
        $tags_array[] = $tag->id;

    $t = implode(', ', $tags_array);

    $query = "  SELECT DISTINCT(p.thread_id)
                FROM {octopus_post} p
                JOIN {octopus_post_has_tag} pht ON p.id = pht.post_id
                WHERE p.is_head = 1 AND pht.tag_id IN ($t) ";

    $thread_list = $DB->get_records_sql($query);

    $r = array();
    foreach($thread_list as $thread) {
        if($thread->thread_id != $thread_id)
            $r[] = octopus_get_thread($thread->thread_id, $cmid);
    }

    return $r;
}

function octopus_get_related_threads_limit($thread_id, $inicio, $fim, $cmid) {
    global $DB;

    $query = "  SELECT DISTINCT(t.id), t.name_tag, t.cmid
                FROM {octopus_tag} t
                JOIN {octopus_post_has_tag} pht ON t.id = pht.tag_id
                JOIN {octopus_post} p ON pht.post_id = p.id
                WHERE p.thread_id = $thread_id AND p.is_head = 1";

    $tags = $DB->get_records_sql($query);

    $tags_array = array();
    foreach($tags as $tag)
        $tags_array[] = $tag->id;

    $t = implode(', ', $tags_array);

    $query = "  SELECT DISTINCT(p.thread_id)
                FROM {octopus_post} p
                JOIN {octopus_post_has_tag} pht ON p.id = pht.post_id
                WHERE p.is_head = 1 AND p.thread_id != $thread_id AND pht.tag_id IN ($t) ORDER BY p.timecreated DESC LIMIT ".$inicio." , ".$fim." ";

    $thread_list = $DB->get_records_sql($query);

    $r = array();
    foreach($thread_list as $thread) {
        if($thread->thread_id != $thread_id)
            $r[] = octopus_get_thread($thread->thread_id, $cmid);
    }

    return $r;
}

function buildPagination($total, $link, $atual, $texto){
    
    $temp = $link;
   
   
     if (strpos($link, 'pag='.$atual) !== false) {
         
            if($atual > 1){
                $link = str_replace("pag=".$atual, "pag=".($atual - 1), $link);
                
                                
            }else{
                $link = "";
            } 
            
            if($texto !== null && (strpos($link, "&search=".$texto) == false)){
                    
                    $link = $link."&search=".$texto;
            }
                   
     }else{
            $link = "";
     }
    
    echo '<ul class="paginacao_msg" id="paginacao">
        
            <li class="paginacao_msg_seta">
                <a href="'.$link.'">
                <img src="pix/setas_octopus_paginacao1.svg">
                <div class="label_voltar">Voltar</div>
                </a>
            </li>';
    
            $link = $temp;
            
            for ($i = 1; $i <= $total ; $i++){
                
                if($atual == $i){
                    $class = 'class="pag_ativa"';
                }else{
                    $class = '';
                }
                 
                 //corrigindo link
                if (strpos($link, 'pag='.$atual) !== false) {
                    $link = str_replace("pag=".$atual, "pag=".$i, $link);

                }else{
                    $link = $link."&pag=".$i;
                }
                
                if($texto !== null && (strpos($link, "&search=".$texto) == false)){
                        $link = $link."&search=".$texto;
                }
                    
                
                echo '<a href="'.$link.'">
                 <li id="page'.$i.'" style="text-align: center;" '.$class.'>'.$i.'</li>
                </a>';
                
                $link = $temp;
            }
            
            
    
     if (strpos($link, 'pag='.$atual) !== false) {
         
            if($atual < $total){
                $link = str_replace("pag=".$atual, "pag=".($atual + 1), $link);
                
                               
            }else{
                $link = "";
                
            } 
                   
     }else{
            $link = "";
     }       
     
     if($texto !== null && (strpos($link, "&search=".$texto) == false)){
                        $link = $link."&search=".$texto;
     }
           
    echo '  <li class="paginacao_msg_seta">
                <a href="'.$link.'">
                <img src="pix/setas_octopus_paginacao.svg">
                <div class="label_avancar">Avançar</div>
                </a>
            </li>
         </ul>';
    
}


function buildPaginationRanking($total, $link, $atual, $texto){
    
    $temp = $link;
   
   
     if (strpos($link, 'pag='.$atual) !== false) {
         
            if($atual > 1){
                $link = str_replace("pag=".$atual, "pag=".($atual - 1), $link);
                
                                
            }else{
                $link = "";
            } 
            
            if($texto !== null && (strpos($link, "&search=".$texto) == false)){
                    
                    $link = $link."&search=".$texto;
            }
                   
     }else{
            $link = "";
     }
    
    echo '<ul class="paginacao_msg" id="paginacao">
        
            <li class="paginacao_msg_seta">
                <a href="'.$link.'">
                <img src="pix/setas_octopus_paginacao1.svg">
                <div class="label_voltar">Voltar</div>
                </a>
            </li>';
    
            $link = $temp;
            
            for ($i = 1; $i <= $total ; $i++){
                
                if($atual == $i){
                    $class = 'class="pag_ativa"';
                }else{
                    $class = '';
                }
                 
                 //corrigindo link
                if (strpos($link, 'pag='.$atual) !== false) {
                    $link = str_replace("pag=".$atual, "pag=".$i, $link);

                }else{
                    $link = $link."&pag=".$i;
                }
                
                if($texto !== null && (strpos($link, "&search=".$texto) == false)){
                        $link = $link."&search=".$texto;
                }
                    
                
                echo '<a href="'.$link.'">
                 <li id="page'.$i.'" style="text-align: center;" '.$class.'>'.$i.'</li>
                </a>';
                
                $link = $temp;
            }
            
            
    
     if (strpos($link, 'pag='.$atual) !== false) {
         
            if($atual < $total){
                $link = str_replace("pag=".$atual, "pag=".($atual + 1), $link);
                
                               
            }else{
                $link = "";
                
            } 
                   
     }else{
            $link = "";
     }       
     
     if($texto !== null && (strpos($link, "&search=".$texto) == false)){
                        $link = $link."&search=".$texto;
     }
           
    echo '  <li class="paginacao_msg_seta">
                <a href="'.$link.'">
                <img src="pix/setas_octopus_paginacao.svg">
                <div class="label_avancar">Avançar</div>
                </a>
            </li>
         </ul>';
    
}


function octopus_get_last_activity($user_id,$cmid) {
    global $DB;

    $today = strtotime(date('d-m-Y', time()));
    $query = "  SELECT *
                FROM mdl_octopus_log
                WHERE time > $today AND cmid = $cmid
                ORDER BY time DESC
                LIMIT 1";


    $log = $DB->get_records_sql($query);
    return array_shift($log);
}


function octopus_get_recent_posts($cmid,$user_id){
   global $DB;

//retorno do caminho que percorri
    $query = "SELECT DISTINCT l.page
               FROM mdl_octopus_log l
               WHERE l.page like '%thread_id%'and l.user_id=$user_id and l.cmid = $cmid ORDER BY l.time DESC";
    $posts = $DB->get_records_sql($query);
    //criando arrays para comparar
    $postLog=array();
    $postAll=array();
    foreach ($posts as $post){
        $id = explode("thread_id=", $post->page);
        $id = $id[1];
        array_push($postLog, $id);
    }

    $allPosts = "SELECT DISTINCT t.id FROM mdl_octopus_post p, mdl_octopus_thread t
    WHERE p.thread_id = t.id and p.is_head = 1 and p.user_id != $user_id and t.cmid = $cmid ORDER BY p.timecreated DESC";
    $postAlls = $DB->get_records_sql($allPosts);
    foreach ($postAlls as $post){
    array_push($postAll, $post->id);
    }
    //comparando
    $result = array_diff($postAll, $postLog);
    //retornando
    foreach($result as $thread) {
       $threads[] = octopus_get_thread($thread, $cmid);
   }
   return $threads;
}


function octopus_get_recent_posts_num($cmid,$user_id) {
   global $DB;

 $query = "SELECT DISTINCT l.page
               FROM mdl_octopus_log l
               WHERE l.page like '%&thread_id=%'and l.user_id=$user_id and l.cmid = $cmid ORDER BY l.time";
    $posts = $DB->get_records_sql($query);
    //criando arrays para comparar
    $postLog=array();
    $postAll=array();
    foreach ($posts as $post){
        $id = explode("thread_id=", $post->page);
        $id = $id[1];
        array_push($postLog, $id);
    }

    $allPosts = "SELECT DISTINCT t.id FROM mdl_octopus_post p, mdl_octopus_thread t
    WHERE p.thread_id = t.id and p.is_head = 1 and t.cmid = $cmid and p.user_id != $user_id  ORDER BY p.timecreated";
    $postAlls = $DB->get_records_sql($allPosts);
    foreach ($postAlls as $post){
    array_push($postAll, $post->id);
    }
    //comparando
    $result = array_diff($postAll, $postLog);
    //retornando
   //  foreach($result as $thread) {

   //     $threads[] = octopus_get_thread($thread);
   // }
    return count($result);
// //    return count($threads_ids);

}

// function octopus_get_recent_posts($start_time,$cmid,$user_id) {
//    global $DB;
//    $today = strtotime(date('d-m-Y', time()));

//     $query2 = "SELECT DISTINCT l.time
//                FROM mdl_octopus_log l
//                WHERE l.page like '%recent%'and l.user_id=$user_id ORDER BY l.time DESC  limit 1";
//     $time = $DB->get_records_sql($query2);

//     foreach ($time as $times){
//         $timelog = $times->time;
//     }

//     if(count($timelog)==0){
//         $timelog=0;
//     }

//      $query = "SELECT DISTINCT p.thread_id
//                FROM mdl_octopus_post p
//                INNER JOIN mdl_octopus_thread t ON t.id = p.thread_id
//                INNER JOIN mdl_octopus_log l ON l.cmid = t.cmid
//                WHERE p.timecreated >= $timelog and t.cmid = $cmid  ORDER BY p.timecreated DESC ";


//    $threads_ids = $DB->get_records_sql($query);
//    $threads = array();

//    foreach($threads_ids as $thread) {
//        $threads[] = octopus_get_thread($thread->thread_id);
//    }

//    return $threads;
// }

// function octopus_get_recent_posts_num($start_time,$cmid,$user_id) {
//    global $DB;

//    $start_time;
//    $today = strtotime(date('d-m-Y', time()));

//     $query2 = "SELECT DISTINCT l.time
//                FROM mdl_octopus_log l
//                WHERE l.page like '%recent%'and l.user_id=$user_id ORDER BY l.time DESC  limit 1";
//     $time = $DB->get_records_sql($query2);

//     foreach ($time as $times){
//         $timelog = $times->time;
//     }

//     if(count($timelog)==0){
//         $timelog=0;
//     }

//     $query = "SELECT DISTINCT p.thread_id
//                FROM mdl_octopus_post p
//                INNER JOIN mdl_octopus_thread t ON t.id = p.thread_id
//                INNER JOIN mdl_octopus_log l ON l.cmid = t.cmid
//                WHERE p.timecreated >= $timelog and t.cmid = $cmid  ORDER BY p.timecreated DESC ";

//    $threads_ids = $DB->get_records_sql($query);
//     return count($threads_ids);

// }


function octopus_get_tag_tree($cmid) {
    global $DB;

    $nodes = $DB->get_records('octopus_tag', array('cmid' => $cmid, 'parent_tag' => 0));
    $stack = $nodes;

    $a = array_shift($stack);
    array_unshift($stack, $a);

    $i = 0;
    while($i < sizeof($stack)) {
        $element = $stack[$i];
        $children = $DB->get_records('octopus_tag', array('cmid' => $cmid,
                                                          'parent_tag' => $element->id));

        if($children) {
            $element->children = $children;
            $nodes[$element->id] = $element;
            $stack = array_merge($stack, $children);
            //array_shift($stack);
        }
        else {
            $element->children = array();
            $nodes[$element->id] = $element;
            //array_shift($stack);
        }

        $i++;
    }

    return $nodes;
}

function octopus_get_tag_tree_recursive($cmid, $parent_tag = 0) {
    global $DB;

    $nodes = $DB->get_records('octopus_tag', array('cmid' => $cmid, 'parent_tag' => $parent_tag), $sort='name_tag');

    if($nodes) {
        foreach($nodes as $node) {
            $nodes[$node->id]->children = octopus_get_tag_tree_recursive($cmid, $node->id);
        }
        $a = array_shift($nodes);
        array_unshift($nodes, $a);
        return $nodes;
    }
    else
        return array();
}

function octopus_get_name_from_chat($id){

    global $DB;

    $query = "SELECT  CONCAT(u.firstname,' ', u.lastname) as nome
                FROM  mdl_user u WHERE u.id =".$id;

    $nome = $DB->get_records_sql($query);

    return $nome;

}

function octopus_set_digest_frequency($user_id, $frequency, $cmid) {
    global $DB;

    if($user = $DB->get_record('octopus_user_preferences', array('user_id' => $user_id, 'cmid' => $cmid))) {
        $user->digest_frequency = $frequency;
        $DB->update_record('octopus_user_preferences', $user);
    }
    else {
        $user = new stdClass();
        $user->user_id = $user_id;
        $user->private_profile = 0;
        $user->digest_frequency = 1;
        $user->cmid = $cmid;

        $DB->insert_record('octopus_user_preferences', $user);
    }
}

function octopus_get_digest_frequency($user_id, $cmid) {
    global $DB;

    if($user = $DB->get_record('octopus_user_preferences', array('user_id' => $user_id, 'cmid' => $cmid)))
        return $user->digest_frequency;
    else
        return false;
}

function octopus_user_digest($user_id, $cmid, $start_time, $end_time) {
    global $DB;
    
    //testando se existe ssl
    if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) {
    $http = "https://";
    }else{
    $http= "http://";
    }
    $url = $http.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    $url = substr($url, 0, strrpos($url, '/'));

    
    // MONTA ASSUNTO -------------------------------------------------------
    $query = "  SELECT cm.id, c.fullname, c.shortname, cm.instance, o.name
                FROM {course} c
                JOIN {course_modules} cm ON c.id = cm.course
                JOIN {octopus} o ON o.id = cm.instance
                WHERE cm.id = $cmid ";

    $course_module = $DB->get_record_sql($query);

    $subject = "[SABER] Atualizações no fórum do curso [$course_module->fullname/$course_module->name]";
    // TERMINA ASSUNTO -----------------------------------------------------

    $user = core_user::get_user($user_id);
     $message_header = "Olá, <b>$user->firstname $user->lastname</b>! <br><br><br>";

    // MONTA "SUAS POSTAGENS" ---------------------------------------------

    $message_body = "Sua postagem sobre <strong>{thread_title}</strong> obteve comentários. Para visualizá-los, clique {link}.";
    $link = "<a target='_blank' href='$url/thread.php?id={cmid}&thread_id={thread_id}'>aqui</a>";

    $user = $DB->get_record('user', array('id' => $user_id));

    $query = "  SELECT t.id, t.title, p.id as head_id
                FROM {octopus_thread} t
                JOIN {octopus_post} p ON p.thread_id = t.id
                WHERE p.is_head = 0 AND t.id IN (
                    SELECT DISTINCT(p.thread_id)
                    FROM {octopus_post} p
                    JOIN {octopus_thread} t ON p.thread_id = t.id
                    WHERE p.is_head = 1 AND t.cmid = $cmid AND p.user_id = $user_id AND timecreated >= $start_time AND timecreated < $end_time
                )";

    $threads = $DB->get_records_sql($query);    
    $final_message = array();
    foreach($threads as $key => $thread) {
        $query = "  SELECT t.name_tag
                    FROM {octopus_tag} t
                    JOIN {octopus_post_has_tag} pt ON pt.tag_id = t.id
                    WHERE pt.post_id = $thread->head_id ";

        $tags = $DB->get_records_sql($query);        
        $threads[$key]->tags = array();
        foreach($tags as $tag)
            $threads[$key]->tags[] = $tag->name_tag;        

        $final_message[] = str_replace( array('{thread_title}', '{tag_name}', '{link}'),
                                        array(  $thread->title,
                                                implode($threads[$key]->tags, ', '),
                                                str_replace(array('{cmid}', '{thread_id}'),
                                                          array($cmid, $thread->id),
                                                          $link)),
                                        $message_body);
    }
   



     $final_message = implode('<br><br>', $final_message);


    // TERMINA "SUAS POSTAGENS" -------------------------------------------

    // MONTA "AS MAIS CURTIDAS E DESCURTIDAS" -----------------------------

    $likes_body = " <b>{likes_count}</b> {pessoas curtiram} sua postagem sobre <b>{thread_title}</b>.
                    Para visualizá-la, clique {link}.";



    $likes_body2 = " <b>{dislikes_count}</b> {pessoas não curtiram} sua postagem sobre <b>{thread_title}</b>.
                    Para visualizá-la, clique {link}.";


    $link = "<a target='_blank' href='$url/thread.php?id={cmid}&thread_id={thread_id}'>aqui</a>";

    $query = "  SELECT p.thread_id as thread_id, t.title as thread_title, p.id as post_id, p.message,
                COUNT(case when l.type = 1 then 1 else null end) as likes,
                COUNT(case when l.type = 0 then 1 else null end) as dislikes
                FROM {octopus_post} p
                JOIN {octopus_thread} t ON t.id = p.thread_id
                LEFT JOIN {octopus_like} l ON l.post_id = p.id
                WHERE t.cmid = $cmid AND p.is_head = 1 AND p.user_id = $user_id AND l.timecreated > $start_time AND l.timecreated < $end_time
                GROUP BY (p.id)
                ORDER BY likes DESC, dislikes ASC";

    $posts_likes = $DB->get_records_sql($query);

    $final_likes = array();
    $final_likes2 = array();

    foreach($posts_likes as $key => $post) {
        $query = "  SELECT t.id, t.name_tag
                    FROM {octopus_tag} t
                    JOIN {octopus_post_has_tag} pt ON pt.tag_id = t.id
                    WHERE pt.post_id = $post->post_id ";

        $tags = $DB->get_records_sql($query);
        $posts_likes[$key]->tags = array();

                foreach($tags as $tag)
                    $posts_likes[$key]->tags[] = $tag->name_tag;

                       if($post->likes == 1){

                        $pessoas = "pessoa curtiu";

                    }else{
                        $pessoas = "pessoas curtiram";

                    }



                    if($post->dislikes == 1){

                        $pessoas2 = "pessoa não curtiu";

                    }else{
                        $pessoas2 = "pessoas não curtiram";

                    }

                    $final_likes[] = str_replace(   array('{likes_count}', '{tag_name}', '{link}', '{thread_title}', '{pessoas curtiram}'),
                                                    array($post->likes,
                                                    implode($posts_likes[$key]->tags, ', '),
                                                    str_replace(array('{cmid}', '{thread_id}'),array($cmid, $post->thread_id),$link),
                                                    $post->thread_title,
                                                    $pessoas
                                                    ),
                                                    $likes_body);


                    $final_likes2[] = str_replace(   array('{dislikes_count}', '{tag_name}', '{link}', '{thread_title}', '{pessoas não curtiram}'),
                                                    array($post->dislikes,
                                                          implode($posts_likes[$key]->tags, ', '),
                                                          str_replace(array('{cmid}', '{thread_id}'),
                                                                      array($cmid, $post->thread_id),
                                                                      $link), $post->thread_title,  $pessoas2),
                                                    $likes_body2);
            }


                $contador = count($final_likes2);
                for ($i=0; $i < $contador ; $i++) {

                    if(strpos($final_likes[$i]," <b>0</b> pessoas") !== false){

                         unset($final_likes[$i]);

                    }
                }

                array_values($final_likes);
                $final_likes = implode('<br><br>', $final_likes);


                $contador = count($final_likes2);

                for ($k=0; $k < $contador ; $k++) {

                    if(strpos($final_likes2[$k]," <b>0</b> pessoas") !== false){

                        unset($final_likes2[$k]);


                    }
                }


                array_values($final_likes2);
                $final_likes2 = implode('<br><br>', $final_likes2);





    // TERMINA "AS MAIS CURTIDAS E DESCURTIDAS" ---------------------------

    //Agrupamento dos arrays
    $used_posts = array();
                
    
    // MONTA "POSTAGENS DE CONTATOS"

    $my_contacts_header = " Verificamos o sistema e encontramos postagem(ns) dos seus contatos que sugerimos que você visualize:";
   
//    $link = "<a target='_blank' href='$url/thread.php?id={cmid}&thread_id={thread_id}'>aqui</a>";
    
    
    //Seleciona todos os usuários que são contatos
    $c_query = "SELECT user_id2
                 FROM {octopus_contact}
                 WHERE user_id1 = $user_id and cmid = $cmid";
    
    $contacts =  $DB->get_records_sql($c_query);
    
    $array_posts = array();
    
          
    //Para cada contato, traz seus posts recentes
    foreach ($contacts as $c){
           
        
            $query = "  SELECT t.*, CONCAT(u.firstname, ' ', u.lastname) as nome_autor, u.id as id_autor
                FROM {octopus_thread} t
                INNER JOIN {octopus_post} p ON p.thread_id = t.id
                INNER JOIN {user} u ON u.id = p.user_id
                WHERE  cmid = $cmid  AND p.is_head = 1 AND p.user_id = $c->user_id2  AND p.timecreated > $start_time AND p.timecreated <= $end_time 
                ORDER BY p.timecreated DESC
                LIMIT 0, 6 ";
            //AND p.timecreated > $start_time AND p.timecreated <= $end_time 
            
            $cont = $DB->get_records_sql($query);
         
            foreach ($cont as $c){
                 
                  array_push($array_posts, $c);
                  array_push($used_posts, $c->id);
                 
            }
            
           
    }
    
   $final_contact = '';
   
     
      
   foreach ($array_posts as $post){
     
        $root = split("mod", 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
        
        $img = "<img style='border-radius: 5px; margin-right: 10px;' src='$root[0]/user/pix.php/$post->id_autor/f1.jpg'>";
        
        $link = "<a target='_blank' href='$url/thread.php?id=$cmid&thread_id=$post->id'>aqui</a>";
        $final_contact = $final_contact
                .'<li style="list-style-type: none;">'
                .'<table><tbody>'
                .'<tr><td rowspan="3">'.$img.'</td>'
                .'<td>Postagem sobre: <strong>'.$post->title.'</strong></td></tr>'
                .'<tr><td> Por: '.$post->nome_autor.'</td></tr>'
                .'<tr><td>  Para visualizá-la, clique '.$link.'.</td></tr>'
                .'</tobdy></table>'
                .'</li><br>';
        
       
   }
   
   
    // TERMINA "POSTAGENS DE CONTATOS"
   
   // MONTA "DISPARADORA"

    $disparadora_header = " Com base nos conteúdos disponíveis no AVA, nossa equipe apresentou a(s) seguinte(s) postagem(ns) para você:";
    $disparadora_body = "<li>Postagem sobre: <strong>{thread_title}</strong>. Para visualizá-la, clique {link}.</li>";

    $link = "<a target='_blank' href='$url/thread.php?id={cmid}&thread_id={thread_id}'>aqui</a>";


    //Primeiro todas as postagens dentro do intervalo de tempo são puxadas. Depois são relacionadas com as tags que o usuário já usou em algum momento no fórum
    $query = "  SELECT DISTINCT(th.id) as thread_id, th.title, p.id as post_id
                FROM mdl_octopus_thread th
                JOIN mdl_octopus_post p ON p.thread_id = th.id
                JOIN mdl_octopus_post_has_tag pt ON pt.post_id = p.id
                WHERE th.cmid = $cmid AND p.timecreated > $start_time AND p.timecreated <= $end_time AND p.user_id != $user_id  AND p.type_message = 4 ";
    $rec = $DB->get_records_sql($query);

    $final_disparadora = array();
    foreach($rec as $key => $thread) {
        $query = "  SELECT t.id, t.name_tag
                    FROM {octopus_tag} t
                    JOIN {octopus_post_has_tag} pt ON pt.tag_id = t.id
                    WHERE pt.post_id = $thread->post_id ";

        $tags = $DB->get_records_sql($query);
        $rec[$key]->tags = array();
        foreach($tags as $tag)
            $rec[$key]->tags[] = $tag->name_tag;
        
        if(in_array($thread->thread_id, $used_posts) == 0){
            
        
            $final_disparadora[] = str_replace( array('{tag_name}', '{thread_title}', '{link}'),
                                                array(implode($rec[$key]->tags, ', '),
                                                      $thread->title,
                                                      str_replace(array('{cmid}', '{thread_id}'),
                                                                  array($cmid, $thread->thread_id),
                                                                  $link)),
                                                $disparadora_body);
            array_push($used_posts, $thread->thread_id);
             
                 
        }
    }



    $final_disparadora = implode('<br>', $final_disparadora);

    // TERMINA "DISPARADORA"
                

    // MONTA "RECOMENDACOES"

    $recommendations_header = " Analisamos o sistema e encontramos postagem(ns) que achamos que corresponde(m) aos seus interesses. Veja nossa(s) recomendação(ões):";
    $recommendations_body = "<li>Postagem sobre: <strong>{thread_title}</strong>. Para visualizá-la, clique {link}.</li>";

    $link = "<a target='_blank' href='$url/thread.php?id={cmid}&thread_id={thread_id}'>aqui</a>";


    //Primeiro todas as postagens dentro do intervalo de tempo são puxadas. Depois são relacionadas com as tags que o usuário já usou em algum momento no fórum
    $query = "  SELECT DISTINCT(th.id) as thread_id, th.title, p.id as post_id
                FROM mdl_octopus_thread th
                JOIN mdl_octopus_post p ON p.thread_id = th.id
                JOIN mdl_octopus_post_has_tag pt ON pt.post_id = p.id
                WHERE th.cmid = $cmid AND p.timecreated > $start_time AND p.timecreated <= $end_time AND p.user_id != $user_id  AND pt.tag_id IN (
                    SELECT DISTINCT(t.id)
                    FROM mdl_octopus_tag t
                    JOIN mdl_octopus_post_has_tag pt ON pt.tag_id = t.id
                    JOIN mdl_octopus_post p ON p.id = pt.post_id
                    WHERE p.user_id = $user_id AND p.is_head = 1 AND t.cmid = $cmid
                ) ";
    $rec = $DB->get_records_sql($query);

    $final_recommendations = array();
    foreach($rec as $key => $thread) {
        $query = "  SELECT t.id, t.name_tag
                    FROM {octopus_tag} t
                    JOIN {octopus_post_has_tag} pt ON pt.tag_id = t.id
                    WHERE pt.post_id = $thread->post_id ";

        $tags = $DB->get_records_sql($query);
        $rec[$key]->tags = array();
        foreach($tags as $tag)
            $rec[$key]->tags[] = $tag->name_tag;
        
      
        
        
        if(in_array($thread->thread_id, $used_posts) == 0){
            
            $final_recommendations[] = str_replace( array('{tag_name}', '{thread_title}', '{link}'),
                                                array(implode($rec[$key]->tags, ', '),
                                                      $thread->title,
                                                      str_replace(array('{cmid}', '{thread_id}'),
                                                                  array($cmid, $thread->thread_id),
                                                                  $link)),
                                                $recommendations_body);
            array_push($used_posts, $thread->thread_id);
             
        }
        
    }



    $final_recommendations = implode('<br>', $final_recommendations);
   
    
    // TERMINA "RECOMENDACOES"
    
    // MONTA "MATERIAIS"

    $materiais_header = " Baseados na(s) sua(s) postagem(ns), recomendamos que visualize o(s) seguinte(s) material(is):";
    
  

        $final_materiais = "";
    
    
        /******* teste materiais ********/
    
        $result = octopus_get_user_interests_rec($user_id, $cmid);
 
 
        foreach ($result as $value) {


            $materiais = octopus_get_rec_material_by_tag($value->id, $cmid);

            foreach ($materiais as $mat){
               $mat->my_rate = get_rec_rate($mat->id, $user_id, $cmid);

            }

            
              
            $arrayTag = new stdClass();
            $arrayTag->nome = $value->name_tag;
            $arrayTag->materiais = $materiais;

            array_push($tags, $arrayTag);
       }   

           
            
       foreach ($tags as $t){
           
          
           
           foreach($t->materiais as $mat){
               
               $link = "<li><a target='_blank' href='$mat->link_rec'>$mat->name_rec</a></li>";
               $final_materiais = $final_materiais.$link;
              
           }
        
           
       }
   
    
    // TERMINA MATERIAIS
    
    
    
     


   
    $validaEnvio = false;

    if($final_message == ''){
        $full_message = $message_header ."Você não possui novos comentários nas suas postagens.<br><br>";
    }else{
        $full_message = $full_message.$message_header.$final_message."<br><br>";
         $validaEnvio = true;
    }
    
    $full_message = $full_message.'';

    if($final_likes == ''){
        $full_message = $full_message."Você não possui novas curtidas nas suas postagens.<br><br>";
    }else{
        $full_message = $full_message.$final_likes."<br><br>";
         $validaEnvio = true;
    }
    
    $full_message = $full_message.'';

    if($final_likes2 == ''){
        $full_message = $full_message."Você não possui novas postagens não curtidas.<br><br>";
    }else{

        $full_message = $full_message.$final_likes2."<br>";
         $validaEnvio = true;
    }
    
    $full_message = $full_message.'<br><hr><br>';
    
     if($final_contact == ''){
        $full_message = $full_message."Nenhum dos seus contatos postou recentemente.<br>";
    }else{
        $full_message = $full_message. $my_contacts_header ."<ul>".$final_contact."</ul>";
        $validaEnvio = true;
    }
    
    $full_message = $full_message.'<br><hr><br>';

    if($final_recommendations == ''){
        $full_message = $full_message."Você não possui nenhuma recomendação de postagens no momento.<br><br>";
    }else{
        $full_message = $full_message.$recommendations_header ."<ul>".$final_recommendations."</ul>";
         $validaEnvio = true;
    }
    
    $full_message = $full_message.'<br><hr><br>';
    
     if($final_materiais == ''){
        $full_message = $full_message."Você não possui nenhuma recomendação de material no momento.<br><br>";
    }else{
        $full_message = $full_message.$materiais_header ."<ul>".$final_materiais."</ul>";
         $validaEnvio = true;
    }
    
    $full_message = $full_message.'<br>';
     
    if($final_disparadora == ''){
        $full_message = $full_message."Você não possui nenhuma questão disparadora para responder no momento.<br>";
    }else{
        $full_message = $full_message."<div style='background-color: #E1D4EF !important; color: rgb(84, 48, 126); padding: 10px; border: 1px solid rgb(84, 48, 126); width: 85% !important;'>". $disparadora_header ."<ul>".$final_disparadora."</ul></div>";
         $validaEnvio = true;
    }
    
    $full_message = $full_message.'<br><br><br>Atenciosamente,<br>
    SABER Tecnologias Educacionais e Sociais';
    
   

    $user2 = $user;
    $user2->firstname = "SABER Tecnologias";
    $user2->lastname = "";

    if($validaEnvio == true){
      // COMENTADO PARA VERIFICAÇÃO
       $response = email_to_user($user, $user2, $subject, '', $full_message);


    }

    echo $full_message;
    echo "<br><br><br><br><br><br><br><br>";
	
    if($response) {
	
        //atualizacao do digest na tabela do usuario
        $upref = $DB->get_record('octopus_user_preferences', array('user_id' => $user_id,'cmid'=>$cmid));
        $upref->last_digest = $end_time;
        $DB->update_record('octopus_user_preferences', $upref);

        return true;
    }
    else
    	
        return false;
}

//
//function get_wwwroot(){
//    global $DB;
//
//    $ups = $DB->get_records('mnet_host', array('cmid' => $cmid));
//
//    return $ups;
//}

function octopus_get_user_preferences($cmid) {
    global $DB;

    $ups = $DB->get_records('octopus_user_preferences', array('cmid' => $cmid));

    return $ups;
}

function octopus_get_connections_threads($cmid, $user_id, $max, $start) {
    global $DB;

    $query1 = " SELECT user_id2
                FROM {octopus_contact}
                WHERE user_id1 = $user_id AND cmid = $cmid ";

    $query2 = " SELECT t.*
                FROM {octopus_thread} t
                JOIN {octopus_post} p ON p.thread_id = t.id
                WHERE p.user_id IN ($query1) AND p.is_head = 1 AND t.cmid = $cmid
                LIMIT $start, $max ";

    $threads = $DB->get_records_sql($query2);

    foreach($threads as $thread) {
        $head = octopus_get_thread_head($thread->id);
        $user = octopus_get_user($head->user_id);
        $likes = octopus_get_thread_likes($thread->id, 1);
        $dislikes = octopus_get_thread_likes($thread->id, 0);
        $posts = octopus_get_num_posts($thread->id);
        $tag = octopus_get_thread_tags($thread->id);

//        $thread->user = $user->firstname . ' ' . $user->lastname;
        $thread->user = "SABER Tecnologias";
        $thread->user_id = $user->id;
        $thread->timecreated = $head->timecreated;
        $thread->type = $head->type_message;
        $thread->likes = $likes;
        $thread->dislikes = $dislikes;
        $thread->posts = $posts;
        $thread->tags = $tag;
    }

    return $threads;
}

function octopus_update_grade($post_id, $grade) {
    global $DB;

    $post = $DB->get_record('octopus_post', array('id' => $post_id));
    if($post) {
        $post->grade = $grade;
        return $DB->update_record('octopus_post', $post);
    }
    else
        return -1;
}

function octopus_get_grade_type($cmid) {
    global $DB;

    $query = "  SELECT o.*
                FROM mdl_octopus o
                JOIN mdl_course_modules cm ON cm.instance = o.id
                WHERE cm.id = $cmid ";

    $octopus = $DB->get_record_sql($query);

    if($octopus->grade_type == 0)
        return "None";
    else if($octopus->grade_type == 1)
        return "Manual";
    else
        return "Reward";
}

function octopus_get_max_grade($cmid) {
    global $DB;

    $query = "  SELECT o.*
                FROM mdl_octopus o
                JOIN mdl_course_modules cm ON cm.instance = o.id
                WHERE cm.id = $cmid ";

    $octopus = $DB->get_record_sql($query);

    return $octopus->grade;
}

function octopus_get_instance($cmid) {
    global $DB;

    $query = "  SELECT o.*
                FROM mdl_octopus o
                JOIN mdl_course_modules cm ON cm.instance = o.id
                WHERE cm.id = $cmid ";

    $octopus = $DB->get_record_sql($query);

    $octopus->assessed = $octopus->grade != 0 ? true : false;
    $octopus->cmidnumber = $cmid;
    $octopus->scale = $octopus->grade;

    return $octopus;
}



/* LISTA AS TAGS DO OCTOPUS */
 function octopus_lista_tags_brasil($cmid, $tag, $pais, $regiao, $estado, $cidade, $data, $data_fim){
     global $DB;

    $condicao = '';
    if($tag != 0){
       $condicao = " AND tag.id = ".$tag;
    }

    if($pais != 0){
        $condicao = $condicao. " AND ue.pais = ".$pais." ";
    } else{ }

    if($regiao != 0){
        $condicao = $condicao. " AND ue.regiao = ".$regiao." ";
    } else{ }

    if($estado != 0){
        $condicao = $condicao. " AND ue.estado = ".$estado." ";
    }else{ }

    if($cidade != 0){
        $condicao = $condicao. " AND ue.municipio = ".$cidade." ";
    } else{ }

    if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }


   $query = " SELECT distinct tag.id, name_tag, pa.paisnome
           FROM mdl_octopus_post p
           LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
           LEFT JOIN mdl_octopus_post_has_tag tg ON tg.post_id = p.id
           LEFT JOIN mdl_octopus_tag tag ON tag.id = tg.tag_id
           LEFT JOIN quest_estados e ON e.id = ue.estado
           LEFT JOIN quest_cidades c ON c.id = ue.municipio
           LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
           WHERE cmid = ".$cmid." ".$condicao."  ";

   $sql = $DB->get_records_sql($query);
   return $sql;

}



/* RETORNA AS THREADS DE CADA TAG */
 function octopus_threads_tag_brasil($cmid,$tag_id,$pais,$regiao,$estado,$cidade,$data,$data_fim){
   global $DB;

   $condicao = '';
    if($tag_id != 0){
       $condicao = " AND tag.id = ".$tag_id;
    }

    if($pais != 0){
        $condicao = $condicao. " AND ue.pais = ".$pais." ";
    } else{ }

    if($regiao != 0){
        $condicao = $condicao. " AND ue.regiao = ".$regiao." ";
    } else{ }

    if($estado != 0){
        $condicao = $condicao. " AND ue.estado = ".$estado." ";
    }else{ }

    if($cidade != 0){
        $condicao = $condicao. " AND ue.municipio = ".$cidade." ";
    } else{ }

    if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }


   $query = " SELECT  t.id as thread_id, pa.paisnome
              FROM mdl_octopus_thread t
              JOIN mdl_octopus_post p ON p.thread_id = t.id
              LEFT JOIN mdl_user u ON u.id = p.user_id
              LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
              LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
              LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
              LEFT JOIN quest_cidades c ON c.id = ue.municipio
              LEFT JOIN quest_estados e ON e.id = ue.estado
              LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
              WHERE t.cmid = ".$cmid." ".$condicao." ";

   $sql = $DB->get_records_sql($query);
   return $sql;

 }


/* RETORNA A QUANTIDADE DE VEZES QUE UMA TAG FOI UTILIZADA EM POSTS */
function octopus_quantidade_tag_utilizada($cmid, $tag_id, $regiao, $estado, $cidade, $data, $data_fim){
     global $DB;

    $condicao = '';
    if($tag_id != 0){
       $condicao = " AND pht.tag_id = ".$tag_id;
    }

    if($pais != 0){
        $condicao = $condicao. " AND ue.pais = ".$pais." ";
    } else{ }

    if($regiao != 0){
        $condicao = $condicao. " AND ue.regiao = ".$regiao." ";
    } else{ }

    if($estado != 0){
        $condicao = $condicao. " AND ue.estado = ".$estado." ";
    }else{ }

    if($cidade != 0){
        $condicao = $condicao. " AND ue.municipio = ".$cidade." ";
    } else{ }

    if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }


   $query = " SELECT COUNT(pht.tag_id) as quantidade_utilizadas, pa.paisnome
            FROM mdl_octopus_post p
            LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
            LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id

            LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
            LEFT JOIN quest_cidades c ON c.id = ue.municipio
            LEFT JOIN quest_estados e ON e.id = ue.estado
            LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
            WHERE tag.cmid = ".$cmid." ".$condicao." ";

    $sql = $DB->get_records_sql($query);
    return $sql;
 }


/* RETORNA AS THREAD_ID DA TAG SELECIONADA */
function octopus_thread_comentarios_brasil($cmid,$thread_id){
   global $DB;

   $query = " SELECT  p.id, CONCAT(u.firstname, ' ', u.lastname) as nome_autor, pa.paisnome, c.nome as cidade,
              e.nome as estado, cb.cbo_nome, tag.name_tag, type_message, p.thread_id, count(posts) as quantidade_comentarios, p.user_id
              FROM mdl_octopus_thread t
              LEFT JOIN mdl_octopus_post p ON p.thread_id = t.id
              JOIN mdl_user u ON u.id = p.user_id
              LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
              LEFT JOIN quest_estados e ON e.id = ue.estado
              LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
              LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
              LEFT JOIN quest_cidades c ON c.id = ue.municipio
              LEFT JOIN quest_user_cbo cb ON cb.cbo_num = ue.cbo_num
              LEFT JOIN	quest_pais pa ON pa.paisid = ue.pais

              LEFT JOIN (   SELECT id as posts, thread_id, is_head as filho FROM mdl_octopus_post WHERE is_head = 0 ) AS pt ON pt.posts = p.id

              WHERE t.cmid = ".$cmid." AND t.id = ".$thread_id." ";

   $sql = $DB->get_records_sql($query);
   return $sql;

}

function octopus_comentarios_tags($cmid,$thread_id){
   global $DB;
   $query = " SELECT MAX(qnt_comentario), thread_id FROM
           (
              SELECT count(p.id) as qnt_comentario, p.thread_id FROM mdl_octopus_thread t
              INNER JOIN mdl_octopus_post p
              WHERE p.thread_id = ".$thread_id." AND t.cmid = ".$cmid." AND is_head = 0
           ) as comentario_qnt ";
       $sql = $DB->get_records_sql($query);
   return $sql;

}


/* RETORNA O USUÁRIO COM MAIS CURTIDAS EM TAG */
function octopus_relatorio_tags_curtidas_brasil($cmid,$thread_id){
    global $DB;

    $query = " SELECT t.id as thread_id, CONCAT(u.firstname, ' ', u.lastname) as nome_autor, count(l.post_id) as quantidade_like, pa.paisnome,
           c.nome as cidade, e.nome as estado, cb.cbo_nome, type_message, p.user_id
           FROM mdl_octopus_thread t
           LEFT JOIN mdl_octopus_post p ON p.thread_id = t.id
           LEFT JOIN mdl_user u ON u.id = p.user_id
           LEFT JOIN mdl_octopus_like as l ON l.post_id = p.id
           LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
           LEFT JOIN quest_estados e ON e.id = ue.estado
           LEFT JOIN quest_cidades c ON c.id = ue.municipio
           LEFT JOIN quest_user_cbo cb ON cb.cbo_num = ue.cbo_num
           LEFT JOIN quest_pais pa ON pa.paisid = ue.pais

           WHERE t.cmid = ".$cmid." AND type = 1 AND is_head = 1 AND thread_id = ".$thread_id." ";

    $sql = $DB->get_records_sql($query);
    return $sql;

}



/* RETORNA O USUÁRIO COM MAIS 'NAO CURTIDAS' EM TAG */
function octopus_relatorio_tags_naocurtidas_brasil($cmid,$thread_id){
    global $DB;

    $query = " SELECT t.id as thread_id, CONCAT(u.firstname, ' ', u.lastname) as nome_autor, count(l.post_id) as quantidade_dislike,
           c.nome as cidade, e.nome as estado, cb.cbo_nome, type_message, p.user_id
           FROM mdl_octopus_thread t
           LEFT JOIN mdl_octopus_post p ON p.thread_id = t.id
           LEFT JOIN mdl_user u ON u.id = p.user_id
           LEFT JOIN mdl_octopus_like as l ON l.post_id = p.id
           LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
           LEFT JOIN quest_estados e ON e.id = ue.estado
           LEFT JOIN quest_cidades c ON c.id = ue.municipio
           LEFT JOIN quest_user_cbo cb ON cb.cbo_num = ue.cbo_num

           WHERE t.cmid = ".$cmid." AND type = 0 AND is_head = 1 AND thread_id = ".$thread_id." ";

    $sql = $DB->get_records_sql($query);
    return $sql;

}




//============================== REGIAO =================================
/* FUNÇÃO GENÉRICO. ESTA FUNCAO RETORNA AS TAGS UTILIZADAS EM CADA: REGIAO, ESTADO OU CIDADE, E/OU POR DATAS TAMBÉM */
function octopus_lista_tags_regiao($cmid, $pais, $regiao, $estado, $cidade, $data, $data_fim){
    global $DB;

    $condicao = '';
    if($pais != 0){
        $condicao = " AND ue.pais = ".$pais." ";
    } else{ }

    if($regiao != 0){
        $condicao = " AND ue.regiao = ".$regiao." ";
    } else{ }

    if($estado != 0){
        $condicao = " AND ue.estado = ".$estado." ";
    }else{  }

    if($cidade != 0){
        $condicao = " AND ue.municipio = ".$cidade." ";
    } else{ }

    if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }



    $query = " SELECT tag.id, name_tag, message, pa.paisid, pa.paisnome, e.nome as estado, c.nome as cidade
                FROM mdl_octopus_tag tag
                LEFT JOIN mdl_octopus_post_has_tag pht ON pht.tag_id = tag.id
                LEFT JOIN mdl_octopus_post p ON p.id = pht.post_id
                LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
                LEFT JOIN quest_estados e ON e.id = ue.estado
                LEFT JOIN quest_cidades c ON c.id = ue.municipio
                LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
                WHERE cmid = ".$cmid." AND is_head = 1 ".$condicao." ";

    $sql = $DB->get_records_sql($query);
    return $sql;
}




/* FUNÇÃO GENÉRICO. ESTA FUNCAO RETORNA A QUANTIDADE DE VEZES QUE A TAG FOI UTILIZADA EM CADA:
REGIAO, ESTADO OU CIDADE, E/OU POR DATAS TAMBÉM. DE ACORDO COM O QUE O USUÁRIO SELECIONAR NO FILTRO*/
function octopus_quantidade_tag_utilizada_regiao($cmid, $tag_id, $pais, $regiao, $estado, $cidade, $data, $data_fim){
    global $DB;

    $condicao = '';
    if($pais != 0){
        $condicao = $condicao. "AND ue.pais = ".$pais." ";
    } else{ }

    if($tag_id != 0){
        $condicao = $condicao. "AND pht.tag_id = ".$tag_id." ";
    } else{ }

    if($regiao != 0){
        $condicao = $condicao. " AND ue.regiao = ".$regiao." ";
    } else{ }

    if($estado != 0){
        $condicao = $condicao. " AND ue.estado = ".$estado." ";
    }else{ }

    if($cidade != 0){
        $condicao = $condicao. " AND ue.municipio = ".$cidade." ";
    } else{ }

    if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }

    $query = " SELECT count(pht.tag_id) as quantidade_utilizadas, pa.paisid, pa.paisnome, ue.regiao, ue.municipio, e.nome as estado
            FROM mdl_octopus_post p
            LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
            LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
            LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
            LEFT JOIN quest_estados e ON e.id = ue.estado
            LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
            WHERE tag.cmid = ".$cmid." ".$condicao."  ";

    $sql = $DB->get_records_sql($query);
    return $sql;
 }



/* FUNÇÃO GENÉRICA. RETORNA AS THREADS DE CADA TAG, FILTRANDO POR: REGIAO, ESTADO OU CIDADE, E/OU POR DATAS */
 function octopus_threads_tag_regiao($cmid, $tag_id, $pais, $regiao, $estado, $cidade, $data, $data_fim){
   global $DB;

    $condicao = '';

    if($tag_id != 0){
        $condicao = $condicao. " AND tag.id = ".$tag_id." ";
    } else{ }

    if($pais != 0){
        $condicao = $condicao. " AND ue.pais = ".$pais." ";
    } else{ }

    if($regiao != 0){
        $condicao = $condicao. " AND ue.regiao = ".$regiao." ";
    } else{ }

    if($estado != 0){
        $condicao = $condicao. " AND ue.estado = ".$estado." ";
    }else{ }

    if($cidade != 0){
        $condicao = $condicao. " AND ue.municipio = ".$cidade." ";
    } else{ }

   if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }


   $query = " SELECT  t.id as thread_id, pa.paisid, pa.paisnome, e.nome as estado, c.nome as cidade
              FROM mdl_octopus_thread t
              JOIN mdl_octopus_post p ON p.thread_id = t.id
              LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
              LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
              LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
              LEFT JOIN quest_estados e ON e.id = ue.regiao
              LEFT JOIN quest_cidades c ON c.id = ue.municipio
              LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
              WHERE t.cmid = ".$cmid." ".$condicao." ";

   $sql = $DB->get_records_sql($query);
   return $sql;

 }



/* RETORNA A THREAD_id DA TAG SELECIONADA */
function octopus_thread_comentarios_regiao($cmid, $thread_id){
   global $DB;


   $query = " SELECT  p.id, CONCAT(u.firstname, ' ', u.lastname) as nome_autor, pa.paisid, pa.paisnome, c.nome as cidade,
              e.nome as estado, cb.cbo_nome, tag.name_tag, type_message, p.thread_id,
              count(posts) as quantidade_comentarios, p.user_id
              FROM mdl_octopus_thread t
              LEFT JOIN mdl_octopus_post p ON p.thread_id = t.id
              JOIN mdl_user u ON u.id = p.user_id
              LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id

              LEFT JOIN quest_estados e ON e.id = ue.estado


              LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
              LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id

              LEFT JOIN quest_cidades c ON c.id = ue.municipio
              LEFT JOIN quest_user_cbo cb ON cb.cbo_num = ue.cbo_num

              LEFT JOIN quest_pais pa ON pa.paisid = ue.pais

              LEFT JOIN (  SELECT id as posts, thread_id, is_head as filho FROM mdl_octopus_post WHERE is_head = 0 ) AS pt ON pt.posts = p.id

              WHERE t.id = ".$thread_id." AND t.cmid = ".$cmid." ";

   $sql = $DB->get_records_sql($query);
   return $sql;

}



/* RETORNA O USUÁRIO QUE OBTEVE MAIS CURTIDAS EM UMA TAG. INFORMAÇÃO PARA O BOX*/
function octopus_relatorio_tags_curtidas_regiao($cmid, $thread_id){
    global $DB;

    $query = " SELECT t.id as thread_id, CONCAT(u.firstname, ' ', u.lastname) as nome_autor,
           count(l.post_id) as quantidade_like, pa.paisid, pa.paisnome, c.nome as cidade, e.nome as estado, cb.cbo_nome,
           type_message, p.user_id
           FROM mdl_octopus_thread t
           LEFT JOIN mdl_octopus_post p ON p.thread_id = t.id
           LEFT JOIN mdl_user u ON u.id = p.user_id
           LEFT JOIN mdl_octopus_like as l ON l.post_id = p.id
           LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
           LEFT JOIN quest_estados e ON e.id = ue.estado
           LEFT JOIN quest_cidades c ON c.id = ue.municipio
           LEFT JOIN quest_user_cbo cb ON cb.cbo_num = ue.cbo_num
           LEFT JOIN quest_pais pa ON pa.paisid = ue.pais

           WHERE thread_id = ".$thread_id." AND t.cmid = ".$cmid." AND type = 1
           AND is_head = 1 ";

    $sql = $DB->get_records_sql($query);
    return $sql;

}




/* RETORNA O USUÁRIO QUE OBTEVE MAIS 'NAO CURTIDAS' EM UMA TAG. INFORMAÇÃO PARA O BOX */
function octopus_relatorio_tags_naocurtidas_regiao($cmid, $thread_id){
    global $DB;

    $query = " SELECT t.id as thread_id, CONCAT(u.firstname, ' ', u.lastname) as nome_autor,
           count(l.post_id) as quantidade_dislike, pa.paisid, pa.paisnome, c.nome as cidade, e.nome as estado, cb.cbo_nome,
           type_message, p.user_id
           FROM mdl_octopus_thread t
           LEFT JOIN mdl_octopus_post p ON p.thread_id = t.id
           LEFT JOIN mdl_user u ON u.id = p.user_id
           LEFT JOIN mdl_octopus_like as l ON l.post_id = p.id
           LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
           LEFT JOIN quest_estados e ON e.id = ue.estado
           LEFT JOIN quest_cidades c ON c.id = ue.municipio
           LEFT JOIN quest_user_cbo cb ON cb.cbo_num = ue.cbo_num
           LEFT JOIN quest_pais pa ON pa.paisid = ue.pais

           WHERE thread_id = ".$thread_id." AND t.cmid = ".$cmid." AND type = 0
           AND is_head = 1 ";

    $sql = $DB->get_records_sql($query);
    return $sql;

}
//FIM RELATORIO COM TRATAMENTO DOS FILTROS



function octopus_get_info_tag($cmid){
    global $DB;

    $info_tags = $DB->get_records_sql(' SELECT name_tag, count(l.post_id) as qnt_tag, t.id as idtag, tag.tag_id, tag.post_id, p.message, p.id as id_post, p.thread_id, p.user_id, u.id, u.firstname, l.post_id, count(p.type_message) as type_message, t.cmid
                                FROM mdl_octopus_tag t
                                LEFT JOIN mdl_octopus_post_has_tag tag ON tag.tag_id = t.id
                                LEFT JOIN mdl_octopus_post p ON p.id = tag.post_id
                                LEFT JOIN mdl_user u ON u.id = p.user_id
                                LEFT JOIN ( SELECT post_id FROM mdl_octopus_like WHERE type = 1) as l ON l.post_id = tag.post_id
                                WHERE t.cmid = '.$cmid.' GROUP BY t.id ORDER BY qnt_tag DESC  ');
    return $info_tags;
}





/* ESTA FUNÇÃO RETORNA O TOTAL DE TAGS UTILIZADAS PARA SER EXIBIDO NO GRÁFICO */
function octopus_tags_total($cmid){

   global $DB;

    $query = "SELECT p.id as comentarios, p.id, p.thread_id, tag.name_tag, is_head, type_message, count(l.post_id) as curtidas
       FROM mdl_octopus_post p
       LEFT JOIN mdl_octopus_thread t ON t.id = p.thread_id
       LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
       LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
       LEFT JOIN mdl_octopus_like l ON l.post_id = p.id
       WHERE tag.cmid =".$cmid." and type=1 GROUP BY tag.id, t.id";

    $sql = $DB->get_records_sql($query);

}


/* RETORNA A QUANTIDADE TOTAL DE LIKES PARA SER ADICIONADO AO GRÁFICO DE RELATORIO */
function octopus_get_tags_total($cmid, $tag_id, $type, $pais, $regiao, $estado, $cidade, $data, $data_fim){
    global $DB;

    $condicao = '';
    if($pais != 0){
        $condicao = $condicao. " AND ue.pais = ".$pais." ";
    } else{ }

    if($regiao != 0){
        $condicao = $condicao. " AND ue.regiao = ".$regiao." ";
    }else { }

    if($estado != 0){
        $condicao = $condicao. " AND ue.estado = ".$estado." ";
    }else { }

    if($cidade != 0){
        $condicao = $condicao. " AND ue.municipio = ".$cidade." ";
    }else { }

    if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }

    $info_tags = $DB->get_records_sql('SELECT name_tag, count(l.post_id) as likes, r.nome_regiao as regiao, e.nome as estado, c.nome as cidade, pa.paisnome
                                FROM mdl_octopus_tag t
                                LEFT JOIN mdl_octopus_post_has_tag tag ON tag.tag_id = t.id
                                LEFT JOIN mdl_octopus_post p ON p.id = tag.post_id
                                LEFT JOIN mdl_user u ON u.id = p.user_id
                                LEFT JOIN mdl_octopus_like l ON l.post_id = tag.post_id
                                LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
                                LEFT JOIN quest_estados e ON e.id = ue.estado
                                LEFT JOIN quest_cidades c ON c.id = ue.municipio
                                LEFT JOIN quest_regiao r ON r.id = ue.regiao
                                LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
                                WHERE t.cmid = '.$cmid.' AND type = '.$type.' AND t.id = '.$tag_id.' '.$condicao.' GROUP BY tag.tag_id ');


    return $info_tags;
}



/* RETORNA A QUANTIDADE TOTAL DE COMENTÁRIOS PARA SER ADICIONADO AO GRÁFICO DE RELATORIO */
function octopus_contagem_total_comentarios($cmid,$tag_id, $pais, $regiao, $estado, $cidade, $data, $data_fim){
   global $DB;

    $condicao = '';
    if($pais != 0){
        $condicao = $condicao. " AND ue.pais = ".$pais." ";
    } else{ }

    if($regiao != 0){
        $condicao = $condicao. " AND ue.regiao = ".$regiao." ";
    }else { }

    if($estado != 0){
        $condicao = $condicao. " AND ue.estado = ".$estado." ";
    }else { }

    if($cidade != 0){
        $condicao = $condicao. " AND ue.municipio = ".$cidade." ";
    }else { }

    if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }

     $query = " SELECT t.name_tag, t.id as tag_id, COUNT(c.id) as comentarios, r.nome_regiao as regiao, e.nome as estado, cid.nome as cidade, pa.paisnome
                FROM mdl_octopus_post p
                LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
                LEFT JOIN mdl_octopus_tag t ON t.id = pht.tag_id
                LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
                LEFT JOIN quest_estados e ON e.id = ue.estado
                LEFT JOIN quest_cidades cid ON cid.id = ue.municipio
                LEFT JOIN quest_regiao r ON r.id = ue.regiao
                LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
                LEFT JOIN ( SELECT id, type_message, thread_id FROM mdl_octopus_post WHERE type_message = 3) as c ON c.thread_id = p.thread_id
                WHERE t.cmid = ".$cmid." AND tag_id = ".$tag_id." ".$condicao." ";




   $sql = $DB->get_records_sql($query);
     return $sql;

}



//lista as tags do octopus
 function octopus_localizacao_tags($cmid, $tag, $pais, $regiao, $estado, $cidade, $data, $data_fim){
     global $DB;

     $condicao = '';
     $condicao_data = '';
     if($pais == 0){
          $condicao = $condicao. " GROUP BY ue.pais ";
     }elseif($regiao == 0){
          $condicao = $condicao. " GROUP BY ue.regiao ";
     }else if($estado == 0){
         $condicao = $condicao. " AND ue.regiao = ".$regiao." GROUP BY ue.estado ";
     }else if($cidade == 0){
         $condicao = $condicao. "  AND ue.regiao = ".$regiao."  AND ue.estado = ".$estado." GROUP BY ue.municipio ";
     }

     if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }


   $query = " SELECT distinct c.nome as cidade, tag.id, name_tag, pa.paisid, pa.paisnome, r.nome_regiao as regiao, e.nome as estado, ue.regiao as id_regiao, ue.estado as id_estado, ue.municipio as id_cidade
           FROM mdl_octopus_post p
           LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
           LEFT JOIN mdl_octopus_post_has_tag tg ON tg.post_id = p.id
           LEFT JOIN mdl_octopus_tag tag ON tag.id = tg.tag_id
           LEFT JOIN quest_estados e ON e.id = ue.estado
           LEFT JOIN quest_cidades c ON c.id = ue.municipio
           LEFT JOIN quest_regiao r ON r.id = ue.regiao
           LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
           WHERE cmid = ".$cmid." AND tag.id = ".$tag." ".$condicao_data." ".$condicao." ";

   $sql = $DB->get_records_sql($query);
   return $sql;

}


function octopus_localizacao_tags_total_comentarios($cmid, $tag_id, $pais, $regiao, $estado, $cidade, $data, $data_fim){
   global $DB;

     $condicao = '';
     $condicao_data = '';
     if($pais != 0){

         if($pais == 1){

            if($regiao != 0 && $estado == 0 && $cidade == 0){

                $condicao = $condicao. " AND ue.pais = ".$pais." AND ue.regiao = ".$regiao." GROUP BY ue.pais ";
            }else if($estado != 0 && $cidade == 0){

                $condicao = $condicao. " AND ue.regiao = ".$regiao." AND ue.estado = ".$estado." GROUP BY ue.estado, ue.municipio ";
            }else if($cidade != 0){

                $condicao = $condicao. " AND ue.regiao = ".$regiao." AND ue.estado = ".$estado." AND ue.municipio = ".$cidade." GROUP BY ue.municipio ";
            }

         }else{
            $condicao = $condicao. " AND ue.pais = ".$pais." GROUP BY ue.pais ";
         }

     }else{
         $condicao = $condicao. " GROUP BY ue.pais ";
     }


    if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }

     $query = " SELECT cid.nome as cidade, t.name_tag, t.id as tag_id, COUNT(c.id) as comentarios, pa.paisid, pa.paisnome, r.nome_regiao as regiao, e.nome as estado
                FROM mdl_octopus_post p
                LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
                LEFT JOIN mdl_octopus_tag t ON t.id = pht.tag_id
                LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
                LEFT JOIN quest_estados e ON e.id = ue.estado
                LEFT JOIN quest_cidades cid ON cid.id = ue.municipio
                LEFT JOIN quest_regiao r ON r.id = ue.regiao
                LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
                LEFT JOIN ( SELECT id, type_message, thread_id FROM mdl_octopus_post WHERE type_message = 3) as c ON c.thread_id = p.thread_id
                WHERE t.cmid = ".$cmid." AND tag_id = ".$tag_id." ".$condicao_data." ".$condicao." ";

   $sql = $DB->get_records_sql($query);
   return $sql;

}



//RETORNA A QUANTIDADE TOTAL DE LIKES PARA SER ADICIONADO AO GRÁFICO DE RELATORIO
function octopus_localizacao_tags_total_curtidas($cmid, $tag_id, $type, $pais, $regiao, $estado, $cidade, $data, $data_fim){
    global $DB;

    $condicao = '';
    $condicao_data = '';

    if($pais != 0){

         if($pais == 1){

            if($regiao != 0 && $estado == 0 && $cidade == 0){

                $condicao = $condicao. " AND ue.pais = ".$pais." AND ue.regiao = ".$regiao." GROUP BY ue.pais ";
            }else if($estado != 0 && $cidade == 0){

                $condicao = $condicao. " AND ue.regiao = ".$regiao." AND ue.estado = ".$estado." GROUP BY ue.estado, ue.municipio ";
            }else if($cidade != 0){

                $condicao = $condicao. " AND ue.regiao = ".$regiao." AND ue.estado = ".$estado." AND ue.municipio = ".$cidade." GROUP BY ue.municipio ";
            }

         }else{
            $condicao = $condicao. " AND ue.pais = ".$pais." GROUP BY ue.pais ";
         }

     }else{
         $condicao = $condicao. " GROUP BY ue.pais ";
     }


    if($data != 0 && $data_fim != 0){

       $condicao = $condicao. " AND p.timecreated BETWEEN ".$data." AND ".$data_fim." ";

    } else{

         if($data != 0 && $data_fim == 0){
               $condicao = $condicao. " AND p.timecreated >= ".$data." ";
        }

         if($data == 0 && $data_fim != 0){
              $condicao = $condicao. " AND p.timecreated <= ".$data_fim." ";
        }

    }

    $info_tags = ' SELECT name_tag, count(l.post_id) as likes, pa.paisid, r.nome_regiao as regiao, e.nome as estado, c.nome as cidade
                                FROM mdl_octopus_tag t
                                LEFT JOIN mdl_octopus_post_has_tag tag ON tag.tag_id = t.id
                                LEFT JOIN mdl_octopus_post p ON p.id = tag.post_id
                                LEFT JOIN mdl_user u ON u.id = p.user_id
                                LEFT JOIN mdl_octopus_like l ON l.post_id = tag.post_id
                                LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
                                LEFT JOIN quest_estados e ON e.id = ue.estado
                                LEFT JOIN quest_cidades c ON c.id = ue.municipio
                                LEFT JOIN quest_regiao r ON r.id = ue.regiao
                                LEFT JOIN quest_pais pa ON pa.paisid = ue.pais
                                WHERE t.cmid = '.$cmid.' AND type = '.$type.' AND t.id = '.$tag_id.' '.$condicao_data.' '.$condicao.' ';


    $sql = $DB->get_records_sql($info_tags);
    return $sql;
}




/* RELATORIO USUARIOS. INFORMAÇÕES DO USUÁRIO PARA O BOX */
function octopus_tags_user($cmid,$user_id,$thread_id){
    global $DB;

    $query = " SELECT tg.name_tag, CONCAT(u.firstname, ' ', u.lastname) as nome_autor
               FROM mdl_octopus_thread t
               INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
               LEFT JOIN mdl_user u ON u.id = p.user_id
               LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
               LEFT JOIN mdl_octopus_tag tg ON tg.id = pht.tag_id
               WHERE t.cmid = ".$cmid." AND p.user_id = ".$user_id." AND p.thread_id = ".$thread_id." AND t.id = ".$thread_id." AND p.type_message != 3 ";

    $sql = $DB->get_records_sql($query);

    return $sql;
}


/* RELATORIO USUARIOS. CONTABILIZA QUANTAS POSTAGENS O USUÁRIO REALIZOU DENTRO DO FORUM */
function octopus_get_count_posts_user($cmid,$user_id){
    global $DB;

    $query = " SELECT distinct thread_id, t.cmid, p.user_id,  t.id as quantidade_postagens, CONCAT(u.firstname, ' ', u.lastname) as nome_autor, r.nome_regiao
               FROM mdl_octopus_thread t
               INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
               LEFT JOIN mdl_user u ON u.id = p.user_id
               LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
               LEFT JOIN quest_regiao r ON r.id = ue.regiao
               WHERE t.cmid = ".$cmid." AND p.user_id = ".$user_id." AND is_head = 1";
    $sql = $DB->get_records_sql($query);

    return count($sql);
}



/* RELATORIO USUARIOS. RETORNA OS id's DE CADA POSTAGEM(THREAD) DO USUÁRIO */
function octopus_get_threads_user($cmid,$user_id){
    global $DB;

    $query = " SELECT thread_id, t.cmid, p.user_id, p.id, CONCAT(u.firstname, ' ', u.lastname) as nome_autor
               FROM mdl_octopus_thread t
               INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
               LEFT JOIN mdl_user u ON u.id = p.user_id
               WHERE t.cmid = ".$cmid." AND p.user_id = ".$user_id." group by t.id ";

    $sql = $DB->get_records_sql($query);

    return $sql;
}


/* RELATORIO USUARIOS. RETORNA A QUANTIDADE TOTAL DE COMENTÁRIOS QUE O USUÁRIO RECEBEU POR CADA POSTAGEM */
function octopus_get_count_comentarios_user($cmid,$thread_id){
    global $DB;

    $query = " SELECT p.id as quantidade_comentarios, thread_id,  t.cmid, p.user_id,  CONCAT(u.firstname, ' ', u.lastname) as nome_autor, r.nome_regiao, type_message
               FROM mdl_octopus_thread t
               INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
               LEFT JOIN mdl_user u ON u.id = p.user_id
               LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
               LEFT JOIN quest_regiao r ON r.id = ue.regiao
               WHERE t.cmid = ".$cmid." AND p.thread_id = ".$thread_id." AND p.type_message = 3 ";

    $sql = $DB->get_records_sql($query);

    return count($sql);
}



/* RELATORIO USUARIOS. RETORNA A QUANTIDADE DE POSTAGENS PARA CADA TAG UTILIZADA PELO USUÁRIO, COMO TAMBÉM AS TAGS QUE O MESMO UTILIZOU */
function octopus_count_tags_user($cmid,$user_id){
    global $DB;

    $query = " SELECT  tag.name_tag, t.id as thread, pht.tag_id, p.user_id, count(p.id) as qnt_posts, c.nome as cidade, e.nome as estado, cb.cbo_nome, CONCAT(u.firstname, ' ', u.lastname) as nome_usuario
                FROM mdl_octopus_thread t
                JOIN mdl_octopus_post p ON p.thread_id = t.id
                LEFT JOIN mdl_user u ON u.id = p.user_id
                LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
                LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
                LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
                LEFT JOIN quest_estados e ON e.id = ue.estado
                LEFT JOIN quest_cidades c ON c.id = ue.municipio
                LEFT JOIN quest_user_cbo cb ON cb.cbo_num = ue.cbo_num

                WHERE t.cmid = ".$cmid." AND is_head = 1 AND p.user_id = ".$user_id." GROUP BY pht.tag_id ";

    $sql = $DB->get_records_sql($query);

    return $sql;

}



/* RELATORIO USUARIOS. RETORNA A QUANTIDADE DE LIKE E DISLIKE QUE O USUÁRIO RECEBEU */
function octopus_get_count_like_dislike_user($cmid,$user_id){
    global $DB;

    $query = " SELECT count(postid) as quantidade_like, t.cmid, p.user_id, thread_id, count(id_post) as quantidade_dislike, CONCAT(u.firstname, ' ', u.lastname) as nome_autor, likes.user_id, li.user
               FROM mdl_octopus_thread t
               INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
               INNER JOIN mdl_user u ON u.id = p.user_id
               LEFT JOIN ( SELECT post_id as postid, type, user_id FROM mdl_octopus_like WHERE type = 1) as likes ON likes.postid = p.id
               LEFT JOIN ( SELECT post_id as id_post, type as tipo, user_id as user FROM mdl_octopus_like WHERE type = 0) as li ON li.id_post = p.id
               WHERE t.cmid = ".$cmid." AND p.user_id = ".$user_id."  ORDER BY quantidade_like DESC ";
    $sql = $DB->get_records_sql($query);

    return $sql;
}


/* RELATORIO USUARIOS. RETORNA O ID DA POSTAGEM DO USUÁRIO QUE FOI MAIS CURTIDA. A PARTIR ESSA INFORMAÇÃO CRIAMOS O LINK PARA DAR ACESSO*/
function octopus_get_link_curtida_user($cmid,$user_id){
    global $DB;

    $query = " SELECT MAX(quantidade_likes), cmid, thread_id, nome_autor, user_id  FROM
            (
            SELECT t.cmid, p.user_id, thread_id, count(postid) as quantidade_likes, CONCAT(u.firstname, ' ', u.lastname) as nome_autor
                           FROM mdl_octopus_thread t
                           INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
                           INNER JOIN mdl_user u ON u.id = p.user_id

                           LEFT JOIN ( SELECT post_id as postid, type, user_id FROM mdl_octopus_like WHERE type = 1 AND user_id = 2) as likes ON likes.postid = p.id
                           WHERE t.cmid = ".$cmid." AND p.user_id = ".$user_id." GROUP BY t.id ORDER BY quantidade_likes DESC
            ) as temp               ";


    $sql = $DB->get_records_sql($query);

    return $sql;
}




/* RELATORIO USUARIOS. RETORNA O ID DA POSTAGEM DO USUÁRIO QUE FOI MAIS 'NAO CURTIDA'. A PARTIR ESSA INFORMAÇÃO CRIAMOS O LINK PARA DAR ACESSO*/
function octopus_get_link_naocurtida_user($cmid,$user_id){
    global $DB;

    $query = " SELECT MAX(quantidade_dislikes), cmid, thread_id, nome_autor, user_id  FROM
            (
            SELECT t.cmid, p.user_id, thread_id, count(id_post) as quantidade_dislikes, CONCAT(u.firstname, ' ', u.lastname) as nome_autor
                           FROM mdl_octopus_thread t
                           INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
                           INNER JOIN mdl_user u ON u.id = p.user_id


                           LEFT JOIN ( SELECT post_id as id_post, type as tipo, user_id as user FROM mdl_octopus_like WHERE type = 0 AND user_id = 2) as li ON li.id_post = p.id
                           WHERE t.cmid = ".$cmid." AND p.user_id = ".$user_id." GROUP BY t.id ORDER BY quantidade_dislikes DESC
            ) as temp               ";

    $sql = $DB->get_records_sql($query);

    return $sql;
}



/* RELATORIO USUARIOS. RETORNA O ID DA POSTAGEM DO USUÁRIO QUE FOI MAIS COMENTADA. A PARTIR ESSA INFORMAÇÃO CRIAMOS O LINK PARA DAR ACESSO*/
function octopus_get_link_comentada_user($cmid,$user_id){
    global $DB;

    $query = " SELECT MAX(quantidade_comentarios), cmid, thread_id, nome_autor, user_id  FROM
            (
              SELECT t.cmid, p.user_id, thread_id, count(post_id) as quantidade_comentarios, CONCAT(u.firstname, ' ', u.lastname) as nome_autor
               FROM mdl_octopus_thread t
               INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
               INNER JOIN mdl_user u ON u.id = p.user_id
               LEFT JOIN ( SELECT id as post_id, type_message, user_id FROM mdl_octopus_post WHERE is_head = 0) as post ON post.post_id = p.id
               WHERE t.cmid = ".$cmid." AND p.user_id = ".$user_id." GROUP BY t.id ORDER BY quantidade_comentarios DESC
            ) as temp                ";

    $sql = $DB->get_records_sql($query);

    return $sql;
}



function octopus_brasil($cmid){
    global $DB;

    $query = " SELECT coumt(p.id) as qnt_vezes_usadas, p.thread_id, t.name_tag, pht.tag_id, pht.post_id, r.nome_regiao as regiao, e.nome as estado, p.type_message, p.user_id
                FROM mdl_octopus_thread tr
                JOIN mdl_octopus_post p ON p.thread_id = tr.id
                LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
                LEFT JOIN mdl_octopus_tag t ON t.id = pht.tag_id
                LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
                LEFT JOIN quest_regiao r ON r.id = ue.regiao
                LEFT JOIN quest_estados e ON e.id = ue.estado
                WHERE tr.cmid = ".$cmid." AND is_head = 1 GROUP BY pht.tag_id, ue.regiao ";

    $sql = $DB->get_records_sql($query);

    return $sql;

}


function octopus_comentarios_brasil($cmid,$thread_id){
    global $DB;

    $query = " SELECT t.id, count(p.id) as comentarios, CONCAT(u.firstname, ' ', u.lastname) as nome_autor, r.nome_regiao as regiao, e.nome as estado
               FROM mdl_octopus_thread t
               INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
               LEFT JOIN mdl_user u ON u.id = p.user_id
               LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
               LEFT JOIN quest_regiao r ON r.id = ue.regiao
               LEFT JOIN quest_estados e ON e.id = ue.estado
               WHERE t.cmid = ".$cmid." AND p.thread_id = ".$thread_id." AND is_head = 0 ";

    $sql = $DB->get_records_sql($query);

    return $sql;

}



function octopus_curtidas_brasil($cmid,$thread_id){
    global $DB;

    $query = " SELECT t.id, count(p.id) as comentarios, CONCAT(u.firstname, ' ', u.lastname) as nome_autor, r.nome_regiao as regiao, e.nome as estado,
    count(type) as likes, tag.name_tag as nome_tag
               FROM mdl_octopus_thread t
               INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
               LEFT JOIN mdl_user u ON u.id = p.user_id
               LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
               LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
               LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
               LEFT JOIN quest_regiao r ON r.id = ue.regiao
               LEFT JOIN quest_estados e ON e.id = ue.estado
               LEFT JOIN mdl_octopus_like l ON l.post_id = p.id
               WHERE t.cmid = ".$cmid." AND p.thread_id = ".$thread_id." AND type = 1 ";

    $sql = $DB->get_records_sql($query);

    return $sql;
}



function octopus_naocurtidas_brasil($cmid,$thread_id){
    global $DB;

    $query = " SELECT t.id, count(p.id) as comentarios, CONCAT(u.firstname, ' ', u.lastname) as nome_autor, r.nome_regiao as regiao, e.nome as estado,
    count(type) as likes, tag.name_tag as nome_tag
               FROM mdl_octopus_thread t
               INNER JOIN mdl_octopus_post p ON p.thread_id = t.id
               LEFT JOIN mdl_user u ON u.id = p.user_id
               LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
               LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
               LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
               LEFT JOIN quest_regiao r ON r.id = ue.regiao
               LEFT JOIN quest_estados e ON e.id = ue.estado
               LEFT JOIN mdl_octopus_like l ON l.post_id = p.id
               WHERE t.cmid = ".$cmid." AND p.thread_id = ".$thread_id." AND type = 1 ";

    $sql = $DB->get_records_sql($query);

    return $sql;
}



//retorna as threads de cada tag, e a quantidade de vezes que a thread foi utilizada. Nivel Brasil
function octopus_tags_threads($cmid,$tag_id){
    global $DB;

    $query = " SELECT t.id as thread, tag.name_tag, pht.tag_id as vezes_tag_utilizada
                FROM mdl_octopus_thread t
                JOIN mdl_octopus_post p ON p.thread_id = t.id
                LEFT JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
                LEFT JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
                WHERE t.cmid = ".$cmid." AND is_head = 1 AND pht.tag_id = ".$tag_id." ";

    $sql = $DB->get_records_sql($query);

    return $sql;
}




function octopus_Verificar_Likes_Threads($thread_id) {
      global $DB;
      $query1 = "SELECT DISTINCT mdl_octopus_like.user_id FROM mdl_octopus_post,mdl_octopus_like WHERE mdl_octopus_post.`thread_id` = $thread_id AND mdl_octopus_like.post_id = mdl_octopus_post.id";
      $ti = $DB->get_records_sql($query1);
      return $ti;
}

function octopus_Verificar_Likes_Threads_Comentario($thread_id) {
      global $DB;
      $query1="Select DISTINCT mdl_octopus_post.id,mdl_octopus_like.user_id,mdl_octopus_post.message "
              . "FROM mdl_octopus_post,mdl_octopus_like WHERE mdl_octopus_post.`thread_id` = $thread_id "
              . "AND mdl_octopus_post.type_message = 3 AND mdl_octopus_like.post_id = mdl_octopus_post.id";
      $ti = $DB->get_records_sql($query1);
      return $ti;
}

function octopus_check_likes_answers($user_id, $post_id, $type) {
      global $DB;
      $query1="SELECT * FROM  mdl_octopus_like l, mdl_octopus_post p WHERE l.user_id = $user_id AND l.post_id = $post_id AND l.post_id = p.id AND p.type_message = $type";
      $ti = $DB->get_records_sql($query1);
      return $ti;
}


function octopus_get_user_map_info($id){
     global $DB;
      $query1="SELECT c.nome as cidade, e.nome as estado, cb.cbo_nome, f.paisNome as pais FROM quest_user_extra ue
                LEFT JOIN quest_estados e ON e.id = ue.estado
                LEFT JOIN quest_cidades c ON c.id = ue.municipio
                LEFT JOIN quest_pais f ON f.paisId = ue.pais
                LEFT JOIN quest_user_cbo cb ON cb.cbo_num = ue.cbo_num
              WHERE user_id = $id";
      $ti = $DB->get_records_sql($query1);
      return $ti;
}


function octopus_get_keyapi($cmid){
    global $DB;

    $query = " SELECT DISTINCT cm.id, cm.module, cm.instance, cm.section, o.keyapi, o.id, o.name, cm.instance
                FROM mdl_course_modules cm
                INNER JOIN mdl_octopus o ON o.id = cm.instance
                WHERE cm.id = ".$cmid." ";

    $codeapi = $DB->get_records_sql($query);

    return $codeapi;

}


function octopus_get_list_cmid(){
   global $DB;  
   $query = " SELECT cm.id, cm.course FROM mdl_modules m, mdl_course_modules cm WHERE                  
                  cm.module = m.id AND m.name = 'octopus'";

   $cmidlist = $DB->get_records_sql($query);

   return $cmidlist;
}


//retorna o peso da atividade p o ranking
function octopus_get_peso($cmid) {
   global $DB;

   $post = $DB->get_record('octopus_set_peso_grade', array('cmid' => $cmid));
   return $post;
}

//retorna o texto quebrado
function oct_limit_crt($texto, $limite, $quebra = true){
   $tamanho = strlen($texto);
   if($tamanho <= $limite){ //Verifica se o tamanho do texto é menor ou igual ao limite
      $novo_texto = $texto;
   }else{ // Se o tamanho do texto for maior que o limite
      if($quebra == true){ // Verifica a opção de quebrar o texto
         $novo_texto = trim(substr($texto, 0, $limite))."...";
      }else{ // Se não, corta $texto na última palavra antes do limite
         $ultimo_espaco = strrpos(substr($texto, 0, $limite), " "); // Localiza o útlimo espaço antes de $limite
         $novo_texto = trim(substr($texto, 0, $ultimo_espaco))."..."; // Corta o $texto até a posição localizada
      }
   }
   return $novo_texto; // Retorna o valor formatado
}


function octopus_get_posts_from_thread_limited($my_id , $tid, $post_search,  $limite, $offset, $first = true) {
    global $DB;
    
    if($post_search != 0){
       $post_query = " AND id =".$post_search;
       
    }else{
       $post_query = ""; 
       
    }
    

    //$posts = $DB->get_records('octopus_post', array('thread_id' => $tid));
    
    $posts = $DB->get_records_sql('SELECT * FROM mdl_octopus_post WHERE thread_id = '.$tid.' '.$post_query.' AND type_message = 3 ORDER BY timecreated DESC LIMIT '.$limite.' OFFSET '.$offset);
    
    if(!$first) {
        // Retirando a head da thread.
        $posts = array_slice($posts, 0,-1);
    }

    foreach($posts as $post) {
        $post->user = octopus_get_user($post->user_id);
        $post->count_likes = octopus_get_post_likes_num($post->id);
        $post->count_dislikes = octopus_get_post_dislikes_num($post->id);
        
        $like = $DB->get_record('octopus_like', array('user_id' => $my_id, 'post_id' => $post->id));
        
        $post->status_ld = 2;
        
        if($like && ($like->type == 1)){
            $post->status_ld = 1;
            
        }else if ($like && ($like->type == 0)) {
            $post->status_ld = 0;
             
        }
        
    }

    return $posts;
}


function getStatusLD($my_id,$post_id ){
    
     global $DB;
     $like = $DB->get_record('octopus_like', array('user_id' => $my_id, 'post_id' => $post_id));
        
        $status_ld = 2;
        
        if($like && ($like->type == 1)){
            $status_ld = 1;
            
        }else if ($like && ($like->type == 0)) {
            $status_ld = 0;
             
        }
        
        return $status_ld;
                
    
}


/* Editar */

function octopus_update_post($user_id, $cmid, $thread_id, $post_id, $title , $message, $is_head){
    
    global $DB;
    
    $titulo = new stdClass();
    $titulo->id = $thread_id;
    $titulo->cmid = $cmid;
    $titulo->title = $title;
    
    $DB->update_record('octopus_thread', $titulo);
    
    
    $post = new stdClass();
    $post->id = $post_id;
    $post->is_head = $is_head;
    $post->user_id = $user_id;
    $post->thread_id = $thread_id;
    $post->message = $message;
    
    
    $DB->update_record('octopus_post', $post);
  
     
    return 1;
}


function octopus_update_comment($user_id, $cmid, $thread_id, $post_id , $message, $is_head){
      
    global $DB;
   
    $post = new stdClass();
    $post->id = $post_id;
    $post->is_head = $is_head;
    $post->user_id = $user_id;
    $post->thread_id = $thread_id;
    $post->message = $message;
    
    
    $DB->update_record('octopus_post', $post);
    
    return 1;
}

// ##################### Inicio das querys de Recomendacao #################################################

 
//Funções para calcular user engaged e recomendar
// quantidade de posts do tipo narrativa de cada tag que o usuário usou para faxzer um 
function octopus_get_post_narrativa($id, $cmid) {
    global $DB;
    $query1 = " SELECT tag.name_tag, COUNT(p.id) as qnt_posts, qr.nome_regiao
               FROM mdl_octopus_thread tr 
               INNER JOIN mdl_octopus_post p ON p.thread_id = tr.id
               INNER JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
               INNER JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
               INNER JOIN quest_user_extra que ON que.user_id = p.user_id
               INNER JOIN quest_regiao qr ON qr.id = que.regiao
               where  p.type_message = 2 ANd p.user_id = $id AND tr.cmid = $cmid
               GROUP by  tag.name_tag order by tag.name_tag";
    
   
    $ti = $DB->get_records_sql($query1);
    return $ti;
}

//quantidade de posts do tipo narrativa
function octopus_get_qnt_post_narrativa($id, $cmid) {
    global $DB;
    $query1 = "SELECT COUNT(p.id) as qnt_post_narrativa 
               FROM mdl_octopus_thread tr
               INNER JOIN mdl_octopus_post p ON p.thread_id = tr.id
               WHERE p.type_message = 2 AND p.user_id = $id AND tr.cmid = $cmid";
    $ti = $DB->get_records_sql($query1);
    return $ti;
}

/* Select retorna  quantidade de curtidas de cada tag no qual o usuário usou para fazer posts 
  do tipo narrativa */

function octopus_get_qnt_curtidas_narrativa($id, $tag_id, $cmid) {
    global $DB;
    $query1 = "SELECT  COUNT(l.type) as qnt_curtidas
            FROM mdl_octopus_thread tr 
            INNER JOIN mdl_octopus_post p ON p.thread_id = tr.id 
            INNER JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id 
            INNER JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id 
            INNER JOIN mdl_octopus_like l ON l.post_id = p.id 
            INNER JOIN quest_user_extra que ON que.user_id = p.user_id
            INNER JOIN quest_regiao qr ON qr.id = que.regiao
            WHERE p.type_message = 2 ANd p.user_id = $id AND tr.cmid = $cmid AND l.type = 1 AND tag.id = $tag_id";
    $ti = $DB->get_records_sql($query1);
    return $ti;
}

/* Select retorna  quantidade de não curtidas de cada tag no qual o usuário usou para fazer posts 
  do tipo narrativa */

function octopus_get_qnt_nao_curtidas_narrativa($id, $tag_id, $cmid) {
    global $DB;
    $query1 = "SELECT  COUNT(l.type) as qnt_n_curtidas
               FROM mdl_octopus_thread tr 
               INNER JOIN mdl_octopus_post p ON p.thread_id = tr.id 
               INNER JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id 
               INNER JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id 
               INNER JOIN mdl_octopus_like l ON l.post_id = p.id 
               INNER JOIN quest_user_extra que ON que.user_id = p.user_id
               INNER JOIN quest_regiao qr ON qr.id = que.regiao
               where p.type_message = 2 ANd p.user_id = $id AND tr.cmid = $cmid AND l.type = 0 AND tag.id = $tag_id";

    $ti = $DB->get_records_sql($query1);
    return $ti;
}

/* Select retorna nome das tags e id no qual o usuário usou para fazer posts 
  do tipo narrativa */

function octopus_get_tag_narrativa($id, $cmid) {
    global $DB;
    $query1 ="SELECT tag.name_tag, tag.id
             FROM mdl_octopus_thread tr 
             INNER JOIN mdl_octopus_post p ON p.thread_id = tr.id
             INNER JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
             INNER JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
             INNER JOIN quest_user_extra que ON que.user_id = p.user_id
             INNER JOIN quest_regiao qr ON qr.id = que.regiao
             where  p.type_message = 2 AND p.user_id = $id AND tr.cmid = $cmid
             GROUP by  tag.name_tag order by tag.name_tag";
    $ti = $DB->get_records_sql($query1);
    return $ti;
}


//lista todas as threads de uma tag
function octopus_get_threads_narrativas($cmid, $id, $tag_id) {
    global $DB;
    $query1 = "SELECT p.thread_id FROM mdl_octopus_post p
              INNER JOIN mdl_octopus_thread tr ON tr.id = p.thread_id
              INNER JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
              WHERE tr.cmid = $cmid AND p.user_id = $id AND p.type_message = 2 AND pht.tag_id = $tag_id";
    $ti = $DB->get_records_sql($query1);
    return $ti;
}

//lista todos os comentrários de uma thread
function octopus_get_qnt_comentarios_narrativas($thread_id, $cmid) {
    global $DB;
    $query1 = "SELECT  COUNT(DISTINCT p.id) as qnt_comentarios
              FROM  mdl_octopus_post p, mdl_octopus_thread th
              WHERE p.thread_id =$thread_id AND p.type_message=3 AND th.cmid = $cmid";
    $ti = $DB->get_records_sql($query1);
    return $ti;
}

//lista todos os posts do tipo narrativa baseado em uma tag específica
function octopus_get_qnt_posts_narrativas_peso($tag_id, $cmid) {
    global $DB;
    $query1 = "SELECT  COUNT(p.id) as qnt_posts_narrativa,Count(DISTINCT p.user_id ) as qnt_pessoas_falam
               FROM mdl_octopus_thread tr 
               INNER JOIN mdl_octopus_post p ON p.thread_id = tr.id
               INNER JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
               INNER JOIN mdl_octopus_tag tag ON tag.id = pht.tag_id
               INNER JOIN quest_user_extra que ON que.user_id = p.user_id
               INNER JOIN quest_regiao qr ON qr.id = que.regiao
               where  p.type_message = 2  AND tr.cmid = $cmid AND tag.id=$tag_id";
    $ti = $DB->get_records_sql($query1);
    return $ti;
}

//Fazer a query que retorne usuários inscritos no curso
function octopus_get_usuarios_inscritos_curso($cmid) {
    global $DB;
    $query1 ="SELECT COUNT(user_id) as qnt_user_inscritos FROM mdl_octopus_user_preferences WHERE cmid = $cmid";
    $ti = $DB->get_records_sql($query1);
    return $ti;
}
//inserir dados na tabela de engaged_user
function octopus_new_engaged_user($user_id,$punctuation,$tag_id,$cmid) {
    global $DB;

    $newengaged = new stdClass();
    $newengaged->user_id = $user_id;
    $newengaged->punctuation = $punctuation;
    $newengaged->tag_id = $tag_id;
    $newengaged->cmid = $cmid;
    $newengaged->timec =time();
    
    return $DB->insert_record('octopus_engaged_user', $newengaged);
    
}

// ##################### Fim das querys de Recomendacao (engaged.php) #################################################


######################################Início das querys para recomendar usuário engajado para outros usuários######################
//retorna tags do tipo "pergunta" de um usuario
function octopus_get_questionable_tags_user($cmid,$user_id) {
   global $DB;
   
   $query = " SELECT *
               FROM mdl_octopus_post p
               JOIN mdl_octopus_post_has_tag ht ON p.id = ht.post_id
               JOIN mdl_octopus_tag t ON ht.tag_id = t.id
               WHERE t.cmid = $cmid AND p.type_message = 1 AND p.user_id = $user_id";    
   
   $tags = $DB->get_records_sql($query);
   return $tags;
               
}
//retorna todos os usuários engajados que postam sobre as tags que os usuários possuem dúvidas
function octopus_get_user_engaged($cmid,$tag) {
   global $DB;    
   $query = "SELECT  DISTINCT user_id FROM mdl_octopus_engaged_user WHERE cmid = $cmid AND tag_id = $tag  ORDER BY timec DESC";
   $user = $DB->get_records_sql($query);
   return $user;
               
}
//retorna todos os dados mais atualizados do usuário da tabela engaged 
function octopus_get_user_engaged_Ranking($cmid,$tag,$user_id){
    global $DB;
    $query = "SELECT * FROM mdl_octopus_engaged_user WHERE cmid = $cmid AND tag_id = $tag AND user_id = $user_id ORDER BY id DESC LIMIT 1";
    $user = $DB->get_records_sql($query);
    return $user;
}

//retorna região do usuário logado
function octopus_get_user_region($user_id) {
   global $DB;    
   $query = "SELECT DISTINCT qr.nome_regiao
             FROM  quest_user_extra que 
             INNER JOIN quest_regiao  qr ON qr.id = que.regiao
             INNER JOIN mdl_user u ON u.id = que.user_id
             WHERE que.user_id= $user_id";
   
    $user = $DB->get_records_sql($query);
    return $user;
}
//retorna região do usuário engajado
function octopus_get_user_region_Engaged($user_id,$tag_id,$cmid) {
   global $DB;    
   $t = time() - (5 * 60);
   $query =  "SELECT DISTINCT u.firstname, u.id as userid, u.lastname,qr.nome_regiao, t.name_tag, eu.punctuation,eu.id, cb.cbo_nome,
             IF(u.id IN (SELECT DISTINCT user_id
                            FROM mdl_octopus_log
                            WHERE time > $t AND cmid = $cmid), 'online', 'offline'
                ) as status
             FROM  quest_user_extra que 
             INNER JOIN quest_regiao  qr ON qr.id = que.regiao
             INNER JOIN mdl_user u ON u.id = que.user_id
             INNER JOIN mdl_octopus_engaged_user eu ON que.user_id = eu.user_id
             INNER JOIN mdl_octopus_tag t ON t.id = eu.tag_id
             LEFT JOIN quest_user_cbo cb ON cb.cbo_num = que.cbo_num
             WHERE que.user_id = $user_id AND  t.id = $tag_id  AND t.cmid = $cmid ORDER BY eu.id DESC LIMIT 1";

    $user = $DB->get_records_sql($query);
    return $user;
}

// ##################### Fim  das querys para recomendar usuário engajado para outros usuários#################################################

//############################################### feedback  da recomendaçao com estrela ###########################################################

function InserirAvaliacaoFeedback($user_id,$evaluation,$engaged_user_id) {
   global $DB;    

    $newengaged = new stdClass();
    $newengaged->user_id = $user_id;
    $newengaged->evaluation = $evaluation;
    $newengaged->engaged_user_id  = $engaged_user_id;
    
    return $DB->insert_record('octopus_evaluation_engaged', $newengaged);
  
}

function EstrelaValidarFR($user_id,$id_E) {
   global $DB;    
   $query =  "SELECT * FROM mdl_octopus_evaluation_engaged WHERE user_id = $user_id AND engaged_user_id = $id_E";
          
     
    $user = $DB->get_records_sql($query);
    return $user;
}

function VerificarAvalicaoFeed($id,$cmid)
{
    global $DB;    
   $query =  "SELECT * FROM  mdl_octopus_engaged_user WHERE id = $id AND cmid = $cmid ORDER BY timec DESC LIMIT 1";
   $engaged = $DB->get_records_sql($query);
   return $engaged;
    
}

// ##################### Fim  do feedback estrela #################################################


/*Inicio recomendação de material */

function octopus_fill_rec(){
    global $DB;

    $query = "SELECT * FROM {octopus}";

    $contador = $DB->get_records_sql($query);


}


function octopus_get_rec_material_by_tag($tag_id, $cmid){
    
   global $DB;    
   
  // $query =  "SELECT * FROM {octopus_recomendacao} WHERE tag_id = $tag_id";
   
   $query = "SELECT DISTINCT (r.id), r.name_rec, r.fonte_rec, r.link_rec, r.type_rec, r.tag_id, t.name_tag, (
                SELECT ROUND (SUM( rate ) / count( id_rate ), 2)
                FROM mdl_octopus_recom_rate
                WHERE rec_id = r.id
                ) AS media
            FROM mdl_octopus_recomendacao r, mdl_octopus_tag t where r.tag_id = $tag_id and r.tag_id = t.id and r.cmid = $cmid";
   
   
   $materiais = $DB->get_records_sql($query);
   
   return $materiais;
   
   /*
    * SELECT *
FROM mdl_octopus_recomendacao r
INNER JOIN mdl_octopus_recom_rate rrate
WHERE tag_id =1
AND rrate.rec_id = r.id
LIMIT 0 , 30
    * 
    * 
    * 
    * 
    * 
    * SELECT SUM( rate )
FROM `mdl_octopus_recom_rate`
WHERE rec_id =6
    * 
    */
   
    
}


function listaRec($cmid){
    
    global $DB;    
    
    $query = "SELECT r.id, r.name_rec, r.fonte_rec, r.link_rec, r.type_rec, r.tag_id, t.name_tag "
            . "FROM {octopus_recomendacao} r, {octopus_tag} t where t.id = r.tag_id and r.cmid = $cmid";
    
    $result = $DB->get_records_sql($query);
    
      
    return $result;
}

function updateMaterial($id, $nome, $fonte, $link, $tipo, $tag, $cmid){
    
    global $DB;
   
    $rec = new stdClass();
    $rec->id = $id;
    $rec->name_rec= $nome;
    $rec->fonte_rec = $fonte;
    $rec->link_rec = $link;
    $rec->type_rec = $tipo;
    $rec->tag_id = $tag;
    $rec->cmid = $cmid;
    
    
    $DB->update_record('octopus_recomendacao', $rec);
    
    return 1;
    
}

function deleteMaterial($id) {
    global $DB;

    $DB->delete_records('octopus_recomendacao', array('id' => $id));

    return true;
}


function octopus_get_tags_post($cmid, $post_id){
    
   global $DB;    
   
   $query =  "  SELECT *
                FROM mdl_octopus_post p
                JOIN mdl_octopus_post_has_tag ht ON p.id = ht.post_id
                JOIN mdl_octopus_tag t ON ht.tag_id = t.id
                WHERE t.cmid = ".$cmid." AND p.id =".$post_id;
   
   $tags = $DB->get_records_sql($query);
   
   return $tags;
    
}

//Select da página de tags

function octopus_get_posts_from_tag($cmid, $maximo, $inicio, $tag_id) {
    global $DB;
    

    $query = "SELECT p.id, p.message, th.title, th.cmid, p.timecreated, p.user_id, p.type_message, p.thread_id, p.is_head, tg.name_tag
            FROM mdl_octopus_post p
            INNER JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
            LEFT JOIN mdl_octopus_thread th ON th.id = p.thread_id
            LEFT JOIN mdl_octopus_tag tg ON tg.id = pht.tag_id
            WHERE tg.cmid = $cmid
            AND p.type_message !=3
            AND tg.id = $tag_id
            LIMIT $inicio , $maximo";
    
    


    //$threads = $DB->get_records('octopus_thread', array('cmid' => $cmid));
    $threads = $DB->get_records_sql($query);
    
    

    foreach ($threads as $thread) {
        $head = octopus_get_thread_head($thread->id);
        $user = octopus_get_user($thread->user_id);
        $posts = octopus_get_num_posts($thread->id);
      
        $thread->user = $user->firstname . ' ' . $user->lastname;
        $thread->user_id = $user->id;
        $thread->timecreated = $head->timecreated;
        $thread->type = $head->type_message;
        $thread->likes = 0;
        $thread->dislikes = 0;
        $thread->posts = $posts;
        $thread->tags = null;
    }

    if(isset($start) && isset($end)) {
        $arr = array();
        foreach($threads as $thread)
            if($thread->timecreated > $start && $thread->timecreated < $end)
                $arr[] = $thread;
        $threads = $arr;
    }

    //return array_reverse($threads);
    return $threads;
}


//Select total da página de tags
function octopus_get_posts_from_tag_total($cmid, $tag_id) {
    global $DB;
    
    

    $query = "SELECT p.id, p.message, th.title, th.cmid, p.timecreated, p.user_id, p.type_message, p.thread_id, p.is_head, tg.name_tag
            FROM mdl_octopus_post p
            INNER JOIN mdl_octopus_post_has_tag pht ON pht.post_id = p.id
            LEFT JOIN mdl_octopus_thread th ON th.id = p.thread_id
            LEFT JOIN mdl_octopus_tag tg ON tg.id = pht.tag_id
            WHERE tg.cmid = $cmid AND p.type_message !=3 AND tg.id = ".$tag_id;
    
    


    //$threads = $DB->get_records('octopus_thread', array('cmid' => $cmid));
    $threads = $DB->get_records_sql($query);
    
    

    foreach ($threads as $thread) {
        $head = octopus_get_thread_head($thread->id);
        $user = octopus_get_user($head->user_id);
       // $likes = octopus_get_thread_likes($thread->id, 1);
       // $dislikes = octopus_get_thread_likes($thread->id, 0);
        $posts = octopus_get_num_posts($thread->id);
      //  $tag = octopus_get_thread_tags($thread->id);

        $thread->user = $user->firstname . ' ' . $user->lastname;
        $thread->user_id = $user->id;
        $thread->timecreated = $head->timecreated;
        $thread->type = $head->type_message;
        $thread->likes = 0;
        $thread->dislikes = 0;
        $thread->posts = $posts;
        $thread->tags = null;
    }

    if(isset($start) && isset($end)) {
        $arr = array();
        foreach($threads as $thread)
            if($thread->timecreated > $start && $thread->timecreated < $end)
                $arr[] = $thread;
        $threads = $arr;
    }

    //return array_reverse($threads);
    return $threads;
}

//Inserir avaliação de material por usuário

function insert_rec_rate($cmid, $user_id, $rate, $rec_id){
    
    global $DB;
   
    $rec_rate = new stdClass();
    $rec_rate->cmid = $cmid;
    $rec_rate->user_id= $user_id;
    $rec_rate->rate = $rate;
    $rec_rate->rec_id = $rec_id;
    
    $DB->insert_record('octopus_recom_rate', $rec_rate);
    
    return 1;
    
    
}

//Pegar valor da avaliação de um material por um usuário 

function get_rec_rate($rec_id, $user_id, $cmid){
    
    global $DB;
    
    $query = "SELECT rate FROM {octopus_recom_rate} WHERE rec_id = $rec_id AND user_id = $user_id AND cmid = $cmid ";
    
    $rate = $DB->get_records_sql($query);
   
    $m = 0;
    
    foreach ($rate as $r){
            $m = $r->rate;
    }
   
    return $m;
    
    
}