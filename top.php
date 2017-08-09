<?php
$page = substr(strrchr($_SERVER['REQUEST_URI'], '/'), 1);
$ip = $_SERVER['REMOTE_ADDR'];
$user_id = $USER->id;
octopus_add_log_activity($USER->id, $cmid, $page, $ip);
$qtd = octopus_get_notifications_num($USER->id,$opt = 1,$cmid);
$last_activity = octopus_get_last_activity($USER->id, $cmid);
$timestamp = $last_activity->time;
$today = strtotime(date('d-m-Y', time()));


$qtd_recent = octopus_get_recent_posts_num($cmid,$user_id);
$qtd_private_message = octopus_notify_private_message($USER->id, $cmid);
//habilitando notificacoes mail
$opt = octopus_get_digest_frequency($USER->id, $cmid);
if($opt == false) {
   octopus_set_digest_frequency($USER->id, 1, $cmid);
   //adicionando usuario ao rankin 
   $cm = get_coursemodule_from_id('octopus', $id, 0, false, MUST_EXIST);
   $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
   octopus_save_user_points($cmid, $course_id, $user_id, 0, 0, 0);
}

//inicio grades


echo "<script>var cmid = ".$cmid."; </script>";
echo "<script>var user_id = $USER->id; </script>";

$context = context_module::instance($cmid);

echo "<script> var wwwroot = '" . $CFG->wwwroot . "'; </script>";
?>
<script src="lib/jquery-2.1.4.min.js"></script>
<script src="lib/underscore-min.js"></script>



<div class="barra_superior">
	<a href="view.php?id=<?php echo $id; ?>">

		<div class="marca">
			<img src="pix/logo.svg" width="150px;" />
		</div></a>

		<form id='oct_bloco_form' method="post" action="search.php?id=<?php echo $id;?>">
			<div class="busca">
				<input type="text" name="txtbusca" id="txtbusca" placeholder="Pesquise">
				<!--<input type="image" src="pix/lupa.svg" id="buscar" value="Pesquisar"  />-->
			</div>
		</form>
	<div class="oct_menu_barras" style="float: right;" onclick="show_menu_responsivo();">
		<img src="pix/icon_menu.svg" title="Clique para acessar o menu" alt="Menu"/>
	</div>
	<div id='oct_bloco2'>
		<div class="colocacao">
                    <?php if(has_capability('mod/octopus:view', $context) && octopus_get_grade_allwranking($cmid) == true) { ?>
			<!--<a href="my_ranking.php?id=<?php echo $cmid; ?>"> <img src="pix/icons_buttons_otopus_medalha.svg" width="40px" style="float: left;"> </a>-->
                        <span style="float: left;">Colocação
				<br>
				<center class="numero">
					<?php
					$user_id = $USER -> id;
                                        $course_id = $cm->course;
                                        $position = octopus_position($cmid, $course_id, $user_id);
                                        foreach($position as $pos){
                                            echo '<center class=numero title="Sua classificação.">'.$pos->colocacao. 'º</center>';
                                         }
					?>
				</center></span>
                
                    <?php } ?>
		</div>

		<div class="online">
                    <span style="float: left;" >On-line 
                        <br>
                        <center class="numero" title="Quantidade de usuários conectados agora.">
                                <?php
                                echo octopus_online_users($cmid);
                                ?>
                        </center>
                    </span>
		</div>

	</div>
	<div class="botoes_aux">


        <div class="oct_botao">
            <a href="view.php?id=<?php echo $id; ?>"> <img id="botao_home" src="pix/icons_buttons_otopus_home.svg" alt="Início" title="Volte para tela inicial do fórum."> </a>Início
        </div>
            
        
        <?php if(has_capability('mod/octopus:editconfigurations', $context)) { ?>
        <div class="oct_botao">
			<a href="configuration.php?id=<?php echo $id; ?>">
                            <img id="botao_config" src="pix/icons_buttons_otopus_configuracao.svg" alt="Configurações" title="Configurações" style="">
                        </a><br>Configuração
	</div>
        <?php } ?>

        <?php if(has_capability('mod/octopus:viewreports', $context)) { ?>
        <div class="oct_botao">
			<a href="report.php?id=<?php echo $id; ?>">
                            <img id="botao_report" src="pix/icons_buttons_otopus_relatorio.svg" alt="Relatórios" title="Relatórios" style="">
            </a><br>Relatórios
		</div>
        <?php } ?>
            


		<div class="oct_botao">
                    <a href="recent.php?id=<?php echo $id; ?>"> <img id="botao_recent" src="pix/icons_buttons_otopus_novo_post.svg" alt="Novos" title="Veja o(s) novo(s) post(s)."> </a><br>Novos

				<?php
					if($qtd_recent > 0){
						echo '<div class="quant">'.$qtd_recent.'</div>';
					}

				?>

		</div>
   <div class="oct_botao">
                    <a href="recommendation.php?id=<?php echo $id; ?>"> <img id="botao_rec" src="pix/icones_recomendacao_menu_roxo.svg" alt="Recomendação" title="Recomendação"> </a>Recomendações
                </div>
                <?php if(has_capability('mod/octopus:view', $context) && octopus_get_grade_allwranking($cmid) == true) {
                        ?>
		<div class="oct_botao">
			
                         
			<img id="botao_ranking" style="cursor: pointer;" onclick="abreMenuLat('ranking');" src="pix/icons_buttons_otopus_ranking.svg" alt="Ranking" title="Conheça os usuários que se destacam neste fórum.">Ranking

		</div>
                 <?php } ?>
		<div class="oct_botao">

			<img id="botao_contacts" style='cursor: pointer;' onclick='abreMenuLat("contatos");' src="pix/icons_buttons_otopus_contatos.svg" alt="Contatos" title="Conheça as pessoas inscritas neste fórum.">Contatos

				<?php
					if($qtd_private_message > 0){
						echo '<div class="quant_contact">'.$qtd_private_message.'</div>';
					}

				?>

		</div>
