/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    list_materiais();

    /*Listagem de materiais na página de configuração */
    
    function list_materiais(){
    
        $.post("webservices/getMateriais.php", {cmid: cmid})
               .done(function( data ) {

                   data = JSON.parse(data);

                      (function(){
                           _.templateSettings = {interpolate : /\{\{(.+?)\}\}/g,      // print value: {{ value_name }}
                                   evaluate    : /\{%([\s\S]+?)%\}/g,   // excute code: {% code_to_execute %}
                                   escape      : /\{%-([\s\S]+?)%\}/g}; // excape HTML: {%- <script> %} prints <script>  

                           var modelo = _.template($("#tab_materiais").html());
                           var conteudo = modelo(data);
                           $("#cont_tab_materiais").html(conteudo);

                       })();



       }); 
    
    }
    
   
    
    function createRec() {
        var newName  = document.getElementById("recName").value;
        var newFonte = document.getElementById("recFonte").value;
        var newLink  = document.getElementById("recLink").value;
        var newType  = document.getElementById("recType").value;
       

        var parentSelect = document.getElementById("parentSelect2");
        var newParent = parentSelect.options[parentSelect.selectedIndex].value;

        if(newName.length > 2) {
            $.post("webservices/tag-ws.php?id="+cmid, { method: "createRec", rec_name: newName,
                rec_fonte: newFonte, rec_link: newLink, rec_type: newType, tag_parent: newParent, cmid: cmid })
                .done(function(data) {
                    alert("Recomendação criada!");
                    document.getElementById("recName").value = '';
                    document.getElementById("recFonte").value = '';
                    document.getElementById("recLink").value = '';
                    document.getElementById("recType").value = '';
                    parentSelect.selectedIndex = 0;
                    list_materiais();
            });
        }else{
            alert("Problema na criação! O nome deve ter no mínimo 3 caracteres.");
        }
    }
    

    function addRec() {
        var newType  = document.getElementById("type_rec").value;


        if(newType.length > 2) {
            $.post("webservices/tag-ws.php?id="+cmid, { method: "addRec", rec_type: newType })
                .done(function(data) {
                    alert("<?php echo 'Tipo criado!' ?>");
                    document.getElementById("type_rec").value = '';
            });
        }
        else
            alert("Problema na criação!");
    }
    
    
    /* Editar material */
    function edit_material(id, tag_id){
        
        var index = parseInt(id) + 3;
        
        $('#list_mat_'+ id).hide();
        $('#edit_mat_' + id).show();
        $("#recTypeEdit" + id).focus();    
               
        $.when($.ajax(loadTagList(index))).then(function () {

            $('#parentSelect'+index).val(tag_id);
            
           

        });
        
      
    }
    
    /* Cancelar edição de material */
    function cancel_edit_material(id){
        
        $('#list_mat_'+ id).show();
        $('#edit_mat_' + id).hide();
    }
    
    
    /* Salvar edição de material */
    function save_material(id){
        
        var index = parseInt(id) + 3;
        
        var name_mat = $('#edit_mat_'+ id + ' input[name=name]').val();
        var font_mat = $('#edit_mat_'+ id + ' input[name=font]').val();
        var link_mat = $('#edit_mat_'+ id + ' input[name=link]').val();
        var type_mat = $('#recTypeEdit'+ id).val();
        var tag_id = $('#parentSelect'+ index).val();
        
        $.post("webservices/updateMateriais.php", {id: id, nome: name_mat, fonte: font_mat, link: link_mat, tipo: type_mat, tag: tag_id, cmid: cmid}).done(function( data ) {

                    alert('Recomendação atualizada com sucesso!');
                    list_materiais();

        }); 
    
      
    }
    
    /*Deletar material */
    function del_material(id){
        
        var txt;
        var r = confirm("Tem certeza que deseja excluir a recomendação deste material?");
        
        if (r === true) {
            
             $.post("webservices/delMateriais.php", {id: id}).done(function( data ) {

                    alert('Recomendação excluída com sucesso!');
                    list_materiais();

            }); 
            
        } 
    
    }
    

    
    /* Montagem e listagem da árvore de tags */
    var tree;
    

    $('#expandList')
        .unbind('click').click( function() {
            $('#listContainer .collapsed').addClass('expanded');
            $('#listContainer .collapsed').children().show('medium');
    });
    
    $('#collapseList').unbind('click').click( function() {
            $('#listContainer .collapsed').removeClass('expanded');
            $('#listContainer .collapsed').children().hide('medium');
    });

    function loadTagList(id) {
                
        $.post("webservices/tag-ws.php?id="+cmid, { method: "get-list" })
            .done(function(data) {
                
                var list = JSON.parse(data).data;
               // alert(id);
                
                var select = document.getElementById("parentSelect"+id);
                select.innerHTML = '';
                var nullOption = document.createElement('option');
                nullOption.value = 0;
                nullOption.innerHTML = "Sem pai";
                select.appendChild(nullOption);

                for(var i = 0; i < list.length; i++) {
                    var option = document.createElement('option');
                    option.value = list[i].id;
                    option.innerHTML = list[i].name_tag;
                    select.appendChild(option);
                }
                

               
        });
    }

    function loadTree(tree) {
        var builtTree = buildTree(tree);
        document.getElementById('listContainer').innerHTML = '';
        document.getElementById('listContainer').appendChild(builtTree);
        document.getElementById('listContainer').firstChild.id = 'expList';
        prepareList();
        $('#listContainer .collapsed').addClass('expanded');
        $('#listContainer .collapsed').children().show('medium');
    }



    function editTag(event) {
        $.post("webservices/tag-ws.php?id="+cmid, { method: "get-list" })
            .done(function( data ) {
                var tagList = JSON.parse(data).data;
                var id = event.target.id;

                // Pegando o parent e os filhos
                var parent = event.target.parentElement;
                
               
                
                var tagId = parent.id.replace("tag-", "");
                var clone = parent.cloneNode(true);
                var child = clone.childNodes;
                var tagName = child[0].nodeValue;
                var delButton = child[1];
                var editButton = child[2];
                var subTags = child[3];
                 console.log('teste2 '+ tagId);
                

                // Criando os elementos de input, select e submit
                var input = document.createElement('input');
                input.name = input.id = "editField-" + tagId;
                input.type = "text";
                input.value = child[0].nodeValue;


                var select = document.createElement('select');
                select.id = "select-" + tagId;
                var option = document.createElement('option');
                option.value = 0;
                option.innerHTML = 'Sem pai';
                select.appendChild(option);
                var index = 0;
                var paiId = 0;
               
                    
                for(var i = 0; i < tagList.length; i++) {
                     if(tagList[i].id == tagId){
                         paiId = tagList[i].parent_tag; 
                     }
                }
                
                for(var i = 0; i < tagList.length; i++) {
                       
                    if(tagList[i].name_tag != tagName) {
                        var option = document.createElement('option');
                        option.value = tagList[i].id;
                        option.innerHTML = tagList[i].name_tag;
                        
                       
                        
                        if(tagList[i].id === paiId){
                          
                            
                        }
                       
                        select.appendChild(option);
                    }
                }
                
                
               

                var button = document.createElement('button');
                button.id = "submit-" + tagId;
                button.innerHTML = "OK";
                button.onclick = function (event) {
                    var newName = document.getElementById("editField-" + event.target.id.replace("submit-", "")).value;
                    var parentSelect = document.getElementById("select-" + tagId);
                    var newParent = parentSelect.options[parentSelect.selectedIndex].value;
                    console.log("Name: " + newName + " New parent: " + newParent);
                    $.post("webservices/tag-ws.php?id=" + cmid, { method: "edit", tag_id: tagId, new_name: newName, new_parent: newParent })
                        .done(function (data) {
                            var tree = JSON.parse(data).data;
                            loadTree(tree);
                    });
                };
                
                // Adicionando os elementos criados
                parent.innerHTML = '';
                parent.appendChild(input);
                parent.appendChild(select);
                parent.appendChild(button);
                if(subTags)
                    parent.appendChild(subTags);
                
               
               
                $('#' +select.id + ' option[value=' + paiId +']').attr('selected','selected');
             
               
               
             
            });
    }
    
    
    function loadType(type, id){
        
        $("#recTypeEdit" + id).val(type);
               
    }

    function deleteTag(event) {
        
        var txt;
        var r = confirm("Tem certeza que deseja excluir essa tag?");
        if (r == true) {
            var id = event.target.id.replace("delete-", "");

        $.post("webservices/tag-ws.php?id="+cmid, { method: "delete", tag_id: id })
            .done(function( data ) {
                data = JSON.parse(data);
                if (data.status == '200') {
                    var tree = data.data;
                    loadTree(tree);
                }
            });
        } 
        
       
    }
    
    function blockDeleteTag(){
        alert('Essa tag foi usada em postagens. Não é mais possível excluí-la.');
    }

    function prepareList() {
      $('#expList').find('li:has(ul)')
        .click( function(event) {
            if (this == event.target) {
                $(this).children('ul').toggleClass('expanded');
                $(this).children('ul').toggle('medium');
            }
            return false;
        }).children('ul')
        .addClass('collapsed')
        .children('ul').hide();
      };

    function buildTree(nodes) {
        var ul = document.createElement('ul');

        for(var i = 0; i < nodes.length; i++) {
            var node = nodes[i];
            var li = document.createElement('li');
            var del = document.createElement('span');
            var edit = document.createElement('span');
            var quant = 0;
            
             
            li.id = "tag-" + node.id;
            li.innerHTML = node.name_tag;
            

            $.ajax({
                type: 'POST',
                url: "webservices/check_has_post_with_tag.php",
                data : { tag : node.id },
                async:false,
                success: function (data) {
                quant =  parseInt(data);
              
              }
            });
              
                
           
            del.id = "delete-" + node.id;            
            del.className="btn";
            if(quant === 0){
                del.onclick = deleteTag;
                del.innerHTML = "<b id=" + del.id + " title='Excluir'>x</b>";
                del.title = 'Excluir';
            }else{
                del.onclick = blockDeleteTag;
                del.innerHTML = "<b id=" + del.id + " title='Não é possível excluir'>x</b>";
                del.title = 'Não é possível excluir';
            }

            


            edit.id = "edit-" + node.id;
            edit.className="btn"
            edit.onclick = editTag;
            edit.innerHTML = "Editar";

         
            li.appendChild(del);

            li.appendChild(edit);

            if(node.children.length > 0) {
                li.appendChild(buildTree(node.children));
            }


            ul.appendChild(li);

            
           
        }
        return ul;
    };

    $('document').ready(function() {
        
        $.post("webservices/tag-ws.php?id="+cmid, { method: "get-tree" })
            .done(function( data ) {
                
                data = JSON.parse(data);
                if (data.status == '200') {
                    tree = data.data;
                    loadTree(tree);
                }
            });
            
            
       
            
            
    });
