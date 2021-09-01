<?php 
$titulo_importante = 'Almacenes';
include 'Application/Views/template/header.php';
require_once 'reportes/recursos/componentes/MasterConexion.php'; 
$objcon = new MasterConexion();
$f = $objcon->consulta_arreglo("Select * from cierrediario where pkCierreDiario = 1");

$obj = new Application_Models_CajaModel();
$fechaCaja = $obj->fechaCierre();

?>

<body>

    <?php
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();
    ?>
    
    <div class="container-fluid">

        <br>
        <br>
        <br>
        <br>


        <div class="panel panel-primary">

            <div class="panel-heading">
                <h4> 
                <i class="fa fa-clipboard"></i>
                Administrar Almacenes</h4>
            </div>

            <div class="panel-body">

                <div class="text-right">
                    <button type="button" class="btn btn-success" onclick="openModal(0)">
                        <i class="fa fa-plus-circle"></i>
                        Nuevo Almacen
                    </button>
                </div>

                <br>

                <table id="tblCompras" class="table">

                    <thead>
                        <th>#</th>
                        <th>Nombre</th>
                        <th></th>
                    </thead>
                
                    <tbody>

                        <?php 
                            
                            $db = new SuperDataBase();

                            $query = "
                                SELECT * FROM n_almacen
                            ";

                            $res = $db->executeQueryEx($query);
                            
                            $index = 1;
                            while ($row = $db->fecth_array($res)):

                        ?>
                        <tr>
                            <td><?php echo $index++ ?></td>
                            <td><?php echo $row['nombre'] ?></td>
                            <td class="text-right">
                                <a class="btn" onclick='openModal(1, <?php echo json_encode($row) ?>)'>
                                    <i class="fa fa-pencil text-info"></i>
                                </a>
                                <a class="btn" onclick="deleteCompra(<?php echo $row['id'] ?>)">
                                    <i class="fa fa-trash-o text-danger"></i>
                                </a>
                            </td>
                        </tr>

                        <?php endwhile ?> 

                    </tbody>
                </table>

            </div>
        </div>
    
    </div>

    
    <div id="modalForm" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar Nuevo Almacen</h4>
                </div>

                <div class="modal-body">

                    <form action="#" id="frmAlmacen" >
                        <div class="form-group">
                            <label for="">Nombre</label>
                            <input type="text" name="nombre" class="form-control" id="nombre" required>
                        </div>
                    </form>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarCompra()">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
                
            </div>
        </div>
    </div> 

    <div id="modalFormEdit" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Editar Compra - <span id="lblEditModal"></span></h4>
                </div>
                <div class="modal-body">

                    <form id="frmAlmacenEdit">
                        <input type="text" name="id" id="compraID" hidden>

                        <div class="form-group">
                            <label for="">Nombre</label>
                            <input type="text" name="nombre" class="form-control" id="nombreE" required>
                        </div>
                    
                    </form>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="editarCompra()">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div> 

    <script src="Application/Views/Almacen/js/AdministrarAlmacenes.js.php"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('#frmAlmacen').forEach( node => node.addEventListener('keypress', e => {
            if(e.keyCode == 13) {
               e.preventDefault();
               guardarCompra();
            }
        }))
        });
    </script> 
</body>

</html>