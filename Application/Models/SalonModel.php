<?php

class Application_Models_SalonModel {
    private $_pkSalon, $_nombre, $pk_Sucursal;
    function __construct() {
        
        
    }
    public function get_pkSalon() {
        return $this->_pkSalon;
    }

    public function get_nombre() {
        return $this->_nombre;
    }

    public function get_pkSucursal() {
        return $this->_pkSucursal;
    }
    
    public function set_pkSalon($_pkSalon) {
        $this->_pkSalon = $_pkSalon;
    }

    public function set_nombre($_nombre) {
        $this->_nombre = $_nombre;
    }
    
    public function set_pkSucursal($_pkSurcursal) {
        $this->_pkSucrusal = $_pkSucursal;
    }

    public function registrarSalon() {
       
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_agregarSalones('$this->_nombre','$sucursal')";
        echo $query;
        $db->executeQuery($query);
        $query = "select @sa";
        $result = $db->executeQuery($query);
        $mensaje;
        while ($row = $db->fecth_array($result)) {
            $mensaje = $row['@sa'];
        }
        return $mensaje;
    }
    
    public function QuitaSalonCaja($salon,$caja) {
        $db = new SuperDataBase();
        $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
        $db->executeQuery("Delete from accion_caja where pk_accion = '".$salon."' AND tipo_accion = 'SAL' AND caja = '".$caja."'");
        $db->executeQuery("SET SQL_SAFE_UPDATES = 1;");
        echo "<script>window.location.href='" . Class_config::get('urlApp') . "?controller=Config&action=AdmSalon'</script>";
    }
    
    public function PonSalonCaja($salon,$caja) {
        $db = new SuperDataBase();
        $db->executeQuery("Insert into accion_caja values(NULL,'".$salon."','SAL','".$caja."')");
        echo "<script>window.location.href='" . Class_config::get('urlApp') . "?controller=Config&action=AdmSalon'</script>";
    }
    
     public function updateSalon($id,$nombre) {
       
        $db = new SuperDataBase();
        $nombre= utf8_decode($nombre);
        $query = "update salon set nombre =upper('$nombre') where pkSalon=$id";
        $db->executeQuery($query);
        echo $query;
       // return $mensaje;
    }
    
    public function deleteSalon($id) {
       
        $db = new SuperDataBase();
        $query = "update salon set estado ='1' where pkSalon=$id";
        $db->executeQuery($query);
        echo $query;
       // return $mensaje;
    }
    
    public function activeSalon($id) {
       
        $db = new SuperDataBase();
        $query = "update salon set estado ='0' where pkSalon=$id";
        $db->executeQuery($query);
        echo $query;
       // return $mensaje;
    }
    
  }