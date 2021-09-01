<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_MesaModel {

    private $_pkMesa, $_descripcionMesa, $_pkSalon;

    function __construct() {
        
    }
    
    //Funcion para obtener una mesa vacia
    public function getMesa($pkSalon){
        $db = new SuperDataBase();
        //Obtenemos Fecha de cierre
        $query_cierre = "Select fecha from cierrediario where pkCierreDiario = 1";
        $rc = $db->executeQuery($query_cierre);
        $fecha_cierre_c = null;
        if ($row = $db->fecth_array($rc)){
            $fecha_cierre_c = $row["fecha"];
        }

        //Obtenemos Ultima mesa abierta
        $last_id = 0;
        //$query00 = "SELECT * FROM mesas where pkSalon = '".$pkSalon."' AND estado = 1 order by pkMesa DESC LIMIT 1";
        $query00 = "SELECT max(p.pkMesa) as max_mesa from pedido p, mesas m where p.pkMesa = m.pkMesa AND m.pkSalon = '".$pkSalon."' AND p.fechaCierre = '".$fecha_cierre_c."'";
        //echo $query00;
        $result00 = $db->executeQuery($query00);
        if ($row00 = $db->fecth_array($result00)) {
            $last_id = intval($row00["max_mesa"]);
        }
             
        $query = "SELECT * FROM mesas where pkSalon = '".$pkSalon."' AND estado = 0 AND pkMesa > ".$last_id." order by pkMesa ASC LIMIT 1";

        //echo $query;
        
        $result = $db->executeQuery($query);

        $array = array();
        if ($row = $db->fecth_array($result)) {
            $array[] = array(
                "nombre" => utf8_encode($row['nmesa']),
                "mesa" => $row['pkMesa']
            );
        }else{
            //Numero Mesa
            $query0 = "Select count(*) as cantidad from mesas where pkSalon = '".$pkSalon."'";
            $result0 = $db->executeQuery($query0);
            $row0 = $db->fecth_array($result0);
            $numero_mesa = intval($row0["cantidad"])+1;
            //Nombre Salon
            $query1 = "Select * from salon where pkSalon = '".$pkSalon."'";
            $result1 = $db->executeQuery($query1);
            $row1 = $db->fecth_array($result1);
            $nombre_mesa = $row1["nombre"];
            //Insertamos nueva mesa
            $query2 = "Insert into mesas values(NULL,'".$nombre_mesa." ".str_pad($numero_mesa, 2, "0", STR_PAD_LEFT)."','0','".$pkSalon."')";
            $db->executeQuery($query2);
            $query3 = "SELECT * FROM mesas where pkSalon = '".$pkSalon."' AND estado = 0 order by pkMesa DESC LIMIT 1";
            $result3 = $db->executeQuery($query3);
            if ($row3 = $db->fecth_array($result3)) {
                $array[] = array(
                    "nombre" => utf8_encode($row3['nmesa']),
                    "mesa" => $row3['pkMesa']
                );
            }
        }
        return $array;
    }

    public function get_pkMesa() {
        return $this->_pkMesa;
    }

    public function get_descripcionMesa() {
        return $this->_descripcionMesa;
    }

    public function get_pkSalon() {
        return $this->_pkSalon;
    }

    public function set_pkMesa($_pkMesa) {
        $this->_pkMesa = $_pkMesa;
    }

    public function set_descripcionMesa($_descripcionMesa) {
        $this->_descripcionMesa = $_descripcionMesa;
    }

    public function set_pkSalon($_pkSalon) {
        $this->_pkSalon = $_pkSalon;
    }

    
      public function ActiveMesa($id) {
        $db = new SuperDataBase();
        $query =    "update mesas 
                    set estado=0
                    where pkMesa=".$id;
        $db->executeQuery($query);
    }
    
    public function deleteMesa($id) {
        $db = new SuperDataBase();
        $query =    "update mesas 
                    set estado=3
                    where pkMesa=".$id;
        $db->executeQuery($query);
    }
    
    
    
    public function _listModulos() {
        $db = new SuperDataBase();
//        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT * FROM module m;";
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "idmodule" => $row['pkModule'],
                "nombre_Module" => utf8_encode($row['nameModule'])
//                "nombre" => $row['precio_venta']
            );
        }
        echo json_encode($array);
    }
    
    public function _listSubModulos($IdModulo) {
        $db = new SuperDataBase();
//        $sucursal = UserLogin::get_pkSucursal();
        $query = "select pkSubModule,nameSubModule from module m inner join submodule s on m.pkModule=s.fkModule where pkModule=$IdModulo ;";
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "id" => $row['pkSubModule'],
                "nombre_SubModule" => utf8_encode($row['nameSubModule'])
//                "nombre" => $row['precio_venta']
            );
        }
        echo json_encode($array);
    }
    
    
   public function CrearMesas($_valor,$_IdSalon,$_Prefijo_mesa, $_descripcionMesa,$_valorCantidadMesas, $cantidadMesas,$MesasDesde,$MesasHsta) {
        $_descripcionMesa = utf8_decode($_descripcionMesa);
        $_Prefijo_mesa = utf8_decode($_Prefijo_mesa);
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        if($_valor=='1')
          {
            if($_valorCantidadMesas=='3')
            {
                 $query = "call sp_agregarMesas($_IdSalon,'$_descripcionMesa',$cantidadMesas,'$sucursal')";
            }
            else
                if($_valorCantidadMesas=='4')
                    {
                       $query = "call sp_GenerarRangoMesas($_valor,$_IdSalon,'$_descripcionMesa',$MesasDesde,$MesasHsta)";
                    }
               
          }
         if($_valor=='2')
         {
             if($_valorCantidadMesas=='3')
               {
                 $query = "call sp_modificarSalones($_IdSalon,'$_Prefijo_mesa',$cantidadMesas)";
               }
               else
                 if($_valorCantidadMesas=='4')
                 {
                    $query = "call sp_GenerarRangoMesas($_valor,$_IdSalon,'$_Prefijo_mesa',$MesasDesde,$MesasHsta)";
                 }
            
         }
        
//        $query = "CALL sp_agregarMesas($_IdSalon, '$_descripcionMesa', $cantidadMesas, '$sucursal');";
//        die($query);
        $db->executeQuery($query);
        return $db->getId();
//        echo $query;
    }
    
    public function _listNombreMesas($IdSalon) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "select distinct(SUBSTRING(nmesa,1,LOCATE(' ',nmesa))) as mesa from mesas where pkSalon=$IdSalon";
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "id" => $row['mesa'],
                "nombre_mesa" => utf8_encode($row['mesa'])