<div class="oct_botao">

			<img id="botao_tag" style="cursor: pointer;" onclick="abreMenuLat('tags')" src="pix/icons_buttons_otopus_tags.svg" alt="Tags" title="Veja a(s) tag(s) associada(s) a este fórum.">Tags

		</div>
		<div class="oct_botao">
			<!-- <a href="notifications.php?id=<?php echo $id; ?>">  -->
				<img id="botao_notifications" style="cursor: pointer;" onclick="abreMenuLat('notificacoes');" src="pix/icons_buttons_otopus_notificacoes.svg" alt="Notificações" title="Fique por dentro das novidades.">Notificações

				<?php
					if($qtd > 0){
						echo '<div class="quant">'.$qtd.'</div>';
					}

				?>

		</div>
	</div>

</div>
        <div class="oct_menu_responsivo" style="display: none;">
                <form method="post" action="search.php?id=<?php echo $id;?>">
                    <div class="busca2" style="display: none;">
                            <input type="text" name="txtbusca" id="txtbusca" placeholder="Pesquise" style="background: #fff;">
                            <div class="oct_x" onclick="show_search();">
                                <img style='background-color: #fff;' src="pix/cancel.svg" alt="Fechar" title="Fechar" >
                            </div>
                            

                    </div>
                </form>
	

		<a href="view.php?id=<?php echo $id; ?>"> <img src="pix/icons_buttons_otopus_home.svg" alt="Início" title="Início" style=""> </a>

		<a style='position: relative; display: inline-block;' href="recent.php?id=<?php echo $id; ?>"> <img src="pix/icons_buttons_otopus_novo_post.svg" alt="Novo post" title="Novo post">
                                <?php
					if($qtd_recent > 0){
						echo '<div class="quant_resp">'.$qtd_recent.'</div>';
					}

				?></a>
               

		<img style="cursor: pointer" onclick="abreMenuLat('tags');" src="pix/icons_buttons_otopus_tags.svg" alt="Tags" title="Tags">

		<img style="cursor: pointer;" onclick="abreMenuLat('ranking');" src="pix/icons_buttons_otopus_ranking.svg" alt="Ranking" title="Ranking">

                <span style='position: relative; display: inline-block;' >
		<img style='cursor: pointer;' onclick='abreMenuLat("contatos");' src="pix/icons_buttons_otopus_contatos.svg" alt="Contatos" title="Contatos">
                                <?php
                
					if($qtd_private_message > 0){
						echo '<div class="quant_resp">'.$qtd_private_message.'</div>';
					}

				?>
                </span>


                <span style='position: relative; display: inline-block;' >
		<img style="cursor: pointer;" onclick="abreMenuLat('notificacoes');" src="pix/icons_buttons_otopus_notificacoes.svg" alt="Notificações" title="Notificações">

                
				<?php

					if($qtd > 0){
						echo '<div class="quant_resp">'.$qtd.'</div>';
					}

                                ?></span>

		<img src="pix/lupa.svg" onclick="show_search();" style="cursor: pointer;width: 25px;height: 25px;padding: 4px;">
	
