<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT);
$cmid = $id;

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

require_login($course, true, $cm);


if (isset($_POST['method'])) {
    $method = $_POST['method'];

    if($method == 'set-frequency' && isset($_POST['frequency'])) {
        $freq = $_POST['frequency'];
        octopus_set_digest_frequency($USER->id, $freq, $cmid);

        $obj = new stdClass();
        $obj->status = '200';
        $obj->message = 'Digest frequency updated';

        echo json_encode($obj);
    }
    else if($method == 'get-frequency') {
        $opt = octopus_get_digest_frequency($USER->id, $cmid);
        if($opt == false) {
            octopus_set_digest_frequency($USER->id, 1, $cmid);
            $opt = '1';
        }

        $obj = new stdClass();
        $obj->status = '200';
        $obj->message = 'Digest frequency fetched.';
        $obj->data = $opt;

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