//                "nombre" => $row['precio_venta']
            );
        }
        echo json_encode($array);
    }

    public function _listMesas($NombreMesa, $_IDSalon) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "select pkMesa,nmesa from mesas where upper(nmesa) like upper('$NombreMesa%') and pkSalon=$_IDSalon order by 1;";
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "idMesa" => $row['pkMesa'],
                "mesa" => utf8_encode($row['nmesa'])
//                "nombre" => $row['precio_venta']
            );
        }
        echo json_encode($array);
    }

    public function _listMesasID($id) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "select pkMesa,nmesa, pkSalon,estado from mesas where pkMesa='$id'";
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "id" => $row['pkMesa'],
                "nmesa" => utf8_encode($row['nmesa']),
                "pkSalon" => utf8_encode($row['pkSalon']),
                "estado" => utf8_encode($row['estado'])
//                "nombre" => $row['precio_venta']
            );
        }
        echo json_encode($array);
    }

    public function _listSalones() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "select pkSalon,nombre from salon where pkSucursal='$sucursal';";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array("idsalon" => $row['pkSalon'],
                "salon" => utf8_encode($row['nombre'])
//                "nombre" => $row['precio_venta']
            );
        }
        echo json_encode($array);
    }
    

    public function RegistrarMesas($_IdSalon, $_descripcionMesa, $estado) {
        $_descripcionMesa = utf8_decode($_descripcionMesa);
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "insert into mesas ( nmesa, estado, pkSalon) values(upper('$_descripcionMesa'),'$estado','$_IdSalon' )";
//        $query = "CALL sp_agregarMesas($_IdSalon, '$_descripcionMesa', $cantidadMesas, '$sucursal');";
//        die($query);
        $db->executeQuery($query);
        return $db->getId();
//        echo $query;
    }

    public function ModificarMEsa($id, $_IdSalon, $_descripcionMesa, $estado) {
        $_descripcionMesa = utf8_decode($_descripcionMesa);
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "update  mesas set nmesa=upper('$_descripcionMesa'), estado='$estado', pkSalon='$_IdSalon' where pkMesa=$id";
//        $query = "CALL sp_agregarMesas($_IdSalon, '$_descripcionMesa', $cantidadMesas, '$sucursal');";
//        die($query);
        $db->executeQuery($query);
        return "true";
//        echo $query;
    }

    public function RegistrarSalones($_NombreSalon) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_agregarSalones('$_NombreSalon','$sucursal')";

        $db->executeQuery($query);
        echo $query;
    }

    public function ActualizarSalones($_IDSalon, $_descripcionMesa, $cantidadMesas) {

        $db = new SuperDataBase();
//        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_modificarSalones($_IDSalon, '$_descripcionMesa', $cantidadMesas);";

        $db->executeQuery($query);
//        
    }

    public function EliminarMesas($_IDSalon, $_descripcionMesa, $cantidadMesas) {

        $db = new SuperDataBase();
//        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_eliminarMesas($_IDSalon, '$_descripcionMesa', $cantidadMesas);";

        $db->executeQuery($query);
//        
    }

    public function EliminarMesaEspecifica($_IDMesa,$estado) {

        $db = new SuperDataBase();
//        $sucursal = UserLogin::get_pkSucursal();
        $query = "Update mesas set estado=$estado where pkMesa=$_IDMesa ";

        $db->executeQuery($query);
        echo "true";
//        
    }

    public function EliminarSalones($_IDSalon, $_descripcionMesa, $cantidadMesas) {

        $db = new SuperDataBase();
//        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_eliminarMesas($_IDSalon, '$_descripcionMesa', $cantidadMesas);";

        $db->executeQuery($query);
//        
    }

}
