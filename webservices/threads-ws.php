<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT);
$cmid = $id;

$user_id = $_POST['user'];



if ($id) {
	$cm = get_coursemodule_from_id('octopus', $id, 0, false, MUST_EXIST);
	$course = $DB -> get_record('course', array('id' => $cm -> course), '*', MUST_EXIST);
	$octopus = $DB -> get_record('octopus', array('id' => $cm -> instance), '*', MUST_EXIST);
} else if ($n) {
	$octopus = $DB -> get_record('octopus', array('id' => $n), '*', MUST_EXIST);
	$course = $DB -> get_record('course', array('id' => $octopus -> course), '*', MUST_EXIST);
	$cm = get_coursemodule_from_instance('octopus', $octopus -> id, $course -> id, false, MUST_EXIST);
} else {
	error('You must specify a course_module ID or an instance ID');
}

//require_login($course, true, $cm);



if (isset($_POST['method'])) {
    $method = $_POST['method'];

    if($method == 'get-recents' && isset($_POST['max']) && isset($_POST['start'])) {
        $max = $_POST['max'];
        $start  = $_POST['start'];

        $threads = octopus_get_threads_list($cmid, $max, $start);
        $number = octopus_get_threads($cmid);
        
                
        $t = array_shift($threads);
        array_unshift($threads, $t);

        $obj = new stdClass();
        $obj->status = '200';
        $obj->message = 'Threads list fetched.';
        $obj->number = count($number);
        $obj->data = $threads;

        echo json_encode($obj);
        
    }else if($method == 'contact-posts'  && isset($_POST['max']) && isset($_POST['start'])){
        
        $max = $_POST['max'];
        $start  = $_POST['start'];
        
        $threads = get_posts_from_contacts($cmid, $user_id, $max, $start);
        $number = get_posts_from_contacts_total($cmid, $user_id );
        
        
        //echo 'limite '.count($threads);
        
        
        $t = array_shift($threads);
        array_unshift($threads, $t);

        $obj = new stdClass();
        $obj->status = '200';
        $obj->message = 'Threads list fetched.';
        $obj->number = $number;
        $obj->data = $threads;

        echo json_encode($obj);
        
        
    }
    else {
        $obj = new stdClass();
        $obj->status = '404';
        $obj->message = 'Missing arguments';

        echo json_encode($obj);
    }
}
else{
    $obj = new stdClass();
    $obj->status = '404';
    $obj->message = 'Missing arguments';

    echo json_encode($obj);
}
?>
