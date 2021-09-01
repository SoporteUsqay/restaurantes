<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_IngresoInsumosModel {

    private $pkIngresoInsumo;
    private $pkSucursal;
    private $_cantidad;
    private $_pkInsumo;
    private $_pkPlato;
    private $_descripcion;
    private $_tipo;

    function __construct() {
        
    }

    function getPkIngresoInsumo() {
        return $this->pkIngresoInsumo;
    }

    function getPkSucursal() {
        return $this->pkSucursal;
    }

    function get_cantidad() {
        return $this->_cantidad;
    }

    function get_pkInsumo() {
        return $this->_pkInsumo;
    }

    function get_pkPlato() {
        return $this->_pkPlato;
    }

    function get_descripcion() {
        return $this->_descripcion;
    }

    function get_tipo() {
        return $this->_tipo;
    }

    function setPkIngresoInsumo($pkIngresoInsumo) {
        $this->pkIngresoInsumo = $pkIngresoInsumo;
    }

    function setPkSucursal($pkSucursal) {
        $this->pkSucursal = $pkSucursal;
    }

    function set_cantidad($_cantidad) {
        $this->_cantidad = $_cantidad;
    }

    function set_pkInsumo($_pkInsumo) {
        $this->_pkInsumo = $_pkInsumo;
    }

    function set_pkPlato($_pkPlato) {
        $this->_pkPlato = $_pkPlato;
    }

    function set_descripcion($_descripcion) {
        $this->_descripcion = $_descripcion;
    }

    function set_tipo($_tipo) {
        $this->_tipo = $_tipo;
    }

    public function _ListDatosDetalleGuia($id) {
        $db = new SuperDataBase();

        $query = "select ing.pkIngresoInsumo,ing.cantidad,ing.precioU,i.descripcioninsumo from ingresoinsumos ing INNER JOIN insumos i ON ing.pkInsumo=i.pkInsumo where pkIngresoInsumo='$id'";
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "IDIngresoInsumo" => $row['pkIngresoInsumo'],
                "insumo" => $row['descripcioninsumo'],
                "Cantidad" => utf8_encode($row['cantidad']),
                "Precio" => utf8_encode($row['precioU'])
//                "nombre" => $row['precio_venta']
            );
        }
        echo json_encode($array);
    }

    public function deletedetalleGuia($id) {
        $db = new SuperDataBase();
//        $objCaja= new Application_Models_CajaModel();
//        $fecha= $objCaja->fechaCierre();        
        $query = "update ingresoinsumos 
                    set estado=1
                    where pkIngresoInsumo=$id";
        $db->executeQuery($query);

        $cantidad = 0;
        $pkInsumo = 0;
        $tipo = 0;
        $fecha = "";
        $query = "SELECT pkInsumo,cantidad, tipo, fecha from ingresoinsumos WHERE pkIngresoInsumo = $id;";
        $result = $db->executeQuery($query);

        while ($row = $db->fecth_array($result)) {
            $cantidad = $row['cantidad'];
            $pkInsumo = $row['pkInsumo'];
            $tipo = $row['tipo'];
            $fecha = $row['fecha'];
        }

        if ($tipo == 1) {
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadFinal = cantidadFinal - $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha >= $fecha;";
            $db->executeQuery($query);
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadInicial = cantidadInicial - $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha > $fecha;";
            $db->executeQuery($query);
        } if ($tipo == 2) {
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadFinal = cantidadFinal + $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha >= $fecha;";
            $db->executeQuery($query);
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadInicial = cantidadInicial + $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha > $fecha;";
            $db->executeQuery($query);
        }
    }

    public function ActivedetalleGuia($id) {
        $db = new SuperDataBase();
//        $objCaja= new Application_Models_CajaModel();
//        $fecha= $objCaja->fechaCierre();        
        $query = "update ingresoinsumos 
                    set estado=0
                    where pkIngresoInsumo=$id";
        $db->executeQuery($query);
        $cantidad = 0;
        $pkInsumo = 0;
        $tipo = 0;
        $fecha = "";
        $query = "SELECT pkInsumo,cantidad,fecha,tipo from ingresoinsumos WHERE pkIngresoInsumo = $id;";
        $result = $db->executeQuery($query);

        while ($row = $db->fecth_array($result)) {
            $cantidad = $row['cantidad'];
            $pkInsumo = $row['pkInsumo'];
            $tipo = $row['tipo'];
            $fecha = $row['fecha'];
        }

        if ($tipo == 1) {
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadFinal = cantidadFinal + $cantidad                                                      
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha >= $fecha;";
            $db->executeQuery($query);
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadInicial = cantidadInicial + $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha > $fecha;";
            $db->executeQuery($query);
        } if ($tipo == 2) {
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadFinal = cantidadFinal - $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha >= $fecha;";
            $db->executeQuery($query);

            $query = "UPDATE historial_stock_insumos
                    SET    cantidadInicial = cantidadInicial - $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha > $fecha;";
            $db->executeQuery($query);
        }
    }

    public function insert() {
        $objCaja = new Application_Models_CajaModel();
        $fecha = $objCaja->fechaCierre();
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $id = UserLogin::get_idTrabajador();
        $query = "insert into ingresoinsumos (cantidad, descripcion, pkInsumo, pkSucursal, fecha, dateModify, pkTrabajador,tipo,estado)"
                . " values($this->_cantidad,upper('$this->_descripcion'),$this->_pkInsumo ,'$sucursal','$fecha',now(),'$id',$this->_tipo),0";
//       echo $query;
        $db->executeQuery($query);
        return $db->getId();
    }

    public function ModificarDatosGuias($pkGuia, $pkIngresoInsumo, $pkInsumo, $cantidad, $precio, $tipo) {

        $fecha = "";
        $db = new SuperDataBase();
//        
//        $query = "SELECT date(fecha) as fecha from comprobante_ingreso WHERE pkComprobante = $pkGuia;";
//        $result = $db->executeQuery($query);        
//        while ($row = $db->fecth_array($result)) {
//            $fecha = $row['fecha'];
//        }

        $sucursal = UserLogin::get_pkSucursal();
        $id = UserLogin::get_idTrabajador();
        $cantidadHistorial = 0;
        $query = "SELECT cantidad,fecha from ingresoinsumos WHERE pkIngresoInsumo = $pkIngresoInsumo;";
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
            $cantidadHistorial = $row['cantidad'];
            $fecha = $row['fecha'];
        }

        $query = "update ingresoinsumos 
                  set pkInsumo = $pkInsumo,
                      cantidad = $cantidad, 
                      dateModify = now(),
                      precioU = $precio, 
                      pkTrabajadorModifica = $id
                  where pkIngresoInsumo=$pkIngresoInsumo;";
        $db->executeQuery($query);

        $query = "update comprobante_ingreso
                  set fechaModificacion = now(),
                      pkTrabajadorEdita = $id
                  where pkComprobante=$pkGuia;";
        $db->executeQuery($query);

        if ($tipo == 1) {
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadFinal = cantidadFinal - $cantidadHistorial + $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha >= '$fecha';";
            $db->executeQuery($query);
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadInicial = cantidadInicial - $cantidadHistorial + $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha > '$fecha';";
            $db->executeQuery($query);
        }if ($tipo == 2) {
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadFinal = (cantidadFinal + $cantidadHistorial) - $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha >= '$fecha';";
            $db->executeQuery($query);
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadInicial = (cantidadInicial + $cantidadHistorial) - $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha > '$fecha';";
            $db->executeQuery($query);
        }
    }

    public function saveDetalle($descripcion, $cantidad, $pkInsumo, $pkComprobante, $precioU, $tipo) {
//        $objCaja= new Application_Models_CajaModel();
//        $fecha= $objCaja->fechaCierre();
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $id = UserLogin::get_idTrabajador();
        $fecha = "";
        $query = "SELECT date(fecha) as fecha from comprobante_ingreso WHERE pkComprobante = $pkComprobante;";
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
            $fecha = $row['fecha'];
        }
        $query = "INSERT INTO ingresoinsumos (descripcion,cantidad, pkInsumo, pkSucursal, fecha, dateModify, pkTrabajador,tipo,estado,pkTrabajadorModifica,pkComprobante,precioU)"
                . " VALUES('$descripcion',$cantidad,$pkInsumo ,'$sucursal','$fecha',now(),$id,$tipo,0,$id,$pkComprobante,$precioU)";
        $db->executeQuery($query);
