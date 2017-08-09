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
 * Prints a particular instance of octopus
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_octopus
 * @copyright  2015 UNASUS UFPE/ SABER
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Replace octopus with the name of your module and remove this line.

require_once (dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once (dirname(__FILE__) . '/lib.php');

ini_set("display_errors", 'on');

$id = optional_param('id', 0, PARAM_INT);
// Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);
// ... octopus instance ID - it should be named as the first character of the module.

$cmid = $id;

if ($id) {
    $cm = get_coursemodule_from_id('octopus', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $octopus = $DB->get_record('octopus', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $octopus = $DB->get_record('octopus', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $octopus->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('octopus', $octopus->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cmid);
$course_id = $cm->course;


// Print the page header.
$PAGE->set_url('/mod/octopus/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($octopus->name));
$PAGE->set_heading(format_string($course->fullname));



if (!has_capability('mod/octopus:createthreads', $context)) {
    echo get_string('sempermissaotopico', 'octopus');
} else {
    if (isset($_POST['thread'])) {
        $error_flag = false;
        // Caso seja questao disparadora e o user nao seja admin, da erro
        if ($_POST['message_type'] == 3 && !isset($admins[$USER->id]))
            $error_flag = true;

        if (!$error_flag) {
            $title = $_POST['thread'];
            $cmid = $id;

            $tid = octopus_new_thread($cmid, $title);

            $message = utf8_decode($_POST['editor1']);
            $user_id = $USER->id;
            $type = $_POST['message_type'];
            $thread_id = $tid;

            $pid = octopus_new_post($message, $user_id, $type, $thread_id, 1);

            $tags = $_POST['tags'];
            if (sizeof($tags) > 0) {
                $tags = explode(',', $tags);
                octopus_add_post_tag($pid, $tags);
            }
            octopus_redirect('view.php?id=' . $cmid);
            die("Cadastrado com sucesso.");
        } else
            die("Usuario nao é adm.");
    }

// Output starts here.
    echo $OUTPUT->header();


    $user_id = $USER->id;
    $cmid = $id;
    $threads_user = octopus_get_threads_by_user($user_id, $cmid);
    $tags = octopus_get_tags($cmid);
    octopus_set_profile($user_id,$cmid);
    octopus_set_activities($cmid);


// Conditions to show the intro can change to look for own settings or whatever.
    if ($octopus->intro) {
        //Ocultando a descrição
        // echo $OUTPUT->box(format_module_intro('octopus', $octopus, $cm->id), 'generalbox mod_introbox', 'octopusintro');
    }

    $user_id = $USER->id;
    $cmid = $id;

    echo "<script> var cmid = $cmid; var user_id = $user_id; </script>";
    include('grade.php');

    include 'top.php';


    global $USER, $COURSE;


    $size = array('large' => 'f1', 'small' => 'f2');


    global $USER, $PAGE;
    $user_picture = new user_picture($USER);
    $src = $user_picture->get_url($PAGE);

    $all_users = octopus_get_users($cmid);
    $tags = octopus_get_tags($cmid);

    $pag = isset($_GET['pag']) ? $_GET['pag'] : '1';

    $maximo = '15'; //maximo de threads que seram axibidas
    $inicio = ($pag * $maximo) - $maximo;

    $threads = octopus_get_threads_list($id, $maximo, $inicio);


    ?>
    <link rel="stylesheet" href="css/style.css" />
    <script type="text/javascript" src="lib/jquery-2.1.4.min.js"></script>



<div class="conteudo">
    <form method="post" id="form_msg" onsubmit="createHiddenInput()">
        <h4 class="oct_titulo_pagina">
            <?php echo get_string('bemvindo', 'octopus') ?>
        </h4>


       <?php echo get_string('bemvindotexto', 'octopus') ?>

        <!-- se retorno for allowusercreatethreads = 1, todos os perfis e aluno tem permissão para criar uma thread-->
       <?php  if ( octopus_get_student_notcreatethreadsusers($cmid) == true){  ?>

       <br><br>
       <h5><?php echo get_string('novopost', 'octopus') ?></h5>
                          <?php
if(has_capability('mod/octopus:editconfigurations', $context)) { 
    //tratamento aviso notificacoes ativadas
 $notification = octopus_get_notification($cmid);
                            if($notification==true){                            
                            echo get_string('notificacao', 'octopus',$a);
                            }
}
?>
       <input type="text" name="thread" id="thread" size="50" value="" placeholder="Assunto" required/>
       <textarea style="display: none;"id="editor1" name="editor1" placeholder="Mensagem" placeholder="textos"></textarea>
       <div id="tags_container" class="oct_tags" style="float: left;"></div>
		 <div class="opcoes_editor">
			<select id="tag" name="tag" onchange="appendTag()" required>
                <option value="0">Tag</option>
                <?php
                foreach($tags as $tag) {
                    echo "<option value='$tag->id'>$tag->name_tag</option>";
                }
                ?>
            </select>
			<label><input type="radio" name="message_type" value="1" /><?php echo get_string('pergunta', 'octopus') ?></label>
			<label><input type="radio" name="message_type" value="2" /><?php echo get_string('narrativa', 'octopus') ?></label>

            <?php if (has_capability('mod/octopus:addtriggeringquestion', $context)) { ?>
            <label>
                <input type="radio" name="message_type" value="4" />
                <?php echo get_string('questaodisparadora', 'octopus') ?>
            </label>
                <?php } ?>
                <input type="hidden" name="cmid" id="cmid" value="<?php echo $id; ?>" style="display:none"/>
                <input type="submit" id="send" value="Enviar" class="oct_botao_roxo" onclick="return ValidarTag();" />

       </div>

        <?php }
            //se retorno for allowusercreatethreads = 0, APENAS aluno não tem permissão para criar uma thread
             else if(has_capability('mod/octopus:donotcreatethreads', $context) && octopus_get_student_notcreatethreadsusers($cmid) == false) { ?>

                   <br><br>
                   <h5><?php echo get_string('novopost', 'octopus') ?></h5>
                   <input type="text" name="thread" id="thread" size="50" value="" placeholder="Assunto" required/>
                   <textarea style="display: none;"id="editor1" name="editor1" placeholder="Mensagem" placeholder="textos"></textarea>
                   <div id="tags_container" class="oct_tags" style="float: left;"></div>
                     <div class="opcoes_editor">
                        <select id="tag" name="tag" onchange="appendTag()" required>
                            <option value="0">-</option>
                            <?php
                            foreach($tags as $tag) {
                                echo "<option value='$tag->id'>$tag->name_tag</option>";
                            }
                            ?>
                        </select>
                        <label><input type="radio" name="message_type" value="1" /><?php echo get_string('pergunta', 'octopus') ?></label>
                        <label><input type="radio" name="message_type" value="2" /><?php echo get_string('narrativa', 'octopus') ?></label>

                        <?php if (has_capability('mod/octopus:addtriggeringquestion', $context)) { ?>
                        <label>
                            <input type="radio" name="message_type" value="4" />
                            <?php echo get_string('questaodisparadora', 'octopus') ?>
                        </label>
                        <?php } ?>
                        <input type="hidden" name="cmid" id="cmid" value="<?php echo $id; ?>" style="display:none"/>
                        <input type="button" id="send" value="Enviar" class="oct_botao_roxo" onclick="ValidarTag();" />
                        
                   </div>

        <?php } ?>


            <div class="tags_selec"></div>
    </form>
 <div style='display: none;' id='count-words'></div>

        <br/>
        <input class="oct_btn_filtro_busca" type="submit" value="Filtrar postagens" onclick="showFilters();">

        <div class="oct_filtros">

        <?php
        include 'filtros.php';
        ?>

        </div>
        <div class="oct_top_tabela">



            <div class="oct_recentes">
                <?php echo get_string('todosposts', 'octopus') ?>
            </div>

            <?php


             if(has_capability('mod/octopus:donotcreatethreads', $context)){

             ?>
               <div class="oct_recentes meus_posts">
                <?php echo get_string('meusposts', 'octopus') ?>
               </div>
            <?php //só permite o aluno visualizar a aba 'meus posts' se o retorno da funcao for true
             }else if(octopus_get_student_notcreatethreadsusers($cmid) == true){ ?>
                <div class="oct_recentes meus_posts" style='border-left: 1px solid #808284;'>
                <?php echo get_string('meusposts', 'octopus') ?>
               </div>
            <?php } ?>
                <div class="oct_recentes posts_contacts" style='border-left: 1px solid #808284;'>
                <?php echo get_string('postscontacts', 'octopus') ?>
                </div>

            <div class="oct_breadcrumbs">
                <!-- Nordeste &lt; Pernambuco &lt; Recife &lt; Mais curtidas &lt; Câncer &lt; 26/12/2015 -->
            </div>
        </div>
   
        <div id='recents' class="oct_tabela_php">

              <table class="oct_tabela" id="oct_tabela">
                 <div id='load_oct' style='width: auto;'>
                            <br><img id='load_oct' src="pix/octopus_0.gif">
                            <span id='load_icon'> Carregando dados...</span>
                           
                    
                 </div>
              </table>   
            <center>
                <ul id="paginacao" class="paginacao_msg"></ul>
            </center>
        </div>


        <div id="response">
            <table  id='tabela_posts'  class="oct_tabela" id="oct_response">

            </table>
            <center>
                <ul id="paginacao_meus_posts" class="paginacao_msg_meus_posts"></ul>
            </center>
        </div>
        
        <div id="response_contacts">
            <table  id='tabela_posts_contacts'  class="oct_tabela" id="oct_response">

            </table>
            <center>
                <ul id="paginacao_meus_posts_contacts" class="paginacao_msg_meus_posts"></ul>
            </center>
        </div>




    </div>


    <?php
// Finish the page.
// Barra de opções e rodapé do moodle
}
echo $OUTPUT->footer();
?>
        <script type="text/javascript" src="lib/script.js"></script>
        <script src="lib/tinymce/js/tinymce/tinymce.js"></script>
        <script src="lib/script_view.js"></script> <!--scripts gerais da página view.php -->
