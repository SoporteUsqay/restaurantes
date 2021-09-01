<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Views_AlmacenView {

    function __construct() {        
    }

    public function showAdministrarInsumo() {
        require_once 'Almacen/AdministrarInsumo.php';
    }

    public function showResumenISInsumos() {
        require_once 'Almacen/ResumenISInsumos.php';
    }

    public function showIngresoSalidaInsumo() {
        require_once 'Almacen/IngresoSalidaInsumos.php';
    }

    public function showProductoFactura() {
        require_once 'Almacen/ProductosFactura.php';
    }

    public function showIngresarProductos() {
        require_once 'Almacen/IngresoProductos.php';
    }

    public function showAdmRecetas() {
        require_once 'Almacen/AdministrarReceta.php';
    }

    public function showResumenISProductos() {
        require_once 'Almacen/ResumenISProductos.php';
    }

    public function showIngresoInsumos() {
        require_once 'Almacen/IngresarInsumo.php';
    }

    public function showAdminProveedor() {
        include_once 'Almacen/Proveedor.php';
    }
    
    public function showAdminGuias() {
        include_once 'Almacen/Guias.php';
    }
    public function showAdminDetalleGuias() {
        include_once 'Almacen/DetalleGuias.php';
    }
    
    public function showAdminGuiaSalida() {
        include_once 'Almacen/GuiaSalida.php';
    }
    
    public function showAdminDetalleGuiaSalida() {
        include_once 'Almacen/DetalleGuiaSalida.php';
    }
    
    public function showAdminUnidades() {
        include_once 'Almacen/AdminUnidades.php';
    }
    
    public function showTipoInsumo() {
        include_once 'Almacen/AdminTipoInsumo.php';
    }
    
    public function showViewInsumoPorcion() {
        include_once 'Almacen/AdministrarInsumoPorcion.php';
    }

}
