<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once (dirname(__FILE__) . '/lib.php');


$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
$regiao = isset($_POST['regiao']) ? $_POST['regiao'] : null;
$estado = isset($_POST['estado']) ? $_POST['estado'] : null;
$cidade = isset($_POST['cidade']) ? $_POST['cidade'] : null;
$contato = isset($_POST['contato']) ? $_POST['contato'] : null;
$tag = isset($_POST['tag']) ? $_POST['tag'] : null;
$curtida = isset($_POST['curtida']) ? $_POST['curtida'] : null;
$type_message = isset($_POST['type_message']) ? $_POST['type_message'] : null;
$categoria_profissional = isset($_POST['categoria_profissional']) ? $_POST['categoria_profissional'] : null;
$escolher_opcao = isset($_POST['escolha']) ? $_POST['escolha'] : null;
$data = isset($_POST['data']) ? $_POST['data'] : null;
$data_fim = isset($_POST['data_fim']) ? $_POST['data_fim'] : null;
$cmid = $_POST['cmid'];

if($data != 0){
        $a = strptime($data, '%d/%m/%Y');
        $timestamp = mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
        $data = $timestamp;
    }


    if($data_fim != 0){

        $a = strptime($data_fim, '%d/%m/%Y');
        $timestamp = mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
        $data_fim = $timestamp;
    }



$where = array();
$innerJoin = array();

    if( $contato){
        $where[] = " tr.cmid = {$cmid} AND p.user_id = {$contato}";
        }
    if( $type_message ){
        $where[] = " type_message = '{$type_message}' AND tr.cmid = {$cmid}";
        }
    if( $tag ){
        $where[] = " tag.tag_id = {$tag}";
        $innerJoin[] = " LEFT JOIN mdl_octopus_post_has_tag tag ON tag.post_id = p.id ";
        }
    if( $regiao ){
        $where[] = " r.id = '{$regiao}' AND ue.regiao = '{$regiao}' AND tr.cmid = {$cmid}";
        $innerJoin[] = "LEFT JOIN quest_user_extra ue ON ue.user_id = p.user_id
                        LEFT JOIN quest_regiao r ON r.id = ue.regiao";
    }
    if( $estado ){
        $where[] = " e.id = '{$estado}' AND ue1.estado = '{$estado}' AND tr.cmid = {$cmid}";
        $innerJoin[] = "LEFT JOIN quest_user_extra ue1 ON ue1.user_id = p.user_id
                        LEFT JOIN quest_estados e ON e.id = ue1.estado";
        }
    if( $cidade ){
        $where[] = " c.id = '{$cidade}' AND uex.municipio = '{$cidade}' AND tr.cmid = {$cmid}";
        $innerJoin[] = "LEFT JOIN quest_user_extra uex ON uex.user_id = p.user_id
                        LEFT JOIN quest_cidades c ON c.id = uex.municipio";
    }
    if( $curtida != 2){
        $where[] = " tr.cmid = {$cmid} AND l.type = {$curtida} AND p.is_head = 1";
        $innerJoin[] = " LEFT JOIN mdl_octopus_like l ON l.post_id = p.id ";
        }

    if($data != 0 && $data_fim != 0){

        $where[] = " tr.cmid = {$cmid} AND p.timecreated BETWEEN {$data} AND {$data_fim} ";

    } else{

         if($data != 0 && $data_fim == 0){
                $where[] = " tr.cmid = {$cmid} AND p.timecreated >= {$data} ";
        }

         if($data == 0 && $data_fim != 0){
               $where[] = " tr.cmid = {$cmid} AND p.timecreated <= {$data_fim} ";
        }

    }




$sql = " WHERE tr.cmid = ".$cmid." AND ".implode( " AND ",$where );

$join = implode( ' ',$innerJoin );

global $DB;
$query = "  SELECT tr.*
            FROM mdl_octopus_thread tr
            JOIN mdl_octopus_post p ON p.thread_id = tr.id AND p.is_head = 1            
            INNER JOIN mdl_user u ON u.id = p.user_id
            {$join}{$sql} GROUP BY p.thread_id ";



//print_r($query);
$threads = $DB->get_records_sql($query);
//print_r($threads);


foreach($threads as $thread) {
    $head = octopus_get_thread_head($thread->id);
    $user = octopus_get_user($head->user_id);
    $likes = octopus_get_thread_likes($thread->id, 1);
    $dislikes = octopus_get_thread_likes($thread->id, 0);
    $posts = octopus_get_num_posts($thread->id);
    $tag = octopus_get_thread_tags($thread->id);

    $thread->user = $user->firstname . ' ' . $user->lastname;
    $thread->user_id= $user->id;
    //adicionei essa campo, para ser passado, esse é o id do usuário no post ou só do usuário?
    $thread->timecreated = $head->timecreated;
    $thread->type = $head->type_message;
    $thread->likes = $likes;
    $thread->dislikes = $dislikes;
    $thread->posts = $posts;
    $thread->tags = $tag;
}

$t = array_shift($threads);
array_unshift($threads, $t);

//print_r($t);

$obj = new stdClass();
$obj->status = '200';
$obj->message = 'Threads returned.';
$obj->data = $threads;

//print_r($obj);
echo json_encode($obj);

?>
