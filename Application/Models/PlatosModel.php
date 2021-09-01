<?php
error_reporting(E_ALL);

class Application_Models_PlatosModel {

    private $_pkPlato, $_descripcion, $_estado, $_precio, $_pkTipo,$_stockMinimo,$_pkCategoria;

    function __construct() {
        
    }
    
    function get_pkCategoria() {
        return $this->_pkCategoria;
    }

    function set_pkCategoria($_pkCategoria) {
        $this->_pkCategoria = $_pkCategoria;
    }

        function get_stockMinimo() {
        return $this->_stockMinimo;
    }

    function set_stockMinimo($_stockMinimo) {
        $this->_stockMinimo = $_stockMinimo;
    }

        public function get_pkPlato() {
        return $this->_pkPlato;
    }

    public function get_descripcion() {
        return $this->_descripcion;
    }

    public function get_estado() {
        return $this->_estado;
    }

    public function get_precio() {
        return $this->_precio;
    }

    public function set_pkPlato($_pkPlato) {
        $this->_pkPlato = $_pkPlato;
    }

    public function set_descripcion($_descripcion) {
        $this->_descripcion = $_descripcion;
    }

    public function set_estado($_estado) {
        $this->_estado = $_estado;
    }

    public function set_precio($_precio) {
        $this->_precio = $_precio;
    }

    public function get_pkTipo() {
        return $this->_pkTipo;
    }

    public function set_pkTipo($_pkTipo) {
        $this->_pkTipo = $_pkTipo;
    }

    public function filtro_plato($valor, $categoria) {
        $db = new SuperDataBase();
        $where = "";
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT pkCategoria, pkPlatoSucursal,tp.pkTipo,precioVenta, pl.pkPlato, pkSucursal, pl.descripcion FROM plato_sucursal p inner join plato pl on pl.pkPlato=p.pkPlato
 inner join tipos tp on (tp.pkTipo=p.pkTipo) where pkSucursal='$sucursal' ";

//        Inicializamos el filtro

        if ($valor != "") {
            $query = $query . " and pl.descripcion like '%$valor%'";
        }

        if ($categoria != "0") {
            $query = $query . " and pkCategoria=$categoria";
        }
//die( $query);
        $result = $db->executeQuery($query);
        if (extension_loaded('zlib')) {
            ob_start('ob_gzhandler');
        } header("Content-type: text/javascript");
        $array = array();

        while ($row = $db->fecth_array($result)) {
            $array[] = array("pkPlatoSucursal" => $row['pkPlatoSucursal'],
                "pkPlato" => $row['pkPlato'],
                "descripcion_plato" => utf8_encode($row['descripcion']),
                "precioVenta" => $row['precioVenta'],
//                "nomcategoria" => $row['c.descripcion'],
//                "nomTipo" => $row['tipo'],
                "categoria" => $row['pkCategoria'],
                "pkTipo_platos" => $row['pkTipo'],
//                "pkPlatoSucursal" => $row['pkPlatoSucursal'],
            );
        }
        echo json_encode($array);
        if (extension_loaded('zlib')) {
            ob_end_flush();
        }
    }

    public function listar() {
        $db = new SuperDataBase();
        $where = "";
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT pkPlato, descripcion, estado, pktipo, precio_venta, pkSucursal_, stock, pkCategoria, personal, mediano, familiar FROM plato pl 
  where pl.estado=0 and pkSucursal_='$sucursal'";
//die($query)

        $result = $db->executeQuery($query);

        $array = array();

        while ($row = $db->fecth_array($result)) {
            $array[] = array("pkPlato" => $row['pkPlato'],
                "descripcion" => utf8_encode($row['descripcion']),
                "label" => utf8_encode($row['descripcion']),
                "estado" => $row['estado'],
                "stock" => $row['stock'],
                "pkCategoria" => $row['pkCategoria'],
                "personal" => $row['personal'],
                "mediano" => $row['mediano'],
                "familiar" => $row['familiar'],
                "pktipo" => $row['pktipo'],
                "precio_venta" => $row['precio_venta'],
                "pkSucursal_" => $row['pkSucursal_'],
            );
        }
        echo json_encode($array);
    }

