<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
ini_set('display_errors', 'on');

$key = optional_param('key', 0, PARAM_INT); // Course_module ID, or
/* $n  = optional_param('n', 0, PARAM_INT);  // ... octopus instance ID - it should be named as the first character of the module.

  if ($id) {
  $cm         = get_coursemodule_from_id('octopus', $id, 0, false, MUST_EXIST);
  $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
  $octopus  = $DB->get_record('octopus', array('id' => $cm->instance), '*', MUST_EXIST);
  } else if ($n) {
  $octopus  = $DB->get_record('octopus', array('id' => $n), '*', MUST_EXIST);
  $course     = $DB->get_record('course', array('id' => $octopus->course), '*', MUST_EXIST);
  $cm         = get_coursemodule_from_instance('octopus', $octopus->id, $course->id, false, MUST_EXIST);
  } else {
  error('You must specify a course_module ID or an instance ID');
  }

  require_login($course, true, $cm); */
//    echo 'key:'.$key;
if ($key == "JK7F5Y8A_!$") {

    $cmidlist = octopus_get_list_cmid();
    foreach ($cmidlist as $cm) {

        $cmid = $cm->id;
        $notification = octopus_get_notification($cmid);
        if ($notification == true) {
            $ups = octopus_get_user_preferences($cmid);
            $t = time() - (3 * 24 * 60 * 60); // 3 dias
            $now = time();

            foreach ($ups as $user) {
                $freq = $user->digest_frequency;
//                echo $user->last_digest."<------";

                if ($now - $user->last_digest >= ($freq * 23 * 60 * 60)) {
                    $r = octopus_user_digest($user->user_id, $cmid, $t, $now);
                    // echo "User ($user->user_id): $r <br>";
                }
            }
        }
    }
} else {
    echo 'chave errada!';
}



//include 'top.php';
?>
