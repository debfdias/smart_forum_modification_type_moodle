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

    if($method == "get-list") {
        $list = octopus_get_tags($cmid);
        $a = array_shift($list);
        array_unshift($list, $a);

        $obj = new stdClass();
        $obj->status = '200';
        $obj->message = 'OK';
        $obj->data = $list;

        echo json_encode($obj);
    }
    elseif($method == "get-list-rec") {
        $list = octopus_get_recs($cmid);
        $a = array_shift($list);
        array_unshift($list, $a);

        $obj = new stdClass();
        $obj->status = '200';
        $obj->message = 'OK';
        $obj->data = $list;

        echo json_encode($obj);
    }
    elseif($method == "get-tree"){
        $tree = octopus_get_tag_tree_recursive($cmid);

        $obj = new stdClass();
        $obj->status = '200';
        $obj->message = 'OK';
        $obj->data = $tree;

        echo json_encode($obj);
    }
    elseif($method == "create" && isset($_POST['tag_name']) && isset($_POST['tag_parent'])) {
        $name = $_POST['tag_name'];
        $parent = $_POST['tag_parent'];

        $r = octopus_new_tag($name, $parent, $cmid);

        $tree = octopus_get_tag_tree_recursive($cmid);

        $obj = new stdClass();
        $obj->status = '200';
        $obj->message = 'OK';
        $obj->data = $tree;

        echo json_encode($obj);
    }
    elseif($method == "createRec" && isset($_POST['rec_name']) && isset($_POST['rec_fonte'])
        && isset($_POST['rec_link']) && isset($_POST['rec_type']) && isset($_POST['tag_parent']) ) {
        
        $name  = $_POST['rec_name'];
        $fonte = $_POST['rec_fonte'];
        $link  = $_POST['rec_link'];
        $type  = $_POST['rec_type'];
        $tag_id= $_POST['tag_parent'];
        $cmid = $_POST['cmid'];

        $r = octopus_new_rec($name, $fonte, $link, $type, $tag_id, $cmid);

    }
    elseif($method == "addRec" && isset($_POST['rec_type']) ) {
        $type  = $_POST['rec_type'];

        $r = octopus_add_rec($type, $cmid);

    }
    elseif($method == "edit" && isset($_POST['tag_id']) && isset($_POST['new_name']) && isset($_POST['new_parent']) ) {
        $tag_id = $_POST['tag_id'];
        $new_name = $_POST['new_name'];
        $new_parent = $_POST['new_parent'];

        octopus_update_tag($tag_id, $new_name, $new_parent, $cmid);

        $tree = octopus_get_tag_tree_recursive($cmid);
        $obj = new stdClass();
        $obj->status = '200';
        $obj->message = 'TAG EDITED';
        $obj->data = $tree;

        echo json_encode($obj);
    }
    elseif($method == "edit-rec" && isset($_POST['tag_id']) && isset($_POST['new_name']) ) {
        $tag_id = $_POST['tag_id'];
        $new_name = $_POST['new_name'];

        octopus_update_rec($tag_id, $new_name, $cmid);

        echo json_encode($obj);
    }
    elseif($method == "delete" && isset($_POST['tag_id'])) {
        $tag_id = $_POST['tag_id'];

        octopus_delete_tag($tag_id);

        $tree = octopus_get_tag_tree_recursive($cmid);
        $obj = new stdClass();
        $obj->status = '200';
        $obj->message = 'TAG DELETED';
        $obj->data = $tree;

        echo json_encode($obj);
    }
    else{
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
