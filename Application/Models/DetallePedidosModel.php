<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_DetallePedidosModel {
    function __construct() {    
        
    }

    /**
     * Cambia el estado de un pedidoe
     */
    public function updateEstados($pkDetallePedido, $estado) {
        $db = new SuperDataBase();
        $pkCocinero = UserLogin::get_idTrabajador();
        $query = "update detallepedido set estado=$estado, horaTermino=now(), pkCocinero='$pkCocinero' where pkDetallePedido=$pkDetallePedido";
        $db->executeQuery($query);
//        echo $query;
    }

}
