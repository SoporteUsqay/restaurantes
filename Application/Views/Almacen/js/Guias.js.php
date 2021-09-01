<?php require_once '../../../../Components/Config.inc.php'; 
$ultimoId=0;
$idProvisional=0;
$valor=0;
//$idEditar=0
?>
//<script>
    
    var idEditar=0;
    var parametro=0
    var url="";

    var hoy = new Date();
    
    $(document).ready(function () {       
        $ ('#tblGuiaActiva') .DataTable ({
            "order": [[0, "desc"], [2, 'desc']]
        });
        
        $('#tblGuiaInactiva').DataTable(); 

        $('#txtFecha').datepicker();

        LoadProveedores();

        LoadAlmacenes();
    });

    function LoadProveedores() {

        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Provedor&&action=List",
            dataType: "json",
            success: function (data) {

                var selected_proveedor = $('#cmb_Proveedor');

                var options = [];

                options.push(`
                        <option value="">
                            SIN PROVEEDOR
                        </option>
                    `);

                for (let item of data) {
                    options.push(`
                        <option value="${item.id}">
                            RUC: <strong>${item.ruc}</strong>
                             - 
                            <div>"${item.descripcion}"</div>
                        </option>
                    `);
                }

                selected_proveedor.html(options.join(' '));

                selected_proveedor.select2({
                    width: '100%',
                    dropdownParent: $('#modalGuia')
                });
            }
        })
    }

    function LoadAlmacenes() {

        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=NAlmacen&&action=ListAlmacen",
            dataType: "json",
            success: function (data) {

                var select_almacen = $('#cmb_Almacen');

                var options = [];

                for (let item of data) {
                    options.push(`
                        <option value="${item.id}">
                            ${item.nombre}
                        </option>
                    `);
                }

                select_almacen.html(options.join(' '));
            }
        })
    }
   
    //Modal de Ver Detalles
    
    //Modal de Edición
    function modalEditarGuia($pkGuia, $numCom,$fecha,$procedencia, $id_prov){
        $('#modalGuia').modal('show'); 

        $('#tituloModalGuia').html('Editar Guia...')  ;
        $('#txtIdGuia').val($pkGuia);
        // $('#cmb_TipoComprobante').val($tipoCom);
        $('#NroComprobante').val($numCom);
        $('#txtFecha').val($fecha);
        $('#cmb_Almacen').val($procedencia);
        $('#cmb_Proveedor').val($id_prov);

        idEditar = $pkGuia;
        parametro = 2;
        url="<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=EditIngreso";
    }
      
    function modalverMasDetalles($pkcomprobante,$nrocomprobante,$tipocomprobante,$descripcion,$fecha,$fechamodificacion,$sucursalprocedencia,$ruc,$razon,$trapertura,$trmodificacion)
    {
        $('#modalMasDetalles').modal('show');
        $('#tituloModalDetalleMas').html('Informacion mas Detallada');
        $('#pk').html($pkcomprobante);
        $('#tipo').html($tipocomprobante);
        $('#tguia').html($descripcion);
        $('#nguia').html($nrocomprobante);
        $('#fregistro').html($fecha);
        $('#fmodificacio').html($fechamodificacion);
        $('#empresa').html($sucursalprocedencia);
        $('#ruc').html($ruc);
        $('#rsocial').html($razon);
        $('#registro').html($trapertura);
        $('#modificado').html($trmodificacion);
    }    
    
    //Modal de Registro
    function modalRegistrarGuia(){
        $('#modalGuia').modal('show'); 
        $('#tituloModalGuia').html('Registrar Guia...')  ;
        $('#cmb_TipoComprobante').val("1");
        $('#NroComprobante').val("");
        $('#Procedencia').val("");
        $('#ruc').val("");
        $('#proveedor').val("");
        parametro = 1;
        url="<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=AddIngreso";
    }
    
    function hallarUltimoId(){
    <?php
    $db = new SuperDataBase();
    $query = "select pkComprobante from comprobante_ingreso order by 1 desc limit 1;";
    $result = $db->executeQuery($query);
    $ultimoId=0;
            while ($row = $db->fecth_array($result)) {
            $ultimoId = $row['pkComprobante']; 
            }
            ?>
     }
     
    //Acción para el modal de registro y edición
    function guardarGuia(ev){
        $('#modalGuia').modal('hide');
        $.post( url, $('#formGuia').serialize(),        
        function( data ) {           
            data = JSON.parse(data);
            if (data.id) {
                alert('Guia de Ingreso registrada.');
                verDetalles(data.id);
            }
        });        
         
        return false;  
    }
    
    //Acción para ver los detalles de la guía
    function verDetalles($pkGuia){
        window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&action=AdminDetalleGuias&Id='+$pkGuia,'_self');
    }
    
    //Modal de Eliminación
    function modalEliminarGuia($id){
        $('#modalGuia2').modal('show'); 
        $('#tituloModalGuia2').html('Eliminando Guia')  ;
        $('#txtMensajeeliminar').html('¿Seguro que desea anular esta Guia?');
        $('#txtIdGuia2').val($id); 
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-danger');
        url="<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=DeleteMovimiento";
    }
    
    //Modal de Habilitación
    function modalActivarGuia($id){
        $('#modalGuia2').modal('show'); 
        $('#tituloModalGuia2').html('Habilitando Guia')  ;
        $('#txtMensajeeliminar').html('¿Seguro que desea habilitar el Guia Seleccionado?');
        $('#txtIdGuia2').val($id); 
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-primary');
        url="<?php echo class_config::get('urlApp') ?>/?controller=Comprobante&action=ActiveGuia";
    }
    
    //Acción para el modal de habilitar y deshabilitar
    function deleteGuia(){
        $.post( url, {id:$('#txtIdGuia2').val()},
        function( data ) {
            location.reload();
        });
        $('#modalGuia2').modal('hide');          
    }
    
    function NuevoProveedor() {
        window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&action=ShowAdminProveedor','_blank');
    }
    
    
    // funcion para validar solo numeros
    function soloNumeros(e)
    {
	var key = window.Event ? e.which : e.keyCode
	return (key >= 48 && key <= 57)
    }

    // funcion para validar solo letras
    function soloLetras(e){
        key = e.keyCode || e.which;
        tecla = String.fromCharCode(key).toLowerCase();
        letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
        especiales = "8-37-39-46";

        tecla_especial = false
        for(var i in especiales){
            if(key == especiales[i]){
                tecla_especial = true;
                break;
            }
        }

        if (letras.indexOf(tecla) == -1 && !tecla_especial) {
            return false;
        }
    }

    function sendPrint(id) {

        $.post("<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=SendPrint", {id},
        function() {
            alert('Impresión enviada');
            // location.reload();
        });
    }
    