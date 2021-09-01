<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_ProductoFacturaModel {

    function __construct() {
        
    }

    /**
     * Listado de los productos con su factura de acuerdo al proveedor
     * 
     */
    public function ListProductosFactura() {

        $db = new SuperDataBase();
        $query = "select fp.pkFactura,razonSocial,pj.ruc,fp.fecha,fp.nroFactura,estado,total from factura_provedor fp inner join provedor p on fp.pkProvedor=p.pkProvedor inner join persona_juridica pj on p.ruc=pj.ruc;";
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
//                , , , pkProducto, pkPlato, mensaje, pkComprobante, estado_pedido, hora_pedido, hora_entrega_pedido
                "pkFactura" => $row['pkFactura'],
                "razonSocial" => utf8_encode($row['razonSocial']),
                "ruc" => $row['ruc'],
                "fecha" => $row['fecha'],
                "nroFactura" => $row['nroFactura'],
                "estado" => $row['estado'],
                "total" => $row['total']
            );
        }
        echo json_encode($array);
    }
   

}
