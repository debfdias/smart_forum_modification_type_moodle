<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once("connection.php");

$userid = $USER->id;
$id = $_POST['idcourse'];


if(isset($_POST['thread'])) {
	$thread = new stdClass();

	$thread->title = $_POST['thread'];
	$thread->course_id = $id;

	$tid = octopus_new_thread($thread);

    $newpost = new stdClass();
    $newpost->message = $_POST['message'];
    $newpost->timecreated = time();
    $newpost->user_id = $USER->id;
    $newpost->type_message = $_POST['message_type'];
    $newpost->thread_id = $tid;

    octopus_new_post($newpost);

	header('Location: view.php?id='.$id);
}

else if (isset($_POST['view_message'])) {

	$query = mysql_query("SELECT * FROM mdl_octopus_post");
	echo "Mensagens Postadas <br><br>";

	userGrades($userid);
	echo "<br>";
	while ($row = mysql_fetch_array($query)) {
		echo $row['message'] ." ". '<a href="functions.php?idpost='.$row['id'].' ">curtir</a>';
		echo "<br><br>";
	}

}elseif($_GET['idpost'] != ""){

	$sql = mysql_query("INSERT INTO mdl_octopus_likes (post_id, user_id, timecreated) VALUES (".$_GET['idpost'].", $userid, UNIX_TIMESTAMP(NOW())) ") or die();

	if ($sql) {
		echo "Mensagem curtida com sucesso!";
	}else{
		echo "Não foi possivel cadastar :(";
	}
}
elseif ($_POST['message'] != "") {

	$sql_threads = mysql_query("INSERT INTO mdl_octopus_thread (title, course_id)
		VALUES ('".$_POST['message']."', ".$_POST['idcourse'].") ") or die();

		$current_idthread = mysql_insert_id();

	$sql_posts = mysql_query("INSERT INTO mdl_octopus_post (message,timecreated,user_id,type_message,thread_id)
		VALUES ('".$_POST['message']."', UNIX_TIMESTAMP(NOW()), $userid, 1, $current_idthread) ") or die();

	if ($sql_threads && $sql_posts) {
		header('Location: view.php?id='.$id.' ');
		echo "Cadastrado com sucesso!";
	}else{
		echo "Não foi possivel cadastar :(";
	}
}
else{
	echo "campos vazios!";
}


function userGrades($userid){

	$query_message = mysql_query("SELECT id, message, user_id, timecreated FROM mdl_octopus_post WHERE user_id = $userid ") or die();
		while ($row = mysql_fetch_array($query_message)) {
			$idmessage = $row['id'];

			$query_count = mysql_query("SELECT sum(l.post_id * 0.5) as pontuacao, l.user_id FROM mdl_octopus_likes l
				INNER JOIN mdl_octopus_post p ON p.id = l.post_id
				WHERE p.user_id = $userid AND l.post_id = $idmessage") or die();

				while ($row = mysql_fetch_array($query_count)) {
					echo "pontuacao: " .$row['pontuacao'];
				}
		}
}

?>
