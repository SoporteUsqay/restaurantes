<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Application_Views_SaleView {

    public function __construct() {
        
    }

    public function showRegister() {
//        include 'template/header.php';
//        include 'template/body.tpl.php';

        include_once 'Sale/Register.tpl.php';
//        include 'template/Footer.tpl.php';
    }

    public function showPedidos() {
        include_once 'Sale/Pedidos.tpl.php';
    }

    public function showList() {
        include_once 'Sale/List.tpl.php';
    }
    public function showAdminProduct(){
        include_once 'Sale/AdminProduct.php';
    }
    public function showMessages(){
        include_once 'Sale/messages.php';
    }
    public function showAdminBoletas(){
        //include_once 'Sale/PruebaBoletas.php';
        include_once 'Sale/AdminBoletas.php';
    }
    public function showAdminFacturas(){
        include_once 'Sale/AdminFacturasP.php';
    }
    public function showTipoCambio(){
        include_once 'Sale/TipoCambio.php';
    }
    
    public function showIngresarPlatos(){
        
        require_once 'Sale/AdminPlatos.php';
    }
    public function showCuentasPorPagar(){
        
        require_once 'Sale/CuentasPendientes.php';
    }
    public function showTipos(){
        
        require_once 'Sale/AdminTipos.php';
    }
    public function showCategorias(){
        
        require_once 'Sale/Categorias.php';
    }
    public function showPromociones(){
        
        require_once 'Sale/Promociones.php';
    }
    public function showRegistrarPromociones(){
        
        require_once 'Sale/RegistrarPromocion.php';
    }

    //Mostrar reporte cuentas por consumo
    //Tiembla Jeanmarco
    public function showCuentasConsumo(){
        
        require_once 'Sale/Consumos.php';
    }

}
