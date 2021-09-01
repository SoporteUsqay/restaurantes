<?php include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <div class="container">
    
        <br>
        <br>
        <br>       
        <center>
        
        <div>
            <form class="form-horizontal" id="frmRegistrarSalones" method="get">
                <div class="form-group">
                    <div class="col-md-2">
                        <button onclick="registrarMensaje()" type="button" class="btn btn-success"id="btnGuardarPagoDiario">
                            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true" ></span> Nuevo Mensaje
                        </button>
                    </div>
                </div>
            </form>
        </div>
 
        <table id="tblMensajes" title="Mensajes" class="table table-borderer" >
            <thead>
                <tr>
                    <th data-options="field:'pkMensaje',hidden:'true'">ID</th>
                    <th field="descripcion" >Descripcion</th>
                    <?php 
                    $db = new SuperDataBase();
                    $query_c = "SELECT * FROM cajas";
                    $result_c = $db->executeQuery($query_c);
                    while ($row_c = $db->fecth_array($result_c)) {
                        echo "<th>CAJA ".$row_c["caja"]."</th>";
                    }
                    ?>        
                    <th></th>
                    <th></th>            
                </tr>
            </thead>
            
            <tbody>
            <?php
            $query="SELECT * FROM mensaje";
            $result= $db->executeQuery($query);
            while($row= $db->fecth_array($result) ) {
                echo "<tr>";
                echo "<td>" . $row[0]. "</td>";
                echo "<td>" . utf8_encode($row[1]). "</td>";
                echo "</td>";
                $query_c = "SELECT * FROM cajas";
                $result_c = $db->executeQuery($query_c);
                while ($row_c = $db->fecth_array($result_c)) {
                    $query_sal = "Select * from accion_caja where pk_accion = '".$row[0]."' AND tipo_accion = 'MSG' AND caja = '".$row_c["caja"]."'";
                    $result_sal = $db->executeQuery($query_sal);
                    $asociado = 0;
                    while ($row_sal = $db->fecth_array($result_sal)) {
                        echo "<td><a href='?controller=Config&action=QuitaMensajeCaja&pkMensaje=".$row[0]."&caja=".$row_c["caja"]."'><span class='glyphicon glyphicon-ok' title='Mensaje Activo en Caja'></span></a></td>";
                        $asociado = 1;
                    }
                    
                    if($asociado === 0){
                        echo "<td><a href='?controller=Config&action=PonMensajeCaja&pkMensaje=".$row[0]."&caja=".$row_c["caja"]."'><span class='glyphicon glyphicon-minus-sign' title='Mensaje Inactivo en Caja'></span></a></td>";                     
                    }
                }
                echo "<td>";
                echo "<a onclick='abrirModal(" . $row[0]. ",\"" . $row[1]. "\")'><span class='glyphicon glyphicon-pencil'></span></a>";
                echo "</td>";
                echo "<td>";
                echo "<a onclick='eliminarMensaje(" . $row[0]. ")'><span class='glyphicon glyphicon-remove'></span></a>";

                echo "</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
        </center>        
        
        <div id="modalMensajes" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalMensajes"></label></h4>
                    </div>
                        <div class="modal-body">
                        <form id="formMensaje">                        
                        <input name="id" id="txtIdMensaje" style="display: none;"/>
                        Escriba Aqui su  mensaje
                        <input name="descripcion" class="form-control" id="txtDescripcionMensaje" placeholder="Ejemplo: Con ají, Sin mayonesa" />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div id="dlg-buttonsCancelarCuenta">

                            <button class="btn btn-primary" onclick="saveMesaje()">Guardar</button>

 <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                                              </div>                            
                    </div>
                </div>
            </div>
        </div>
        
        <div id="modalMensajeseliminar" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalMensajes2"></label></h4>
                    </div>
                        <div class="modal-body">
                            <form id="formMensaje2" class="form">
                        <input name="id1" id="txtIdMensaje" style="display: none;"/>
                        <strong >¿Seguro que desea eliminar el mensaje seleccionado?</strong>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div id="dlg-buttonsCancelarCuenta">
                    <button class="btn btn-danger" onclick="deleteMesaje()">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                                              </div>                            
                    </div>
                </div>
            </div>
        </div>
        
        
        <?php
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showFooter();
        ?> 
        <script type="text/javascript">
$(document).ready(function () {
        $('#tblMensajes').DataTable();
    });
    var url="";
    function abrirModal($id,$descripcion){
     $('#modalMensajes').modal('show'); 
     $('#tituloModalMensajes').html('Editando Mensaje')  ;
     $('#txtIdMensaje').val($id);
     $('#txtDescripcionMensaje').val($descripcion);
     url="<?php echo class_config::get('urlApp')?>/?controller=Mensaje&action=Edit";
     
     
    }
    function registrarMensaje(){
     $('#modalMensajes').modal('show'); 
     $('#tituloModalMensajes').html('Registrando Mensaje')  ;
     $('#txtIdMensaje').val("");
     $('#txtDescripcionMensaje').val("");
     url="<?php echo class_config::get('urlApp')?>/?controller=Mensaje&action=Save";      
    }
    
    function eliminarMensaje($id){
     $('#modalMensajeseliminar').modal('show'); 
     $('#tituloModalMensajes2').html('Eliminando Mensaje')  ;
     $('#txtIdMensaje').val($id);
     url="<?php echo class_config::get('urlApp')?>/?controller=Mensaje&action=Delete";          
    }
    
    function saveMesaje(){
        $.post( url, $('#formMensaje').serialize(),
        function( data ) {
            location.reload();
        });
         $('#modalMensajes').modal('hide');
    }
    function deleteMesaje(){
        $.post( url, {id:$('#txtIdMensaje').val()},
        function( data ) {
            location.reload();
        });
         $('#modalMensajeseliminar').modal('hide');          
    }
    
            $(function () {
                //            $('#tblClientes').datagrid({data: getData()}).datagrid('clientPaging');

            });
            var url = '<?php echo Class_config::get('urlApp') ?>?controller=Cliente&&action=Save';
            function newCliente() {
                $('#dlg-Mensaje').dialog('open').dialog('setTitle', 'Nuevo Cliente');
                $('#frmClientes').form('clear');
                url = '<?php echo Class_config::get('urlApp') ?>?controller=Cliente&&action=Save';
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
        </script>
    </div>