//        return $db->getId();
        if ($tipo == 1) {
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadFinal = cantidadFinal + $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha >= '$fecha';";
            $db->executeQuery($query);
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadInicial = cantidadInicial + $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha > '$fecha';";
            $db->executeQuery($query);
        }if ($tipo == 2) {
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadFinal = cantidadFinal - $cantidad
                    WHERE  pkInsumo = $pkInsumo
                    AND fecha >= '$fecha';";
            $db->executeQuery($query);
            $query = "UPDATE historial_stock_insumos
                    SET    cantidadInicial = cantidadInicial - $cantidad
                    WHERE  pkInsumo =$pkInsumo
                    AND fecha > '$fecha';";
            $db->executeQuery($query);
        }
    }

    public function _saveInsumoporPlato($idPlato, $descripcion, $cantidad, $estado, $pkcomprobante, $precio) {
        $db = new SuperDataBase();
        $cadena = "";
        if ($estado == 1) {
            $cadena = "or pktipopedido=1";
        } else {
            $cadena = "";
        }
        $query = "SELECT pkplato,descripcioninsumo,ins.pkinsumo as insumo,i.cantidadTotal*$cantidad as cantidad FROM insumo_menu i inner join insumos ins on i.pkinsumo=ins.pkinsumo where pkPlato='$idPlato' and pktipopedido=0 $cadena;";
        $result = $db->executeQuery($query);
        $insumo = "";
        while ($row = $db->fecth_array($result)) {
            $insumo = $row['insumo'];
            $cantidadEnvio = $row['cantidad'];
            $objInsumoPlato = new Application_Models_IngresoInsumosModel();
            $objInsumoPlato->saveDetalle($descripcion, $cantidadEnvio, $insumo, $pkcomprobante, $precio, 2);
        }
    }
    
    public function ModificarGuiasFecha($pkInsumo,$cantidad,$fechaAntigua,$fechaNueva,$tipoGuia) {        
        $db = new SuperDataBase();

        if($tipoGuia == '1' || $tipoGuia == '2' || $tipoGuia == '3'){
            //Si la fecha nueva es menor a la antigua
            if ($fechaNueva < $fechaAntigua) {
                $query = "UPDATE historial_stock_insumos
                        SET    cantidadFinal = cantidadFinal + $cantidad
                        WHERE  pkInsumo =$pkInsumo
                        AND fecha >= '$fechaNueva' AND fecha < '$fechaAntigua';";
                $db->executeQuery($query);
                $query = "UPDATE historial_stock_insumos
                        SET    cantidadInicial = cantidadInicial + $cantidad
                        WHERE  pkInsumo =$pkInsumo
                        AND fecha > '$fechaNueva' AND fecha <= '$fechaAntigua';";
                $db->executeQuery($query);
            }//Fin if
        
            //Si la fecha nueva es mayor a la antigua
            if ($fechaNueva > $fechaAntigua) {
                $query = "UPDATE historial_stock_insumos
                        SET    cantidadFinal = cantidadFinal - $cantidad
                        WHERE  pkInsumo =$pkInsumo
                        AND fecha >= '$fechaAntigua' AND fecha < '$fechaNueva';";
                $db->executeQuery($query);
                $query = "UPDATE historial_stock_insumos
                        SET    cantidadInicial = cantidadInicial  - $cantidad
                        WHERE  pkInsumo =$pkInsumo
                        AND fecha > '$fechaAntigua' AND fecha <= '$fechaNueva';";
                $db->executeQuery($query);
            }//Fin if
        
        }//Fin if
        
        if($tipoGuia == '4'){
            //Si la fecha nueva es menor a la antigua
            if ($fechaNueva < $fechaAntigua) {
                $query = "UPDATE historial_stock_insumos
                        SET    cantidadFinal = cantidadFinal - $cantidad
                        WHERE  pkInsumo =$pkInsumo
                        AND fecha >= '$fechaNueva' AND fecha < '$fechaAntigua';";
                $db->executeQuery($query);
                $query = "UPDATE historial_stock_insumos
                        SET    cantidadInicial = cantidadInicial - $cantidad
                        WHERE  pkInsumo =$pkInsumo
                        AND fecha > '$fechaNueva' AND fecha <= '$fechaAntigua';";
                $db->executeQuery($query);
            }//Fin if
        
            //Si la fecha nueva es mayor a la antigua
            if ($fechaNueva > $fechaAntigua) {
                $query = "UPDATE historial_stock_insumos
                        SET    cantidadFinal = cantidadFinal + $cantidad
                        WHERE  pkInsumo =$pkInsumo
                        AND fecha >= '$fechaAntigua' AND fecha < '$fechaNueva';";
                $db->executeQuery($query);
                $query = "UPDATE historial_stock_insumos
                        SET    cantidadInicial = cantidadInicial  + $cantidad
                        WHERE  pkInsumo =$pkInsumo
                        AND fecha > '$fechaAntigua' AND fecha <= '$fechaNueva';";
                $db->executeQuery($query);
            }//Fin if
        
        }//Fin if      
        
    }//Fin ModificarGuiasFecha
    
    
}//Fin Clase
