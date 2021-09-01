<?php

Class Application_Views_PedidosView{
    
    public function __construct() {
    }
    
    public function showPedidos(){
        include_once 'Pedidos/Pedidos3.php';
    }
    public function showPedidos2(){
        include_once 'Pedidos/pedidos2.php';
    }
    public function showPedidosApp(){
        include_once 'Pedidos/pedidosApp.php';
    }
    public function showCocina(){
        include_once 'Pedidos/VerPedidosCocina.php';
    }
    public function showBar(){
        include_once 'Pedidos/VerPedidoBar.php';
    }
    public function showMesas(){
        include_once 'Pedidos/ShowMesas.php';
    }
    public function showPedidoPorPagar(){
        include_once 'Pedidos/PedidoPorPagar.php';
    }

   
   
}
?>