</div>



<?php
if (isset($_POST['buscarContatos'])) {
	$search = $_POST['buscarContatos'];
	$cmid = $_GET['id'];
	$search_contact = octopus_search_contacts($search, $cmid);
	echo "<div class='conteudo'><b>Resultados encontrados:</b><br><br><table class='oct_tabela' width='100%'>";
	foreach ($search_contact as $contact) {
		// echo "<div class='resultado_pesquisa'>" . $serach -> message . "</div>";
		// echo $serach -> message;
		echo "<tr>
<td><a href='thread.php?thread_id=" . $contact -> thread_id . " &id=" . $cmid . "'>" . $contact -> message . "</a></td>
<td>" . $contact -> title . "</td>
<td>" . $contact -> name_tag . "</td>
</tr>";
	}
	echo "</table></div>";
}
?>

<div class='cortina' id="cortina_contatos">
    <div id="titulo_contatos">
        <div onclick='abreMenuLat("contatos");' id="fechar_conta">
             <img src="pix/cancel.svg" alt="Fechar" title="Fechar" >
        </div>
        <img id='icon_contatos2'  src="pix/icons_buttons_otopus_contatos.svg" />Meus contatos
    </div><br>

    <div id='lista'>
        <?php
            include 'contacts.php';
        ?>
    </div>
    <div id='chat'></div>
</div>
<div class='cortina' id="cortina_tags">
	<div id="titulo_contatos">
		<div onclick='abreMenuLat("tags");' id="fechar_conta">
			 <img src="pix/cancel.svg" alt="Fechar" title="Fechar" >
		</div>
		<img id='icon_contatos'  src="pix/icons_buttons_otopus_tags.svg" /> Tags disponíveis
	</div>
	<?php
		include 'tag.php';
	?>
</div>
<div class='cortina' id="cortina_ranking">
	<div id="titulo_contatos">
		<div onclick='abreMenuLat("ranking");' id="fechar_conta">
			 <img src="pix/cancel.svg" alt="Fechar" title="Fechar" >
		</div>
		<img id='icon_contatos' src="pix/icons_buttons_otopus_ranking.svg" /> Ranking
	</div>
	<br>
        <?php
                include 'ranking.php';
        ?>
</div>
<div class='cortina' id="cortina_notificacoes">
	<div id="titulo_contatos">
		<div onclick="abreMenuLat('notificacoes');" id="fechar_conta">
			 <img src="pix/cancel.svg" alt="Fechar" title="Fechar" >
		</div>
		<img id="icon_contatos" src="pix/icons_buttons_otopus_notificacoes.svg" /> Notificações
	</div>
        <?php
                  include 'notifications.php';
        ?>
</div>
<div class='cortina' id="cortina_relatorio">
    <div id="titulo_contatos">
        <div onclick='abreMenuLat("relatorio");' id="fechar_conta">
             <img src="pix/cancel.svg" alt="Fechar" title="Fechar" >
        </div>
        <img id='icon_contatos2'  src="pix/icons_buttons_otopus_contatos.svg" />Usuários
    </div><br>

    <div id='lista'>
        <?php
            include 'report_users.php';
        ?>
    </div>
    <div id='chat'></div>
</div>

<script src="lib/script_top.js"></script>
