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
        <br>

        <ul class="nav nav-tabs">
            <li class="active"><a href="#ProductoActivo" data-toggle="tab">Activos</a>
            </li>
            <li><a href="#ProductoInactivo" data-toggle="tab">Inactivos</a>
            </li>
            <p class="text-right">
                <button onclick="modalRegistrarProducto()" type="button" class="btn btn-success"id="btnGuardarPagoDiario">
                    <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span> Nuevo Producto
                </button>
            </p>
        </ul>
        </br>
        <div class="tab-content">
            <div class="tab-pane active" id="ProductoActivo" >            
                <table id="tblProductoActivo" title="Productos" class="table table-borderer" >
                    <thead>
                        <tr>
                            <th style="display: none;visibility: hidden">Codigo</th>
                            <th>Producto</th>        
                            <th>P. Venta</th>
                            <th>P. Compra</th>
                            <th>Stock</th>
                            <th style="display: none;visibility: hidden">pkTipo</th>
                            <th>Tipo</th>    
                            <th></th>
                            <th></th>            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $db = new SuperDataBase();
                        $query = "SELECT pr.pkProducto,upper(pr.descripcion) as descripcionProducto, tp.pkTipo,upper(tp.descripcion) as descripcionTipo," .
                                " precioVenta, precioCompra, stock FROM producto_sucursal p inner join productos pr on pr.pkProducto=p.pkProducto " .
                                "inner join tipos tp on (tp.pkTipo=p.pkTipo) where pkSucursal= '" . UserLogin::get_pkSucursal() . "' and pr.estado=0;";
                        $result = $db->executeQuery($query);
                        while ($row = $db->fecth_array($result)) {
                            echo "<tr class='success'>";
                            echo "<td style='display: none;visibility: hidden'>" . $row['pkProducto'] . "</td>";
                            echo "<td>" . utf8_encode($row['descripcionProducto']) . "</td>";
                            echo "<td>" . utf8_encode($row['precioVenta']) . "</td>";
                            echo "<td>" . utf8_encode($row['precioCompra']) . "</td>";
                            echo "<td>" . utf8_encode($row['stock']) . "</td>";
                            echo "<td style='display: none;visibility: hidden'>" . $row['pkTipo'] . "</td>";
                            echo "<td>" . $row['descripcionTipo'] . "</td>";
                            echo "<td>";
                            echo "<a onclick='modalEditarProducto(\"" . $row[0] . "\",\"" . $row[1] . "\"," . $row[2] . "," . $row['precioVenta']. "," . $row['precioCompra'] . "," . $row['stock'] . ")'><span class='glyphicon glyphicon-pencil' title='Editar Producto'></span></a>";
                            echo "</td>";
                            echo "<td>";
                            echo "<a onclick='modalEliminarTipo(" . $row[0] . ")'><span class='glyphicon glyphicon-minus-sign' title='Eliminar Producto'></span></a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="modalProductos" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><label id="tituloModalProductos"></label></h4>
                        </div>
                        <div class="modal-body">
                            <form id="formProducto">                        
                                <input name="id" id="txtIdProducto" style="display: none;"/>
                                Producto
                                <input required="true" type="text" name="producto" class="form-control" id="txtProducto" placeholder="Ingrese Nombre del Producto" />
                                <br/>
                                Tipo
                                <select  class="form-control" title="Debe Elegir la Tipo" id="cmbTipo" required="true" name="tipo">
                                </select>
                                <br/>
                                Precio de Venta
                                <input required="true" type="text" name="precioVenta" class="form-control" id="txtPrecioVenta" placeholder="Ingrese Precio de Venta Producto" />                            
                                <br/>
                                Precio de Compra
                                <input required="true" type="text" name="precioCompra" class="form-control" id="txtPrecioCompra" placeholder="Ingrese Precio de Compra Producto" />                            
                                <br/>
                                Stock
                                <input required="true" type="text" name="stock" class="form-control" id="txtstock" placeholder="Ingrese Stock del Producto" />                            
                            </form>
                        </div>
                        <div class="modal-footer">

                            <button class="btn btn-primary" onclick="guardarProducto()">Guardar</button>

                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                        </div>
                    </div>
                </div>
            </div>                     
        </div>
    </div>  
</body>
<script>

    _listTipos('cmbTipo');            
    $('#tblProductoActivo').DataTable();
            
    var url = "";
        
    //Modal de Registro
    function modalRegistrarProducto(){
        $('#modalProductos').modal('show'); 
        $('#tituloModalProductos').html('Registrando Producto')  ;
        $('#txtIdProducto').val("");
        $('#txtDescripcionProducto').val("");
        $('#cmbTipo').val("");
        $('#txtPrecioVenta').val("");
        $('#txtPrecioCompra').val("");
        $('#txtstock').val("");
        url="<?php echo class_config::get('urlApp') ?>/?controller=Product&action=Save";
    }
    
    //Modal de Edicion
    function modalEditarProducto($pkProducto,$descripcionProducto,$pkTipo,$precioVenta,$precioCompra,$stock){
        $('#modalProductos').modal('show'); 
        $('#tituloModalProductos').html('Editando Producto')  ;
        $('#txtIdProducto').val($pkProducto);
        $('#txtProducto').val($descripcionProducto);
        $('#cmbTipo').val($pkTipo);
        $('#txtPrecioVenta').val($precioVenta);
        $('#txtPrecioCompra').val($precioCompra);
        $('#txtstock').val($stock);
        url="<?php echo class_config::get('urlApp') ?>/?controller=Product&action=Edit";
    }
        
    //Acción para el modal de registro y edición
    function guardarProducto(){
        $.post( url, $('#formProducto').serialize(),
        function( data ) {            
            location.reload();
        });
        $('#modalProductos').modal('hide');          
    }
    
</script>
