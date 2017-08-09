<?php
require_once (dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once (dirname(__FILE__) . '/lib.php');

    $id = optional_param('id', 0, PARAM_INT);
    // Course_module ID, or
    $n = optional_param('n', 0, PARAM_INT);
    // ... octopus instance ID - it should be named as the first character of the module.
    $admins = get_admins();

    $cmid = $id;
    $user_id = $USER->id;

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
    $context = context_module::instance($cmid);

    // Print the page header.
    $PAGE -> set_url('/mod/octopus/view.php', array('id' => $cm -> id));
    $PAGE -> set_title(format_string($octopus -> name));
    $PAGE -> set_heading(format_string($course -> fullname));

    // Output starts here.
    echo $OUTPUT->header();

    echo "<script>var cmid = $cmid;</script>";

    include 'top.php';

if (isset($_POST['txtbusca']) || $_GET['search'] != '') {
                
               
                $offset = 10;
                
                
                $pag = isset($_GET['pag']) ? $_GET['pag'] : '1';

                $intervalIni = ($pag * $offset) - $offset;
                $intervalFim = $offset;

                $inicio = ($pag * $offset) - $offset;
               
                             
                
                if(isset($_POST['txtbusca'])){
                    $searchText = $_POST['txtbusca'];
                }else{
                     $searchText = $_GET['search'];
                }
                
                
                $cmid = $_GET['id'];
                
                $searchTotal = octopus_search($searchText, $cmid);
                $total = count($searchTotal);
                
                $num_pag = ceil($total/$offset);
                
                $searchs = octopus_search_limited($searchText, $cmid, $inicio, $offset);
                $quntidade = count($searchs);
                
                $result = '';
                
                if($total == 1){
                    $result = 'resultado';
                }else{
                    $result = 'resultados';
                }

                echo "<div class='conteudo'>Encontramos <b>".$total."</b> ".$result." para a sua pesquisa sobre <span class='b_roxo'>".$searchText."</span>. Clique
                    <a href='view.php?id=$id'>aqui</a> para voltar aos posts recentes.<br><br><br>
			<table class='oct_tabela' id='oct_tabela'>";


        

	foreach ($searchs as $search) {
            
                //print_r($search);
            
		$num_likes = octopus_get_thread_likes($search->thread_id, 1);
                $dislikes = octopus_get_thread_likes($search->thread_id, 0);
		$num_posts = octopus_get_num_posts($search->thread_id);
                $tagsPost = octopus_get_tags_post($cmid, $search->id);
                
                $hierarquia = $search-> is_head;
                
                if($hierarquia == 1){
                    $link='location.href = "thread.php?id='.$cmid.'&thread_id='.$search->thread_id.'";';
                }else{
                    $link='location.href = "thread.php?id='.$cmid.'&thread_id='.$search->thread_id.'&post='.$search->id.'";';
                }
                
		

                $pic = $CFG->wwwroot;
                //cor p questao disparadora
                if($search->type_message==4){$questao= "oct_questao_disparadora";}else{$questao="";} 
		echo "<tr  class='oct_selecao_thread ".$questao."' style='cursor: pointer;' onclick='".$link."'>
			<td style='width: 100%;'>
                        <img class='img_usuario_inicial' src=".$pic."/user/pix.php/".$search->user_id."/f1.jpg'/>
			<div class='oct_data_public'>";
				$datestring = '%d/%m/%Y';
		        $data_hora = userdate($search->timecreated, $datestring);
		        echo "No dia ".$data_hora. " às ";
		        $datestring2 = '%H:%M';
		        $data_hora2 = userdate($search->timecreated, $datestring2);
		        echo $data_hora2;
		        echo " por ".$search-> firstname;
		        $search_msg = str_replace("<p>","",$search-> message);
		       	$search_msg = str_replace("</p>"," ",$search_msg);
		       	$search_msg = str_replace("<br />","",$search_msg);
		       	$leng1 = strlen($search_msg);
                    //tratamento p qtd de likes/dislike/comentarios p filho                               
 
                    if($search->is_head==0){                     

                    $num_likes = octopus_get_post_likes_num($search->id);
                    $dislikes = octopus_get_post_dislikes_num($search->id);
                    $num_posts=0;
                    
                    }
		        $search_msg = substr($search_msg,0, 100);
				$leng2 = strlen($search_msg);
				

				if($leng2 < $leng1){
					 $search_msg = $search_msg."...";
				}

				if($hierarquia == 0){
					$hierarquia = '<strong>Re: </strong>';
				}else{
					$hierarquia = '';
				}
                                
				echo '</div><div class="oct_titulo_thread">' .$hierarquia.$search -> title . '</div><div class="search_msg">'.$search_msg.'</div> <div class="oct_tags">';
                                        
                                foreach ($tagsPost as $tag){
                                    
                                    echo '<span>'.$tag->name_tag.'</span>';
                                }
                                
                                
                                
                                echo '</div> <div class="oct_responsavel_public"></div></td>';
                   
    
                
                echo "<td style='width: 70px;' align='center'><div class='oct_numero'>".$dislikes." </div><div style='font-size: 10px;'>Não curtida(s)</div></td>
                <td align='center'><div class='oct_numero'>".$num_likes."</div><div style='font-size: 10px;'>Curtida(s)</div></td>
                <td align='center'><div class='oct_numero'>".$num_posts."</div><div style='font-size: 10px;'>Comentário(s)</div></td>

                </tr>";
	}

	echo "</table></div>";
        
        if($total > 0){

            echo "<div id='pagination1'>";
                buildPagination($num_pag, "search.php?".$_SERVER['QUERY_STRING'], $pag, $searchText);
            echo "</div>";
        }
}
?>
<script src="lib/jquery-2.1.4.min.js"></script>
<script>$('.search_msg img').hide();</script>

<?php
    echo $OUTPUT -> footer();
    
    
?>