    /**
     * Funcion que agrega cantidad al stock de un producto por sucursal
     * @param String $pkProduct Identificador del producto;
     * @param Int $cantidad cantidad que se la a agregar
     * @param $comentario $comentario Describe la razon por la cual se le esta agregando el producto
     */
    public function addNuevoPlato($descripcion, $tipo, $precio) {
        $db = new SuperDataBase();
        // $comentario = utf8_decode($comentario);
        $sucursal = UserLogin::get_pkSucursal();
        //$user=  UserLogin::get_id();
        $query = "Call sp_addNuevoPlato('$descripcion',$tipo,$precio,'$sucursal',@sa)";
//        die($query);
        $db->executeQuery($query);
        $query2 = "select @sa;";
        $result = $db->executeQuery($query2);
        $mensaje = "";
        while ($row = $db->fecth_array($result)) {
            $mensaje = $row['@sa'];
        }
        return $mensaje;
    }

public function _saveplato($tipo_sunat,$tipo_impuesto,$tipo_articulo) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $sp = "CALL sp_addNuevoPlato('$this->_descripcion', $this->_pkTipo,$this->_pkCategoria,$this->_precio, '$sucursal',$this->_stockMinimo,@sa);";
     
        $db->executeQuery($sp);
        $query = "Select @sa";
        $result = $db->executeQuery($query);
        $valor = "";
        while ($row = $db->fecth_array($result)) {
            $valor = $row['@sa'];
        }
        
        $db->executeQuery("Insert into plato_codigo_sunat values(NULL,'".$valor."','".$tipo_sunat."','".$tipo_impuesto."','".$tipo_articulo."')");
        
        return $valor;
    }

    public function _updateplato($id,$tipo_sunat,$tipo_impuesto,$tipo_articulo) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $sp = "CALL sp_UpdatePlato('$id','$this->_descripcion', $this->_pkTipo,$this->_precio,$this->_stockMinimo ,'$sucursal');";
        
        $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
        $db->executeQuery("Delete from plato_codigo_sunat where id_plato = '".$id."'");
        $db->executeQuery("Insert into plato_codigo_sunat values(NULL,'".$id."','".$tipo_sunat."','".$tipo_impuesto."','".$tipo_articulo."')");
        $db->executeQuery("SET SQL_SAFE_UPDATES = 1;");
        
        $db->executeQuery($sp);
        echo $sp;
    }

    public function deletePlato($pkPlato) {
        $db = new SuperDataBase();
        $user = new UserLogin();
        //$id = $user->get_idTrabajador();
        $query = "CALL sp_delete_Plato('$pkPlato');";
        $db->executeQuery($query);
        echo $query;
    }

    public function listId($pkPlato) {
        $db = new SuperDataBase();
      
        $query = "SELECT *,p.descripcion as plato FROM plato p ,tipos t ,categoria c where p.pkPlato='$pkPlato' AND p.pktipo = t.pkTipo AND p.pkCategoria = c.pkCategoria";
        
        $result = $db->executeQuery($query);
        
        $result_sunat = $db->executeQuery("SELECT cs.id, cs.descripcion, pc.id_tipo_impuesto, pc.tipo_articulo  from plato_codigo_sunat pc, codigo_sunat cs where pc.id_plato = '".$pkPlato."' AND pc.id_codigo_sunat = cs.id");
        
        $array = array();
        while ($row= $db->fecth_array($result)){
            $id_sunat = "";
            $descripcion_sunat = "";
            $tipo_impuesto = "";
            $tipo_articulo = "";
            if($row1 = $db->fecth_array($result_sunat)){
                $id_sunat = $row1["id"];
                $descripcion_sunat = $row1["descripcion"];
                $tipo_impuesto = $row1["id_tipo_impuesto"];
                $tipo_articulo = $row1["tipo_articulo"];
            }
            
            $array[] = array("id" => $row['pkPlato'],
                "descripcion"=>  utf8_encode($row['plato']),
                "stockMinimo"=>$row['stockMinimo'],
                "pkTipo"=>$row['pkTipo'],
                "pkCategoria"=>$row['pkCategoria'],
                "precioVenta"=>$row['precio_venta'],
                'id_sunat' => $id_sunat,
                'descripcion_sunat' => utf8_encode($descripcion_sunat),
                'tipo_impuesto' => $tipo_impuesto,
                'tipo_articulo' => $tipo_articulo
                );
        }
        echo json_encode($array);
    }

}
