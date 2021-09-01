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
        <br>       
        <table id="tblClientes" title="Clientes" class="easyui-datagrid" style="width:1200px;height:450px"
               toolbar="#toolbarClientes" pagination="true"
               rownumbers="true" fitColumns="true" singleSelect="true">
            <thead>
                <tr>
                    <th data-options="field:'pkCliente',hidden:'true'">ID</th>
                    <th field="document" >Documento</th>
                    <th field="names" >Nombres</th>
                    <th field="lastNames">Apellidos</th>
                    <th field="email">Email</th>
                    <th field="date">F.Nacim.</th>
                    <th field="address">Direccion</th>
                    <th field="celphone">Celular</th>
                    <th field="telephone">T. Fijo</th>
                    <th field="descriptionArea">Area</th>
                    <th data-options="field:'pkArea',hidden:'true'">Area</th>
                    <th data-options="field:'pkStatusCivil',hidden:'true'">pkStatusCivil</th>
                    <!--<th data-options="field:'fkProfession',hidden:'true'">fkProfession</th>-->
                    <th data-options="field:'sexo'">sexo</th>
                    <th data-options="field:'departament',hidden:'true'">departament</th>
                    <th data-options="field:'province',hidden:'true'">province</th>
                    <th data-options="field:'district',hidden:'true'">district</th>
                    <th data-options="field:'fkUbigeo',hidden:'true'">fkUbigeo</th>
                    <!--<th field="phone" width="50">Phone</th>-->
                    <!--<th field="email" width="50">Email</th>-->
                </tr>
            </thead>
        </table>
        <div id="toolbarClientes">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newCliente()">Nuevo Cliente</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editCliente()">Editar Cliente</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyCliente()">Elminar Cliente</a>
        </div>

        <div id="dlg-Cliente" class="easyui-dialog" style="width:550px;height:580px;padding:10px 20px"
             closed="true" buttons="#dlg-Cliente-buttons">
            <div class="ftitle">Informacion del Cliente</div>
            <form id="frmClientes" method="get">
                <!--<div class="fitem">-->
                <!--<label>DNI:</label>-->
                <!--<input name="pkCliente" style="display: none">-->
                <!--</div>-->
                <div class="fitem">
                    <label>DNI:</label>
                    <input id="txtDocumentIdentityClienteRegister" onblur="_validateDocument()"  maxlength="8" name="document" title="Número de documento de identidad, contiene solo 8 caracateres" class="easyui-validatebox textbox easyui-textbox easyui-tooltip" required="true">
                    <label id="msgValidateDni" class="red"><span></span></label>
                </div>
                <div class="fitem">
                    <label>Nombres:</label>
                    <input name="names" class="easyui-validatebox textbox easyui-textbox easyui-tooltip" required="true" title="Ingrese aquí el nombre del empleado">
                </div>
                <div class="fitem">
                    <label>Apellidos:</label>
                    <input name="lastNames" class="easyui-validatebox easyui-textbox easyui-tooltip" title="Ingrese aquí los apellidos del empleado" required="true">
                </div>
                <div class="fitem">
                    <label>Sexo:</label>
                    <input  class="easyui-combobox easyui-tooltip"   title="Debe Elegir el departamento" title="Seleccione el Sexo"  id="cmbRegisterSexo" required="true"
                            name="sexo"
                            data-options="
                            url:'<?php echo Class_config::get('urlApp') ?>/?controller=Sexo&&action=List',
                            method:'get',
                            valueField:'pkSexo',
                            textField:'description',

                            panelHeight:'auto'

                            ">

                </div>
                <div class="fitem">
                    <label>F. Nacim:</label>
                    <input name="date" id="txtdatBirthRegisterCliente" class="easyui-validatebox textbox easyui-textbox easyui-tooltip" required="true">
                </div>
                <div class="fitem">
                    <label><?php echo Class_message::get('msgTxtDeparment'); ?></label>
                    <input  class="easyui-combobox easyui-tooltip"   title="Debe Elegir el departamento" id="cmbRegisterDeparment" required="true"
                            name="departament"
                            data-options="
                            url:'<?php echo Class_config::get('urlApp') ?>/?controller=Ubigeo&&action=ListDepartament',
                            method:'get',
                            valueField:'description',
                            textField:'description',
                            onSelect: function(rec){
                            var url = '<?php echo Class_config::get('urlApp') ?>/?controller=Ubigeo&&action=ListProvince&&departament='+rec.description;
                            $('#cmbRegisterProvince').combobox('reload', url);
                            },


                            panelHeight:'auto'

                            ">
    <!--                <select id="cmbRegisterDeparment" name="departament" onchange="_loadProvince('cmbRegisterProvince', 'cmbRegisterDisctrict')">
                        <option value="0">
                    <?php echo Class_message::get('TxtSelectionOneOption') ?>
                        </option>
                    </select>-->
                </div>
                <div class="fitem">
                    <label><?php echo Class_message::get('msgTxtProvince') ?></label>
                    <input  class="easyui-combobox"  id="cmbRegisterProvince" required="true"
                            name="province"
                            data-options="
                            url:'<?php echo Class_config::get('urlApp') ?>/?controller=Ubigeo&&action=ListProvince',
                            method:'get',
                            valueField:'description',
                            textField:'description',
                            onSelect: function(rec){
                            var url = '<?php echo Class_config::get('urlApp') ?>/?controller=Ubigeo&&action=ListDistrict&&province='+rec.description;
                            $('#cmbRegisterDistrict').combobox('reload', url);
                            },

                            panelHeight:'auto'

                            ">
    <!--                <select id="cmbRegisterProvince" name="province" onchange="_loadDistrict()">
                        <option value="0">
                    <?php echo Class_message::get('TxtSelectionOneOption') ?>
                        </option>
                    </select>-->
                </div><!--
                -->            <div class="fitem">
                    <label><?php echo Class_message::get('msgTxtDistrict') ?></label>
                    <input  class="easyui-combobox easyui-tooltip"  id="cmbRegisterDistrict" required="true"
                            name="district"
                            data-options="
                            url:'<?php echo Class_config::get('urlApp') ?>/?controller=Ubigeo&&action=ListDistrict',
                            method:'get',
                            valueField:'pkUbigeo',
                            textField:'description',
                            onSelect: function(rec){

                            $('#txtUbigeoCliente').val( rec.pkUbigeo);
                            },
                            panelHeight:'auto'

                            ">
                    <input id="txtUbigeoCliente" name="fkUbigeo" style="display: none">
                </div>
                <div class="fitem" required="true">
                    <label>Direccion:</label>
                    <input name="address" class="easyui-validatebox textbox easyui-textbox easyui-tooltip" required="true">
                </div>
                <div class="fitem" required="true">
                    <label>Celular:</label>
                    <input id="txtPhoneRegisterCliente" name="celphone" class=" easyui-validatebox textbox easyui-textbox easyui-tooltip" required="true">
                </div>
                <div class="fitem">
                    <label>T. Fijo:</label>
                    <input id="txtTelfRegisterCliente" name="telephone" class="easyui-validatebox textbox easyui-textbox easyui-tooltip">
                </div>
                <div class="fitem">
                    <label>Email:</label>
                    <input name="email" onblur="_validateEmail()" id="txtEmailRegisterCliente" class="easyui-validatebox textbox easyui-textbox easyui-tooltip" validType="email" required="true">
                    <label id="msgValidateEmail" class="red"><span></span></label>
                </div>

                <!--<div class="fitem">-->
                <!--                <label>Profesion:</label>-->
                <!--                <input  class="easyui-combobox easyui-tooltip"   title="Debe Elegir una profesion" title="Seleccione el Sexo"  id="cmbRegisterSexo" required="true"
                                        name="fkProfession"
                                        data-options="
                                        url:'<?php // echo Class_config::get('urlApp')   ?>/?controller=Professions&&action=List',
                                        method:'get',
                                        valueField:'pkProfessions',
                                        textField:'description',
                
                                        panelHeight:'auto'
                
                                        ">-->
                <!--                <select style="width: 150px" id="cmbRegisterProfessions" name="" required="true">
                                    <option value="0">
                <?php echo Class_message::get('TxtSelectionOneOption') ?>
                                    </option>
                                </select>  -->

            </form>
        </div>
        <div id="dlg-Cliente-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveCliente()" style="width:90px">Guardar</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-Cliente').dialog('close')" style="width:90px">Cancelar</a>
        </div>
        <?php
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showFooter();
        ?> 
        <script type="text/javascript">

            $(function () {
                //            $('#tblClientes').datagrid({data: getData()}).datagrid('clientPaging');

            });
            var url = '<?php echo Class_config::get('urlApp') ?>?controller=Cliente&&action=Save';
            function newCliente() {
                $('#dlg-Cliente').dialog('open').dialog('setTitle', 'Nuevo Cliente');
                $('#frmClientes').form('clear');
                url = '<?php echo Class_config::get('urlApp') ?>?controller=Cliente&&action=Save';
            }
            function editCliente() {
                var row = $('#tblClientes').datagrid('getSelected');
                if (row) {
                    $('#dlg-Cliente').dialog('open').dialog('setTitle', 'Editando Cliente');
                    $('#frmClientes').form('load', row);
                    url = '<?php echo Class_config::get('urlApp') ?>?controller=Cliente&&action=Update&pkCliente=' + row.pkCliente;
                } else {
                    $.messager.alert('Alerta', 'Error, no ha seleccionado ningun empleado en la tabla', 'error');
                }
            }
            function saveCliente() {
                console.log($("#frmClientes").form('validate'));
                if ($("#frmClientes").form('validate') == true) {
                    $.ajax({
                        type: "GET",
                        url: url,
                        data: $("#frmClientes").serialize(), // Adjuntar los campos del formulario enviado.
                        dataType: 'json',
                        success: function (data)

                        {
                            //                        console.log(data);
                            //                        console.log(url);
                            if (data == "true") {
                                $('#dlg-Cliente').dialog('close'); // close the dialog
                                $('#tblClientes').datagrid('reload');
                            }
                            else {
                                $.messager.show({
                                    title: 'Error',
                                    msg: data
                                });
                            }
                            //
                            //
                        }

                    });
                }
                else {

                    $.messager.show({
                        title: 'Error',
                        msg: "No se han Completado los campos requeridos"
                    });
                }
            }
            function destroyCliente() {
                var row = $('#tblClientes').datagrid('getSelected');
                if (row) {
                    $.messager.confirm('Confirmar', '¿Esta seguro que desea eiminar este trabajador?', function (r) {
                        if (r) {
                            $.get('<?php echo Class_config::get('urlApp') ?>?controller=Cliente&&action=Drop', {pkCliente: row.pkCliente}, function (result) {
                                if (result == "true") {
                                    $('#tblClientes').datagrid('reload');    // reload the user data
                                } else {
                                    $.messager.show({// show error message
                                        title: 'Error',
                                        msg: "No se ha podido eliminar"
                                    });
                                }
                            }, 'json');
                        }
                    });
                }
                else {
                    $.messager.alert('Alerta', 'Error, no ha seleccionado ningun empleado en la tabla', 'error');
                }
            }
        </script>
        <style type="text/css">
            #fm{
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
        <script>
            $("#txtdatBirthRegisterCliente").datepicker({dateFormat: 'yy-mm-dd'});
            //        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Area&&action=List', function(data) {
            //            for (var i = 0; i < data.length; i++) {
            //
            //                $('#cmbRegisterArea').append("<option value=\"" + data[i].pkArea + "\">" + data[i].description + "</option>")
            ////            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            ////            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            //            }
            //
            //        });
            //        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=StatusCivil&&action=List', function(data) {
            //            for (var i = 0; i < data.length; i++) {
            //
            //                $('#cmbRegisterStatusCivil').append("<option value=\"" + data[i].pkStatusCivil + "\">" + data[i].description + "</option>")
            ////            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            ////            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            //            }
            //
            //        });
            //        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Professions&&action=List', function(data) {
            //            for (var i = 0; i < data.length; i++) {
            //
            //                $('#cmbRegisterProfessions').append("<option value=\"" + data[i].pkProfessions + "\">" + data[i].description + "</option>")
            ////            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            ////            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            //            }
            //
            //        });
            //        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Sexo&&action=List', function(data) {
            //            for (var i = 0; i < data.length; i++) {
            //
            //                $('#cmbRegisterSexo').append("<option value=\"" + data[i].pkSexo + "\">" + data[i].description + "</option>")
            ////            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            ////            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            //            }
            //
            //        });
            //        listadoDepartamentos('cmbRegisterDeparment');
        </script>
    </div>