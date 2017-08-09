/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//funcinalidade para visualizar filtros da views
function showFilters() {



    $(".oct_filtros").slideToggle(1000);

//    $(".oct_btn_form").show();


}

function showFormPost() {



    $(".oct_filtros").hide(1000);
    $("#form_msg").show(1000);
    $(".oct_btn_form").hide();
    $(".oct_btn_filtro_busca").show();


}

 
  



