<?php

//include '../../config.php';
require_once (dirname(dirname(dirname(__FILE__))) . '/config.php');
$hostname = $CFG->dbhost;
$username = $CFG->dbuser;
$password = $CFG->dbpass;
$dbName = $CFG->dbname;
$conexao = mysql_connect("$hostname", "$username", "$password");

mysql_select_db($dbName, $conexao);


?>
