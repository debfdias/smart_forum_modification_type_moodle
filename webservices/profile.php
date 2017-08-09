<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');

$tipo = $_POST['tipo'];
$cmid = $_POST['cmid'];
$user_id = $_POST['user_id'];
octopus_set_visible_flag($user_id, $cmid);
?>
