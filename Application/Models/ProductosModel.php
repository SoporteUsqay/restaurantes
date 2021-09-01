<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_ProductosModel {

    private $_pkProductoSucursal, $_pkProducto, $_descripcion, $_stock, $_precioVenta, $_pkTipo, $_precioCompra;

    function __construct() {
        
    }

    public function get_pkProductoSucursal() {
        return $this->_pkProductoSucursal;
    }

    public function set_pkProductoSucursal($_pkProductoSucursal) {
        $this->_pkProductoSucursal = $_pkProductoSucursal;
    }

    public function get_pkProducto() {
        return $this->_pkProducto;
    }

    public function get_descripcion() {
        return $this->_descripcion;
    }

    public function get_stock() {
        return $this->_stock;
    }

    public function get_precioVenta() {
        return $this->_precioVenta;
    }

    public function get_pkTipo() {
        return $this->_pkTipo;
    }

    public function get_precioCompra() {
        return $this->_precioCompra;
    }

    public function set_pkProducto($_pkProducto) {
        $this->_pkProducto = $_pkProducto;
    }

    public function set_descripcion($_descripcion) {
        $this->_descripcion = $_descripcion;
    }

    public function set_stock($_stock) {
        $this->_stock = $_stock;
    }

    public function set_precioVenta($_precioVenta) {
        $this->_precioVenta = $_precioVenta;
    }

    public function set_pkTipo($_pkTipo) {
        $this->_pkTipo = $_pkTipo;
    }

    public function set_precioCompra($_precioCompra) {
        $this->_precioCompra = $_precioCompra;
    }

    /**
     * Listado de todos los producto
     * * */
    public function listAllProduct($tipo, $page, $size, $acceso_rapido = '') {
        error_reporting(E_ALL);
        $db = new SuperDataBase();

        //Obtenemos impuesto de bolsa para este año
        $monto_icbper = 0;
        $query_icbper = "Select * from cloud_config where parametro = 'icbper'";
        $result_i = $db->executeQuery($query_icbper);

        if($row = $db->fecth_array($result_i)){
            $monto_icbper = floatval($row["valor"]);
        }

        $where = "";
        if ($tipo != "") {
            $where = " and tp.pkTipo=" . $tipo . " ";
        }
        
        if ($page != "") {
          $page=  " limit $page";
        }
        if ($size != "") {
         $size=   ", $size";
        }
        if ($acceso_rapido != "" && $acceso_rapido != "()") {
            $where = " and pl.pkPlato in " . $acceso_rapido;
        }

        $query = "Select tp.pkTipo, pl.precio_venta, pl.pkPlato, pl.descripcion from plato pl, tipos tp where pl.pkTipo = tp.pkTipo AND pl.estado = 0 ".$where." ORDER BY pl.descripcion ASC ".$page.$size;

        //echo $query;

        $result = $db->executeQuery($query);

        $array = array();

        while ($row = $db->fecth_array($result)) {
            //Vemos si tiene Stock
            $stock = -1;
            $rstock = $db->executeQuery("Select * from plato_stock where CONVERT(id_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$row["pkPlato"]."' USING utf8) COLLATE utf8_spanish2_ci");
            if($row1 = $db->fecth_array($rstock)){
                $stock = intval($row1["stock"]);
            }

            //Verificamos el impuesto de la bolsa plastica
            $impuesto = 0;
            $rstock = $db->executeQuery("Select * from plato_codigo_sunat where id_plato = '".$row["pkPlato"]."'");
            if($row1 = $db->fecth_array($rstock)){
                if(intval($row1["id_tipo_impuesto"]) === 5){
                    $impuesto = $monto_icbper;
                }
            }


            $array[] = array("id" => $row['pkPlato'],
                "descripcion" => $row['descripcion'],
                "label" => $row['descripcion'] . " - " . (floatval($row['precio_venta'])+$impuesto),
                "value" => $row['pkPlato'],
                "precio" => (floatval($row['precio_venta'])+$impuesto),
                "tpedido" => 'pkPlato',
                "stock" => $stock
            );
        }
        echo json_encode($array);
    }
    
    public function listAllTipoMenu() {
        $db = new SuperDataBase();

        $query = "Select * from tipo_menu where estado = 1";

        $result = $db->executeQuery($query);

        $array = array();

        while ($row = $db->fecth_array($result)) {
            $array[] = array("id" => $row['id'],
                "nombre" => utf8_encode($row['nombre']),
                "precio" => $row['precio']
            );
        }
        echo json_encode($array);
    }
    
    public function componentesPorTipo($idi) {
        $tipos = array();
        
        $db = new SuperDataBase();

        $query = "Select * from tipo_componente_menu where estado = 1";

        $result = $db->executeQuery($query);

        if(!empty($result)){
            while ($row = $db->fecth_array($result)) {
                $tmp = array();
                $tmp["nombre"] = $row["nombre"];
                $pls = array();
                $r1 = $db->executeQuery("Select cm.id, p.pkPlato, p.descripcion, cm.precio, cm.stock from componente_menu cm, plato p where cm.id_tipo_componente_menu = '".$row["id"]."' AND cm.id_tipo_menu = '".$idi."' AND cm.fecha_inicio <= '".date("Y-m-d")."' AND cm.fecha_fin >= '".date("Y-m-d")."' AND cm.estado = 1 AND binary cm.pk_plato = binary p.pkPlato");
                if(!empty($r1)){
                    while ($row1 = $db->fecth_array($r1)) {
                        $pls[] = $row1;
                    }
                }
                $tmp["platos"] = $pls;
                $tipos[] = $tmp;
            }
        }
        
        echo json_encode($tipos);
    }
    
    
    public function listAllProductDescripcion($tipo, $page, $size) {
        error_reporting(E_ALL);
        $db = new SuperDataBase();
        
        //Obtenemos impuesto de bolsa para este año
        $monto_icbper = 0;
        $query_icbper = "Select * from cloud_config where parametro = 'icbper'";
        $result_i = $db->executeQuery($query_icbper);

        if($row = $db->fecth_array($result_i)){
            $monto_icbper = floatval($row["valor"]);
        }

        $where = "";
        if ($tipo != "") {
            $where = " AND pl.descripcion like  '%" . $tipo . "%' ";
        }

        $query = "Select tp.pkTipo, pl.precio_venta, pl.pkPlato, pl.descripcion from plato pl, tipos tp, accion_caja ac where pl.pkTipo = tp.pkTipo AND pl.estado = 0 AND ac.pk_accion = tp.pkTipo AND ac.tipo_accion = 'TYP' AND ac.caja = '".$_COOKIE['c']."' ".$where;

        $result = $db->executeQuery($query);

        $array = array();

        while ($row = $db->fecth_array($result)) {
            //Vemos si tiene Stock
            $stock = -1;
            $rstock = $db->executeQuery("Select * from plato_stock where CONVERT(id_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$row["pkPlato"]."' USING utf8) COLLATE utf8_spanish2_ci");
            if($row1 = $db->fecth_array($rstock)){
                $stock = intval($row1["stock"]);
            }

            //Verificamos el impuesto de la bolsa plastica
            $impuesto = 0;
            $rstock = $db->executeQuery("Select * from plato_codigo_sunat where id_plato = '".$row["pkPlato"]."'");
            if($row1 = $db->fecth_array($rstock)){
                if(intval($row1["id_tipo_impuesto"]) === 5){
                    $impuesto = $monto_icbper;
                }
            }

            $array[] = array("id" => $row['pkPlato'],
                "descripcion" => utf8_encode($row['descripcion']),
                "label" => utf8_encode($row['descripcion'] . " - " . (floatval($row['precio_venta'])+$impuesto)),
                "value" => utf8_encode($row['pkPlato']),
                "precio" => (floatval($row['precio_venta'])+$impuesto),
                "tpedido" => 'pkPlato',
                "stock" => $stock
            );
        }
        echo json_encode($array);
    }

    public function _saveprodu() {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_addNuevoProducto('$this->_descripcion', $this->_pkTipo, $this->_precioVenta, $this->_precioCompra, $this->_stock, '$sucursal',@sa);";

        $db->executeQuery($query);
//        $query2= "select @sa;";
//        $result= $db->executeQuery($query2);
//        $mensaje="";
//        while ($row= $db->fecth_array($result)){
//            $mensaje=$row['@sa'];
//        }
//        return $mensaje;
//        $db->executeQuery($sp);
//        echo $sp;
//        echo "true";
    }

    public function _updateprodu() {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $sp = "CALL sp_update_Producto('$this->_pkProducto','$this->_descripcion', $this->_pkTipo, $this->_precioVenta, $this->_precioCompra, $this->_stock, '$sucursal');";

        $db->executeQuery($sp);
        echo $sp;
//        echo "true";
    }

    public function addNuevoProducto($_descripcion, $tipo, $precioventa, $preciocompra, $stock) {
        $db = new SuperDataBase();
        // $comentario = utf8_decode($comentario);
        $sucursal = UserLogin::get_pkSucursal();
//        $user=  UserLogin::get_id();
        $query = "Call sp_addNuevoProducto('$_descripcion',$tipo,$precioventa,$preciocompra,$stock,'$sucursal',@sa)";
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

    /**
     * Listado  los producto
     * * */
    public function listProduct($valor, $tipo) {
        $db = new SuperDataBase();
        $where = "";
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT pkCategoria,pkProductoSucursal ,tp.descripcion as tipo, pkSucursal,  tp.pkTipo, stock, precioVenta, precioCompra, p.pkProducto, pr.descripcion FROM producto_sucursal p inner join productos pr on pr.pkProducto=p.pkProducto
 inner join tipos tp on (tp.pkTipo=p.pkTipo) where pkSucursal='$sucursal'";

        if ($valor != "") {
            $query = $query . " and pr.descripcion like '%$valor%'";
        }

        if ($tipo != "") {
            $query = $query . " and tp.pkTipo=$tipo";
        }

        $result = $db->executeQuery($query);
        if (extension_loaded('zlib')) {
            ob_start('ob_gzhandler');
        } header("Content-type: text/javascript");
        $array = array();
//        die( $query);
        while ($row = $db->fecth_array($result)) {
            $array[] = array("pkProductoSucursal" => $row['pkProductoSucursal'],
                "pkProducto" => $row['pkProducto'],
                "descripcion_producto" => utf8_encode($row['descripcion']),
                "precioVenta" => $row['precioVenta'],
                "precioCompra" => $row['precioCompra'],
                "stock" => $row['stock'],
                "nombcategoria" => $row['pkCategoria'],
                "pkTipocatg" => $row['pkTipo'],
            );
        }
        echo json_encode($array);
        if (extension_loaded('zlib')) {
            ob_end_flush();
        }
    }

    /**
     * Listado  los producto
     * * */
    public function BusquedaProductos($tipo, $value) {
        $db = new SuperDataBase();
        $where = "";

        $query = "SELECT * FROM productos p inner join tipos tp on (tp.pkTipo=p.pkTipo) ";
        switch ($tipo) {
            case "1":
                $query = $query . " where descripcion like '%$value%' ";
                break;
            case "2": $query = $query . " where stock <=$value ";
                break;
            case "3": $query = $query . " where stock >=$value ";
                break;
            case "4": $query = $query . " where tp.descripcion  like '%$value%' ";
                break;
            case "5": $query = $query . " where pkProducto like '%$value%' ";
                break;
        }

        $result = $db->executeQuery($query);
        if (extension_loaded('zlib')) {
            ob_start('ob_gzhandler');
        } header("Content-type: text/javascript");
        $array = array();
//        die( $query);
        while ($row = $db->fecth_array($result)) {
            $array[] = array("id" => $row['pkProducto'],
                "descripcion" => utf8_encode($row['descripcion'])
            );
        }
        echo json_encode($array);
        if (extension_loaded('zlib')) {
            ob_end_flush();
        }
    }

    /**
     * Funcion de agrega stock un producto
     */
    public function addCantidad($cantidad, $user) {
        $db = new SuperDataBase();

        $query = "call sp_agrega_cantidad_productos('$this->_pkProducto',$cantidad,$user)";
        $db->executeQuery($query);
    }

    public function ListProductPorProvedor($pkProvedor) {
        $db = new SuperDataBase();
        $query = "SELECT * FROM productos_provedor p inner join productos pr on pr.pkProducto=p.pkProducto where p.pkProvedor='$pkProvedor';";
        $result = $db->executeQuery($query);
        $array = array();
//        die( $query);
        while ($row = $db->fecth_array($result)) {
            $array[] = array("id" => $row['pkProducto'],
                "descripcion" => utf8_encode($row['descripcion'])
            );
        }
        echo json_encode($array);
    }

    /**
     * Filtro de productos 
     * * */
    public function filtro_Product($categoria, $valor, $tipoBusqueda) {
        $db = new SuperDataBase();
        $where = "";
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT pkCategoria, pkProductoSucursal, pkSucursal,  tp.pkTipo, stock, precioVenta, precioCompra, p.pkProducto, pr.descripcion FROM producto_sucursal p inner join productos pr on pr.pkProducto=p.pkProducto
 inner join tipos tp on (tp.pkTipo=p.pkTipo) where pkSucursal='$sucursal' ";

        //Inicializamos el filtro
        if ($categoria != "") {
            $query = $query . " and pkCategoria=$categoria";
        }
        switch ($tipoBusqueda) {
            case '1': $query = $query . " and pr.descripcion like '%$valor%'";
                break;
            case '2':$query = $query . " and stock <= $valor";
                break;
            case '3':$query = $query . " and stock >= $valor";
                break;
        }
//die( $query);
        $result = $db->executeQuery($query);
        if (extension_loaded('zlib')) {
            ob_start('ob_gzhandler');
        } header("Content-type: text/javascript");
        $array = array();

        while ($row = $db->fecth_array($result)) {
            $array[] = array("id" => $row['pkProducto'],
                "descripcion" => utf8_encode($row['descripcion']),
                "precioVenta" => $row['precioVenta'],
                "stock" => $row['stock'],
            );
        }
        echo json_encode($array);
        if (extension_loaded('zlib')) {
            ob_end_flush();
        }
    }

    /**
     * Funcion que agrega cantidad al stock de un producto por sucursal
     * @param String $pkProduct Identificador del producto;
     * @param Int $cantidad cantidad que se la a agregar
     * @param $comentario $comentario Describe la razon por la cual se le esta agregando el producto
     */
    public function addCantidadProducto($pkProduct, $cantidad, $comentario, $tipo) {
        $db = new SuperDataBase();
        $comentario = utf8_decode($comentario);
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "Call sp_addEntradaProducto('$user','$pkProduct',$cantidad,'$comentario','$sucursal',$tipo,@sa)";
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

    public function eliminar_producto($pkProductoSucursal) {

        $db = new SuperDataBase();
        $user = new UserLogin();
        //$id = $user->get_idTrabajador();
        $query = "CALL sp_delete_Producto($pkProductoSucursal);";
        $result = $db->executeQuery($query);
        echo $query;
    }
        
    public function saveProducto($producto,$tipo,$precioVenta,$precioCompra,$stock) {        
        $sucursal=UserLogin::get_pkSucursal();
        $db = new SuperDataBase();
        $query = "CALL sp_addNuevoProducto('$producto',$tipo,$precioVenta,$precioCompra,$stock,'$sucursal',@sa);";        
        $db->executeQuery($query);
        echo $query;
    }
    public function editProducto($id,$producto,$tipo,$precioVenta,$precioCompra,$stock) {        
        $db = new SuperDataBase();
        $sp = "CALL sp_update_Producto('$id','$producto',$tipo,$precioVenta,$precioCompra,$stock);";
        $db->executeQuery($sp);
        echo $sp;
    }

}
