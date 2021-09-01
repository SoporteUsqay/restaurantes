<?php include 'Application/Views/template/header.php'; ?>
<body>
    <link href="Public/select2/css/select2.css" rel="stylesheet">
    <style>
        .select2-container{
            height: 46px !important;
        }
        .select2-selection{
            height: 46px !important;
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.33;
            border-radius: 6px;
        }
    </style>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();

    $db = new SuperDataBase();
    ?>
    <div class="container mt-12 mb-5">
        <div class="row">
            <div class="col-md-12">
                <div class="tarjeta">
                    <div class="tarjeta-body">
                        <div class="row">
                            <div class="col-md-12">
                                <img src="Public/images/iconos2018/menu.png" class="center-block img-frm2" alt="">
                            </div>
                        </div>
                        <div class="alert alert-danger alert-dismissable" style="display:none;" id="merror">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            Hubo un error, reintenta
                        </div>
                        <div class="alert alert-success alert-dismissable" style="display:none;" id="msuccess">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            Operación Completada con Éxito
                        </div>
                        
                       <div class="row">
                            <div class="col-md-12">
                                <h3 class="administrar text-center">Platos - Carta</h3>
                                <form id="frmPlatos" action="#">
                                <input id="id" name="id" style="display: none">
                                    <div class="input-group input-group-lg mt-2">
                                        <span class="input-group-addon" id="basic-addon1">
                                            <img src="Public/images/iconos2018/breakfast2.png" alt="">
                                        </span>
                                        <input id="descripcion" name="descripcion_plato" required="true"  type="text" class="form-control" placeholder="Descripción" aria-describedby="basic-addon1" autofocus>
                                    </div>
                                    <div style="display:none;">
                                        <label>Categoria</label>
                                        <select name="categoria" required="true" id="cmbRegisterCategoria" class="form-control"></select>
                                    </div>
                                    <div class="input-group input-group-lg mt-1">
                                        <span class="input-group-addon" id="basic-addon3">
                                            <img src="Public/images/iconos2018/tag.png" alt="">
                                        </span>
                                        <input id="precioventa" name="precioVenta" required="true" class="form-control" type="number" placeholder="Precio" aria-describedby="basic-addon3">
                                    </div>
                                    <div class="input-group input-group-lg mt-1">
                                        <span class="input-group-addon" id="basic-addon2">
                                            <img src="Public/images/iconos2018/salad.png" alt="">
                                        </span>                                      
                                        <select onchange="carga_tipo_sunat();" name="pkTipo" required="true" id="cmbTipo" class="form-control" aria-describedby="basic-addon2"></select>
                                    </div>
                                    <div class="input-group input-group-lg mt-1">
                                        <span class="input-group-addon" id="basic-addon5">
                                            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                                        </span>                                   
                                        <select name="tipo_sunat" required="true" id="tipo_sunat" class="form-control" aria-describedby="basic-addon5">
                                        </select>
                                    </div>
                                    <div class="input-group input-group-lg mt-1">
                                        <span class="input-group-addon" id="basic-addon2">
                                            <span class="glyphicon glyphicon-tag" aria-hidden="true"></span>
                                        </span>                                
                                        <select name="tipo_articulo" required="true" id="tipo_articulo" class="form-control" aria-describedby="basic-addon2">
                                            <option value="1">PRODUCTO</option>
                                            <option value="2">SERVICIO</option>
                                        </select>
                                    </div>

                                    <div class="input-group input-group-lg mt-1">
                                        <span class="input-group-addon" id="basic-addon2">
                                            <span class="glyphicon glyphicon-tag" aria-hidden="true"></span>
                                        </span>                                
                                        <select name="tipo_impuesto" required="true" id="tipo_impuesto" class="form-control" aria-describedby="basic-addon2">
                                            <?php
                                                $tipos_impuestos = $db->executeQuery("Select * from tipo_impuesto");
                                                while($row = $db->fecth_array($tipos_impuestos)){
                                                    echo '<option value="'.$row["id"].'">'.$row["nombre"].'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="input-group input-group-lg mt-1" style="display:none;">
                                        <span class="input-group-addon" id="basic-addon4">
                                            <img src="Public/images/iconos2018/plus.png" alt="">
                                        </span>
                                        <input id="stockMinimo" name="stockMinimo" required="true" class="form-control" type="hidden" value="0" placeholder="Stock" aria-describedby="basic-addon4">
                                    </div>
                                    
                                </form>
                                <div class="form-group mt-2 text-center">
                                    <button onclick="guardarPlatos()" class="btn btn-primary btn- btn-lg">
                                        <i class="fa fa-save"></i>
                                        Guardar
                                    </button>
                                </div>
                            </div>
                       </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="tarjeta">
                <div class="tarjeta-body">

                <div class="text-left" style="margin-bottom: 1em">
                    <button class="btn btn-success" type="button" onclick="openModalCarta()">
                        Subir Carta PDF para Qr
                    </button>
                </div>

                <table id="tblPlatos" class="table">
            <thead>
                <th>ID</th>
                <th>Plato</th>
                <th>Precio</th>
                <th>T.Plato</th>
                <th>Cod.SUNAT</th>
                <th>Tipo</th>
                <th>Impuesto</th>
                <th>INS</th>
                <th>Opciones</th>
            </thead>
            <tbody>
                <?php
                $db = new SuperDataBase();
                
                //Obtenemos impuesto de bolsa para este año
                $monto_icbper = 0;
                $query_icbper = "Select * from cloud_config where parametro = 'icbper'";
                $result_i = $db->executeQuery($query_icbper);

                if($row = $db->fecth_array($result_i)){
                    $monto_icbper = floatval($row["valor"]);
                }

                $tipos = [];

                $query = "select pk_accion from accion_caja where tipo_accion = 'TYP' and caja = '".$_COOKIE['c']."'";

                $res = $db->executeQuery($query);
                while($row = $db->fecth_array($res)) {
                    $tipos[] = $row['pk_accion'];
                }

                if (count($tipos) > 0) {
                    $str_tipos = implode(', ', $tipos);

                $query = "SELECT
                    plato.*,
                    tipos.descripcion as tipo,
                    (SELECT count(*) FROM n_receta i where i.plato_id=plato.pkPlato and i.deleted_at is null) as receta
                FROM
                    plato
                INNER JOIN tipos on plato.pktipo = tipos.pkTipo
                WHERE
                    plato.estado = 0 and
                    plato.pktipo IN (
                        $str_tipos
                    )
                ";

                $result = $db->executeQuery($query);
                while ($row = $db->fecth_array($result)) {

                    $result_sunat = $db->executeQuery("SELECT cs.id, cs.descripcion, ti.nombre as impuesto, pc.tipo_articulo from plato_codigo_sunat pc, codigo_sunat cs, tipo_impuesto ti where pc.id_plato = '".$row[0]."' AND pc.id_codigo_sunat = cs.id AND pc.id_tipo_impuesto = ti.id");
                            
                    $id_sunat = "";
                    $descripcion_sunat = "";
                    $tipo_plato = "SERVICIO";
                    $tipo_impuesto = "";
                    if($row1 = $db->fecth_array($result_sunat)){
                        $id_sunat = $row1["id"];
                        $descripcion_sunat = $row1["descripcion"];
                        if(intval($row1["tipo_articulo"]) === 1){
                            $tipo_plato = "PRODUCTO";
                        }
                        $tipo_impuesto = $row1["impuesto"];
                    }

                    $class = "";
                    $row['receta'] = (int) $row['receta'];
                    if ($row['receta'] < 1) {
                        $class = "warning";
                    } else {
                        $class = "";
                    }
                    ?>
                    <tr class="<?php echo $class; ?>">
                        <td><?php echo $row[0] ?></td>
                        <td><?php echo $row['descripcion']; ?></td>
                        <td><?php echo $row['precio_venta']; 
                        if($tipo_impuesto === "ICBPER"){
                            echo "<br/><span style='color:red;'>+".$monto_icbper."</span>";
                        }
                        ?></td>
                        <td><?php if($row['tipo'] == ""){
                            echo "MENUS (Sistema)";
                        }else{
                            echo $row["tipo"];
                        }?></td>
                        <?php                            
                            echo "<td>".$id_sunat." ".$descripcion_sunat."</td>";
                            echo "<td>".$tipo_plato."</td>";
                            echo "<td>".$tipo_impuesto."</td>";
                        ?>
                        <td><?php
                        echo (int) $row['receta'];
                        ?></td>
                        <td>
                            <a href="#" onclick="sel('<?php echo $row[0]; ?>')">  <span class="glyphicon glyphicon-pencil"></span></a>
                            <a onclick="modalEliminarPlato('<?php echo $row[0]; ?>')"><span class="glyphicon glyphicon-remove"></span></a>
                            <a title="Ver receta" href="<?php echo class_config::get('urlApp') ?>/?controller=Almacen&action=AdmInsumo&pkPlato=<?php echo $row[0] ?>&descripcion=<?php echo utf8_encode($row['descripcion']) ?>"><span class="glyphicon glyphicon-list"></span></a>
                        </td>
                    </tr>
                <?php } }
                ?>
            </tbody>
        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="modalPlatos2" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalPlatos2"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formPlato2">
                            <input name="id" id="txtIdPlato2" style="display: none;"/>
                            <strong id="txtMensajeeliminar"></strong>
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button id="btnColor" class="btn btn-primary" onclick="deletePlato()">Aceptar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>

        <div id="modalUploadCarta" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Carta</h4>
                    </div>
                    <div class="modal-body" id="mbUpload">
                        <form id="formUploadCarta">
                            <label for="">Carta </label>
                            <input type="file" name="file" id="carta_file" class="form-control">

                            <small>** El archivo debe estar en formato PDF</small>
                        </form>

                        <div class="text-center">
                            <button id="btnUploadQr" class="btn btn-primary" onclick="uploadCarta()">Subir</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                        </div>
                    </div>
                    <div class="modal-body text-center" id="mbQr">
                        <div>Escanee el código QR para acceder a la carta virtual</div>
                        <img src='carta-qr.png'>

                        <div>
                            <a onclick="changeUpload()">Actualizar Carta</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="Public/select2/js/select2.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
        <script type="text/javascript" src="Application/Views/Sale/js/AdminPlatos.js.php" ></script>
        
        <script>
            _loadTiposCategoria('cmbRegisterCategoria', 'cmbTipo');
            
            $("#descripcion").focus();
            
            //Modal de Eliminación
            function modalEliminarPlato($id){
                $('#modalPlatos2').modal('show');
                $('#tituloModalPlatos2').html('Eliminando Plato')  ;
                $('#txtMensajeeliminar').html('¿Seguro que desea eliminar este Plato?');
                $('#txtIdPlato2').val($id);
                $('#btnColor').removeClass();
                $('#btnColor').addClass('btn btn-danger');
                url="<?php echo class_config::get('urlApp') ?>/?controller=Sale&action=deletePlato";
            }

            //Acción para el modal de habilitar y deshabilitar
            function deletePlato(){
                $.post( url, {id:$('#txtIdPlato2').val()},
                function( data ) {
                    location.reload();
                });
                $('#modalPlatos2').modal('hide');
            }
            
            function carga_tipo_sunat(){
                var id_tipo = $("#cmbTipo").val();
                $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&&action=getTipoSunat&pktipo=' + id_tipo,{}, function (data) {
                    $('#tipo_sunat')
                    .empty()
                    .append('<option selected value="'+data[0].id_sunat+'">'+data[0].id_sunat+' - '+decodeURIComponent(escape(data[0].descripcion_sunat))+'</option>');
                    $('#tipo_sunat').select2('data', {
                      id: data[0].id_sunat,
                      label: decodeURIComponent(escape(data[0].descripcion_sunat))
                    });
                    $('#tipo_sunat').trigger('change');
                });
            }

            function changeAccesoRapido(pkPlato, isAccesoRapido) {

                $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Platos&&action=ChangeAccesoRapido', {pkPlato, isAccesoRapido}).then(function (res) {
                    if (res == 'ok') {
                        if (isAccesoRapido == 1) {
                            alert('Plato quitado del acceso rápido')
                        } else {
                            alert('Plato agregado al acceso rápido')
                        }
                        location.reload();
                    } else {
                        alert('Ocurrió un inconveniente, inténtelo de nuevo por favor.');
                    }

                });
            }

            function openModalCarta() {

                $('#mbQr').hide()
                $('#mbUpload').hide()

                $('#modalUploadCarta').modal('show')

                $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Platos&&action=GetCartaQr', {}, function (data) {
                    if (data) {
                        $('#mbQr').show()
                    } else {
                        $('#mbUpload').show()
                    }
                });
            }

            function changeUpload() {
                $('#mbQr').hide()
                $('#mbUpload').show()
            }

            function uploadCarta() {

                if ($('#carta_file').prop('files').length == 0) return alert('Debe seleccionar un archivo para ser subido');

                var file_data = $('#carta_file').prop('files')[0];

                var form_data = new FormData();                  
                form_data.append('file', file_data);

                $('#btnUploadQr').attr('disabled', true);

                $.ajax({
                    url: 'http://sistemausqay.com/carta_qr/uploads/upload.php', 
                    dataType: 'json',  
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,                         
                    type: 'post',
                    success: function(res){
                        // console.log(res)
                        // alert(php_script_response); 

                        if (res.url) {

                            $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Platos&&action=SaveCartaQr', {url: res.url}).then(function (res) {
                                
                                location.reload();
                            });
                        }

                        
                    }
                });
            }
        </script>
