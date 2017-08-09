<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT);
$course_id = $_POST['course_id'];
$atividades = array();

$info = octopus_get_grade_users($course_id);

foreach($info as $a){
    $nome_atividade = $a->itemtype;
    $media_atividade = $a->media;
    $peso_atividade = $a->peso_prova;
    $id = $a->userid;

    $obj = array($nome_atividade, $media_atividade, $peso_atividade, $id);
    array_push($atividades, $obj);
}


$resultado = array();
$resultado['atividades'] = $atividades;


print json_encode($resultado);

?>
