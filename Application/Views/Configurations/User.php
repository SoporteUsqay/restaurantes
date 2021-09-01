<?php include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <div class="container">
        <!--<div class="jumbotron">-->

        <br>
        <br>
        <br>    <table id="tblUsuarios" title="Mis Usuarios" class="easyui-datagrid"
                       url="<?php echo Class_config::get('urlApp') ?>/?controller=User&&action=List"
                       toolbar="#toolbar" 
                       rownumbers="true" fitColumns="true" singleSelect="true">
            <thead>
                <tr>
                    <th field="lastName" width="50">Apellidos</th>
                    <th field="names" width="50">Nombres</th>
                    <th field="user" width="50">Usuario</th>
                    <th field="description" width="50">T. Usuario</th>
                    <th data-options="field:'pkTypeUser',width:'50',hidden:'true'">T. Usuario</th>
                    <th field="id" width="50">id</th>
                </tr>
            </thead>
        </table>
        <div id="toolbar">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newUser()">Nuevo Usuario</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editUser()">Editar Usuario</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyUser()">Eliminar Usuario</a>
            <input type="checkbox"> Sin Usuario
            <!--<a href="javascript:void(0)" class="easyui-"   onclick="destroyUser()">Mostrar empleados si usuario</a>-->
        </div>

        <div id="dlg_usuarios" class="easyui-dialog" style="width:580px;padding:10px 20px"
             closed="true" modal="true" buttons="#dlg_usuarios-buttons">
            <div class="ftitle">Informacion del Usuario</div>
            <form id="frmUsuarios" method="post" novalidate>
                <div class="fitem">
                    <label>Nombres:</label>
                    <input name="names" class="easyui-textbox"  disabled="true">
                </div>
                <div class="fitem">
                    <label>Apellidos:</label>
                    <input name="lastName" class="easyui-textbox" disabled="true">
                </div>
                <div class="fitem">
                    <label>Trabajador:</label>
                    <select name="trabajador" class="" id="cmbTrabajadorUser" onchange="verificaPassword($('#cmbTrabajadorUser').val())"></select>
                </div>

                <div class="fitem" hidden="true">
                    <label>Usuario:</label>
                    <input name="user" class="easyui-textbox">
                </div>
                <div class="fitem">
                    <input type="radio"  checked="checked" name="clave" value="1" onchange="mostrarClave()" > Generar clave
                    <input type="radio" checked="" name="clave" value="2" onchange="mostrarClave()" > Digitar Clave


                </div>
                <div class="fitem clave" hidden="true">
                    <label >Clave  <input onkeydown="verificaPassword($('#lblClaveStatus').val());" onchange="verificaPassword($('#lblClaveStatus').val());"  name="textClave" id="lblClaveStatus"></label>
                </div>
                <label class="lblClaveStatus" id="msg"></label>
                

            </form>
        </div>
        <div id="dlg_usuarios-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveUser()" style="width:90px"><?php echo Class_message::get('BtnSave') ?></a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg_usuarios').dialog('close')" style="width:90px"><?php echo Class_message::get('BtnCancel') ?></a>
        </div>
        <script type="text/javascript">
//            $(function () {
//            var $radios = $('input:radio[name=clave]');
//            if ($radios.is(':checked') === false) {
//                $radios.filter('[value=1]').prop('checked', true);
//            }
//            })
            function mostrarClave() {
                var $radios = $('input:radio[name=clave]:checked').val();
                if ($radios === "1") {
                    $(".clave").hide();
                }
                else {
                    $(".clave").show();
                }
            }
            var url = "";
            listTrabajadoresSInUsuario('cmbTrabajadorUser');

            function newUser() {
                $('#dlg_usuarios').dialog('open').dialog('setTitle', 'Nuevo Usuario');
                $('#frmUsuarios').form('clear');
                var $radios = $('input:radio[name=clave]');
                if ($radios.is(':checked') === false) {
                    $radios.filter('[value=1]').prop('checked', true);
                }
                url = '<?php echo Class_config::get('urlApp') ?>/?controller=User&action=AddUser';
            }
            function editUser() {
                var row = $('#tblUsuarios').datagrid('getSelected');
                if (row) {
                    $('#dlg_usuarios').dialog('open').dialog('setTitle', 'Editando Usuario');
                    $('#frmUsuarios').form('load', row);
                    url = 'update_user.php?id=' + row.id;
                    $('#cmbTrabajadorUser').setDisable(true);
                }
            }
            function saveUser() {
                $('#frmUsuarios').form('submit', {
                    url: url,
                    onSubmit: function () {
                        return $(this).form('validate');
                    },
                    success: function (result) {
//                      else {
                            $('#dlg_usuarios').dialog('close');        // close the dialog
                            $('#tblUsuarios').datagrid('reload');    // reload the user data
//                        }
                    }
                });
            }
            function destroyUser() {
                var row = $('#tblUsuarios').datagrid('getSelected');
                if (row) {
                    $.messager.confirm('Confirm', 'Are you sure you want to destroy this user?', function (r) {
                        if (r) {
                            $.post('destroy_user.php', {id: row.id}, function (result) {
                                if (result.success) {
                                    $('#tblUsuarios').datagrid('reload');    // reload the user data
                                } else {
                                    $.messager.show({// show error message
                                        title: 'Error',
                                        msg: result.errorMsg
                                    });
                                }
                            }, 'json');
                        }
                    });
                }
            }
            function verificaPassword($value) {
                $.post('<?php echo Class_config::get('urlApp') ?>/?controller=User&action=VerificaPw', {value: $value}, function (result) {

                    if (result === "0") {
                        $('.lblClaveStatus').html("");
//                          console.log("entro");
                    }
                    else {
                        $('.lblClaveStatus').css("color",'red');
                        $('.lblClaveStatus').html("Esta Clave ya existe, ingrese otra por favor");
                    }
                }, 'html');

            }
            function listTrabajadoresSInUsuario($id) {
                $('#' + $id + ' option').empty();
                $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=User&&action=ListNotUser', function (data) {
                    for (var i = 0; i < data.length; i++) {

                        $('#' + $id).append("<option value=\"" + data[i].documento + "\">" + data[i].trabajador + "</option>")
                    }

                });
            }
        </script>
        <style type="text/css">
            #frmUsuarios{
                margin:0;
                padding:10px 30px;
            }
            .ftitle{
                font-size:14px;
                font-weight:bold;
                padding:5px 0;
                margin-bottom:10px;
                border-bottom:1px solid #ccc;
            }
            .fitem{
                margin-bottom:5px;
            }
            .fitem label{
                display:inline-block;
                width:80px;
            }
            .fitem input{
                width:160px;
            }
        </style>
    </div>