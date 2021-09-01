<?php require_once '../../Components/Config.inc.php'; ?>
//<script>
    function listadoDepartamentos($nameId) {
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Ubigeo&&action=ListDepartament', function(data) {
            for (var i = 0; i < data.length; i++) {

                $('#'+$nameId).append("<option>" + data[i].description + "</option>")
//            $('#tx+tModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    function _loadProvince($nameIdProvince, $nameIdDistrict) {
        $('#' + $nameIdProvince + ' option').remove();
        $('#' + $nameIdProvince).append("<option value=\"0\">Seleccione una opcion</option>")
        $('#' + $nameIdDistrict + ' option').remove();
        $('#' + $nameIdDistrict).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Ubigeo&&action=ListProvince', {'departament': $('#cmbRegisterDeparment').val()}, function(data) {

//          
            for (var i = 0; i < data.length; i++) {

                $('#' + $nameIdProvince).append("<option>" + data[i].description + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    function _listProducto_Categorias($idProducto,$idCategoria) {
        $('#'+$idCategoria+' option').remove();
        var pro= $('#'+$idProducto).val();
        $('#'+$idCategoria).append("<option value=\"0\">Seleccione una opcion</option>\n\
                                    <option value=\"1\">Todos</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&action=ListCategoria_Producto&pkTipoProducto='+pro, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$idCategoria).append("<option value=\"" + data[i].pkcategoria + "\">" + data[i].descripcion + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    function _loadDistrict() {
        $('#cmbRegisterDisctrict option').remove();
        $('#cmbRegisterDisctrict').append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Ubigeo&&action=ListDistrict', {'province': $('#cmbRegisterProvince').val()}, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#cmbRegisterDisctrict').append("<option value=\"" + data[i].pkUbigeo + "\">" + data[i].description + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    function _loadProductos($id) {
        $('#'+$id+' option').remove();
        $('#'+$id).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Product&action=List', function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$id).append("<option value=\"" + data[i].id + "\">" + data[i].descripcion + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    function _loadProductosProvedor($id,$provedor) {
        $('#'+$id+' option').remove();
        var pro= $('#'+$provedor).val();
        $('#'+$id).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Product&action=ListProductProvedor&pkProvedor='+pro, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$id).append("<option value=\"" + data[i].id + "\">" + data[i].descripcion + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    function _loadTiposCategoria($idCategoria,$idTipo) {
        $('#'+$idTipo+' option').remove();
        var pro= $('#'+$idCategoria).val();
        $('#'+$idTipo).append("<option value=\"\">Seleccione una opcion</option>")
//        $('#'+$idTipo).append("<option value=\"1\">Todos</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&action=List&pkCategoria='+pro, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$idTipo).append("<option value=\"" + data[i].pkTipoPlato + "\">" + data[i].descripcion + "</option>");
           }

        });
    }
    
    function _loadTiposCategoria1($idCategoria,$idTipo) {
        $('#'+$idTipo+' option').remove();
        var pro= $('#'+$idCategoria).val();
        $('#'+$idTipo).append("<option value=\"\">Seleccione una opcion</option>")
                              
//        $('#'+$idTipo).append("<option value=\"1\">Todos</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&action=List&pkCategoria='+pro, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$idTipo).append("<option value=\"" + data[i].pkTipoPlato + "\">" + data[i].descripcion + "</option>");
           }

        });
    }
    
    function _loadProvedor($id) {
        $('#'+$id+' option').remove();
        $('#'+$id).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Provedor&action=List', function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$id).append("<option value=\"" + data[i].id + "\">" + data[i].descripcion + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    
    function _listCategorias($id) {
        $('#'+$id+' option').remove();
        $('#'+$id).append("<option value=''>Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&action=ListCategoria', function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$id).append("<option value=\"" + data[i].id + "\">" + data[i].description + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    function _listCategoriasProductos($id) {
        $('#'+$id+' option').remove();
        $('#'+$id).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&action=ListCategoriaProducto', function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$id).append("<option value=\"" + data[i].id + "\">" + data[i].description + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    
     function _listProducto_Categorias($idProducto,$idCategoria) {
        $('#'+$idCategoria+' option').remove();
        var pro= $('#'+$idProducto).val();
        $('#'+$idCategoria).append("<option value=\"0\">Seleccione una opcion</option>\n\
                                    <option value=\"1\">Todos</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&action=ListCategoria_Producto&pkTipoProducto='+pro, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$idCategoria).append("<option value=\"" + data[i].pkcategoria + "\">" + data[i].descripcion + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    
    function _listTipos($id) {
        $('#'+$id+' option').remove();
        $('#'+$id).append("<option value=\"\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&action=ListTipo', function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$id).append("<option value=\"" + data[i].id + "\">" + data[i].description + "</option>")
}

        });
    }

function _listTrabajador($idTipoTrabajador,$idtrabajador) {
        $('#'+$idtrabajador+' option').remove();
        var pro= $('#'+$idTipoTrabajador).val();
        $('#'+$idtrabajador).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Mozo&action=List&pkTipoTrabajador='+pro, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$idtrabajador).append("<option value=\"" + data[i].id + "\">" + data[i].nombres + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    function _listTipoTrabajador($id) {
        $('#'+$id+' option').remove();
        $('#'+$id).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Mozo&action=ListtipoTrabajador', function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$id).append("<option value=\"" + data[i].idTipoTrabajador + "\">" + data[i].descripcionTipoTrabajador + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }

// funcion para listar todos los salones
    function _listSalones($id) {
        $('#'+$id+' option').remove();
        $('#'+$id).append("<option value=\"0\">Seleccione una opcion</option>")

        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Config&action=ListSalones', function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$id).append("<option value=\"" + data[i].idsalon + "\">" + data[i].salon + "</option>")

            }

       });
    }

//funcion para listar los nombres de las mesas segun el salon seleccionado
    function _listNombreMesa($idsalon,$idmesa) {
        $('#'+$idmesa+' option').remove();
        var pro= $('#'+$idsalon).val();
        $('#'+$idmesa).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Config&action=ListNombreMesas&pkIdSalon='+pro, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$idmesa).append("<option value=\"" + data[i].id + "\">" + data[i].nombre_mesa + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    
    // funcion para listar todas las mesas segun el nombre de la mesa y eñ Id del Salon
    function _listMesas($NombreMesa,$mesas,eliminar_cmb_Salon) {
        $('#'+$mesas+' option').remove();
        var pro= $('#'+$NombreMesa).val();
        var Idsalon= $('#'+eliminar_cmb_Salon).val();
        $('#'+$mesas).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Config&action=ListMesas&NombreMesa='+pro+ "&PkSalon=" +Idsalon, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$mesas).append("<option value=\"" + data[i].idMesa + "\">" + data[i].mesa + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }


function _listCategoriasSucursal($id) {
    $('#'+$id+' option').remove();
    $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&action=ListCategoriaSucursal', function(data) {
        for (var i = 0; i < data.length; i++) {
            $('#'+$id).append("<option value=\"" + data[i].id + "\">" + data[i].description + "</option>");
        }
    });
}