<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_InsumoModel {

    private $_descripcion;
    private $pkTipoInsumo;
    private $_estado;
    private $presentacion;
    private $provedor;
    private $precio_p;
    private $_stockMinimo;

    function __construct() {
        
    }

    function get_estado() {
        return $this->_estado;
    }

    function set_estado($_estado) {
        $this->_estado = $_estado;
    }

    function get_descripcion() {
        return $this->_descripcion;
    }

    function set_descripcion($_descripcion) {
        $this->_descripcion = utf8_decode($_descripcion);
    }

    function getPkTipoInsumo() {
        return $this->pkTipoInsumo;
    }

    function getPresentacion() {
        return $this->presentacion;
    }

    function getProvedor() {
        return $this->provedor;
    }

    function getPrecio_p() {
        return $this->precio_p;
    }

    function setPkTipoInsumo($pkTipoInsumo) {
        $this->pkTipoInsumo = $pkTipoInsumo;
    }

    function setPresentacion($presentacion) {
        $this->presentacion = $presentacion;
    }

    function setProvedor($provedor) {
        $this->provedor = $provedor;
    }

    function setPrecio_p($precio_p) {
        $this->precio_p = $precio_p;
    }

    function get_stockMinimo() {
        return $this->_stockMinimo;
    }

    function set_stockMinimo($_stockMinimo) {
        $this->_stockMinimo = $_stockMinimo;
    }

    function set_porcentajeMerma($_porcentajeMerma) {
        if ($_porcentajeMerma) {
            $this->_porcentajeMerma = $_porcentajeMerma;
        } else {
            $this->_porcentajeMerma = 0;
        }
    }

    /**
     * Function para guardar un insumo
     */
    public function guardar() {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $obj = new Application_Models_CajaModel();
        $fecha = $obj->fechaCierre();
        $pkTrabajador = UserLogin::get_idTrabajador();
        $query = "insert into insumos (estado,pkSucursal,descripcionInsumo, pkTipoInsumo, pkUnidad, pkProvedor, precio_promedio,stockMinimo,porcentaje_merma)"
                . " values($this->_estado, '$sucursal',upper('$this->_descripcion'),$this->pkTipoInsumo,'$this->presentacion','$this->provedor',$this->precio_p,$this->_stockMinimo,$this->_porcentajeMerma )";
        $db->executeQuery($query);
//        echo $query;
//        return $db->getId();
        // $query = "INSERT INTO historial_stock_insumos  (pkInsumo, cantidadInicial, cantidadFinal, fecha, pkTrabajador, dateModify)
        //           VALUES (" . $db->getId() . ",0,0,'$fecha',$pkTrabajador,now());";
        // $db->executeQuery($query);
        $query = "INSERT INTO n_historial_stock_insumo ( insumo_id, insumo_porcion_id, almacen_id, stock_inicial, stock_final, fecha, trabajador_id, created_at)
                    SELECT " . $db->getId() . ", null, n_almacen.id, 0, 0, '$fecha', $pkTrabajador, now() from n_almacen";
        $db->executeQuery($query);
    }

    /**
     * Function para guardar un insumo
     */
    public function updateInsumo($id) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query ="update insumos 
                set estado=$this->_estado,
                pkSucursal='$sucursal',
                descripcionInsumo=upper('$this->_descripcion'),
                pkTipoInsumo=$this->pkTipoInsumo, 
                pkUnidad='$this->presentacion', 
                pkProvedor='$this->provedor', 
                precio_promedio=$this->precio_p,
                stockMinimo=$this->_stockMinimo,
                porcentaje_merma=$this->_porcentajeMerma  
                where pkInsumo=$id";
        $db->executeQuery($query);
//         $query;
        return $db->getId();
    }

    public function deleteInsumo($id) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "UPDATE insumos 
                  SET estado = 1
                  where pkInsumo=$id";
        $db->executeQuery($query);
//         $query;
        return $db->getId();
    }

    public function activeInsumo($id) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "UPDATE insumos 
                  SET estado = 0
                  where pkInsumo=$id";
        $db->executeQuery($query);
//         $query;
        return $db->getId();
    }

    /**
     * Function para guardar un insumo
     */
    public function updateCantidad($id, $tipo, $cantidad) {
        $db = new SuperDataBase();
        if ($tipo == "1") {
            //sumar
            $valor = "+";
        } else {
            $valor = "-";
        }
        $query = "update insumos set cantidad=cantidad" . $valor . "$cantidad where pkInsumo=$id";
        $db->executeQuery($query);
//         $query;
        return $db->getId();
    }

    public function listInsumos() {
        $db = new SuperDataBase();
        $query = "SELECT i.pkInsumo, upper(i.descripcionInsumo) as descripcionInsumo, i.precio_promedio, i.pkUnidad, u.descripcion as unidad FROM insumos i, unidad u where (i.estado = 0 OR i.estado = 2) AND i.pkUnidad = u.pkUnidad";
//        echo $query;
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "id" => $row['pkInsumo'],
                "descripcion" => utf8_encode($row['descripcionInsumo']),
                "label" => utf8_encode($row['descripcionInsumo']),
                "price" => $row["precio_promedio"],
                "unidad_id" => $row["pkUnidad"],
                "unidad" => $row["unidad"],
            );
        }
        echo json_encode($array);
    }

    public function listInsumosId($id) {
        $db = new SuperDataBase();
        $query = "SELECT estado,pkTipoInsumo,stockMinimo, pkUnidad, pkProvedor, porcentaje_merma, pkInsumo,upper(descripcionInsumo) as descripcionInsumo,precio_promedio,cantidad FROM insumos i where pkInsumo=" . $id;
//        echo $query;
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "id" => $row['pkInsumo'],
                "descripcion" => utf8_encode($row['descripcionInsumo']),
                "cantidad" => (float) $row['cantidad'],
                "stockMinimo" => (float) $row['stockMinimo'],
                "precio_promedio" => (float) $row['precio_promedio'],
                "pkUnidad" => $row['pkUnidad'],
                "pkTipoInsumo" => $row['pkTipoInsumo'],
                "provedor" => $row['pkProvedor'],
                "estado" => $row['estado'],
                "porcentaje_merma" => floatval($row['porcentaje_merma']),
                "label" => utf8_encode($row['descripcionInsumo'])
            );
        }
        echo json_encode($array);
    }

    public function actualizaStockParcial() {
        $db = new SuperDataBase();
        $obj = new Application_Models_CajaModel();
        $fecha = $obj->fechaCierre();
        $query = "update insumos set cantidadParcial =
(cantidad+
(SELECT ifnull(sum(cantidad),0) as cantidad FROM ingresoinsumos ins where fecha='$fecha' and tipo=1 and insumos.pkInsumo=ins.pkInsumo)) -
((SELECT ifnull(sum(cantidad),0) as cantidad FROM ingresoinsumos ins where fecha='$fecha' and tipo=2 and ins.pkInsumo=insumos.pkInsumo)
+
(SELECT  ifnull(sum(round((cantidadTotal*dp.cantidad),5)),0) FROM insumo_menu im
inner join (detallepedido dp inner join pedido p on  p.pkPediido=dp.pkPediido)
on dp.pkPlato=im.pkPlato where im.pkInsumo=insumos.pkInsumo and dp.estado<>3 and p.estado<>3 and pkTipoPedido=0
and p.fechaCierre='$fecha'  and tipopedido=0
) +
(SELECT  ifnull(sum(round((cantidadTotal*dp.cantidad),5)),0) as tvendido FROM insumo_menu im
inner join (detallepedido dp inner join (pedido p inner join  mesas m on m.pkMesa=p.pkMesa) on  p.pkPediido=dp.pkPediido)
on dp.pkPlato=im.pkPlato where insumos.pkInsumo=im.pkInsumo and dp.estado<>3 and p.estado<>3  and pkTipoPedido=1 and p.fechaCierre='$fecha' and (pkSalon between 43 and 44 or tipoPedido=1))
)";
        $db->executeQuery($query);
        $db->getId();
    }

    public function actualizaCantidadInicial() {
        $db = new SuperDataBase();
        $query = "update insumos set cantidad=cantidadParcial;";
        $db->executeQuery($query);
    }

    public function actualizaVentasDiaAnterior($db = null) {
        error_reporting(E_ALL);
        require_once 'Application/Views/Almacen/KardexHelper.php';
        if (is_null($db)) {
            $db = new SuperDataBase();
        }
        $obj = new Application_Models_CajaModel();
        $fecha = $obj->fechaCierre();

        $pkTrabajador = UserLogin::get_idTrabajador();

        $query = "Select * from corte where fecha_cierre = '$fecha' order by id ASC LIMIT 1";

        $res = $db->executeQueryEx($query);

        while($row = $db->fecth_array($res)) {
            $fecha_inicio = $row['inicio'];
        }

        $kardexHelper = new KardexHelper();

        // echo "<br>" . json_encode($fecha) . "<br>";
        // echo "<br>" . json_encode($fecha_inicio) . "<br>";

        $query = "SELECT
            detallepedido.pkPlato AS plato_id,
            detallepedido.terminal,
            detallepedido.cantidad,
            detallepedido.horaPedido
        FROM
            detallepedido
        LEFT JOIN pedido ON detallepedido.pkPediido = pedido.pkPediido
        WHERE
            detallepedido.estado > 0
        AND detallepedido.estado < 3
        AND pedido.estado != 0
        AND detallepedido.horaPedido <= '$fecha_inicio'
        AND pedido.fechaCierre = '$fecha'";

        $res = $db->executeQueryEx($query);

        $detalles = [];

        while($row = $db->fecth_array($res)) {
            $detalles[] = $row;

            $data_insumo = $kardexHelper->_getDataInsumoPorDetalle($row);

            // echo "<br>" . json_encode($data_insumo) . "<br>";

            
            foreach ($data_insumo as $insumo) {
                
                $historiales = $kardexHelper->getHistorialesPorHoraPedido($row['horaPedido'], $insumo['insumo_id'], $insumo['insumo_porcion_id'], $insumo['almacen_id']);
                
                // echo "<br>" . json_encode($historiales) . "<br>";

                $cantidad = $insumo['cantidad_insumo'];

                foreach ($historiales as $historial) {
                    $query = "UPDATE n_historial_stock_insumo ";
                    if (array_key_exists('is_primera', $historial) && $historial['is_primera']) {
                        $query .= "set stock_final = stock_final - $cantidad";
                    } else if ($historial['fecha'] == $fecha) {
                        $query .= "set stock_inicial = stock_inicial - $cantidad";
                    } else {
                        $query .= "set stock_inicial = stock_inicial - $cantidad, stock_final = stock_final - $cantidad";
                    }

                    $query .= " where id = " . $historial['id'];

                    // echo "<br>" . json_encode($query) . "<br>";

                    $db->executeQueryEx($query);
                }

            }


        }

        // echo "<br>" . count($detalles) . "<br>";
        // echo "<br>" . json_encode($detalles) . "<br>";

        

        // echo 'actualizaVentasDiaAnterior';
        // die(0);
    }

    public function actualizaCantidadHistorial($db = null) {
        error_reporting(E_ALL);
        require_once 'Application/Views/Almacen/KardexHelper.php';
        if (is_null($db)) {
            $db = new SuperDataBase();
        }
        $obj = new Application_Models_CajaModel();
        $fecha = $obj->fechaCierre();

        $pkTrabajador = UserLogin::get_idTrabajador();

        // echo '<br>Fecha Caja : ';
        // echo $fecha . '<br>';

        // $tiempo_inicio = null;
        // $tiempo_fin = date("Y-m-d H:i:s");
        // //Gino Lluen 02/06/2019
        // //Solucion al problema del kardex cuando se deja abierta una mesa varios dias
        // $query_corte_min = "Select * from corte where fecha_cierre = '".$fecha."' order by id ASC LIMIT 1";
        // $result_c = $db->executeQueryEx($query_corte_min);
        // while ($row = $db->fecth_array($result_c)){

        //     // echo "<br>" . json_encode($row) . "<br>"; 
        //     $tiempo_inicio = $row["inicio"];
        // }

        // $db->executeQueryEx("SET SQL_SAFE_UPDATES = 0");
        // $query = "UPDATE historial_stock_insumos h
        //             SET cantidadFinal = cantidadFinal -
        //                 (SELECT (SELECT  ifnull(SUM(ROUND((cantidadTotal*dp.cantidad),5)),0) FROM insumo_menu im
        //                 INNER JOIN detallepedido dp ON dp.pkPlato=im.pkPlato WHERE im.pkInsumo=h.pkInsumo AND dp.estado > 0 AND dp.estado <3 AND dp.horaPedido BETWEEN CAST('".$tiempo_inicio."' as DATETIME) AND CAST('".$tiempo_fin."' as DATETIME))) WHERE h.fecha = '$fecha';";
        // echo $query;    
        // $db->executeQueryEx($query);
        //$db->executeQuery("SET SQL_SAFE_UPDATES = 1");

        // actualizar la cantidad final con la suma de movimientos (+) con las ventas
        $fecha_inicio_ = $fecha . " 00:00:00";
        $fecha_fin_ = date('Y-m-d H:i:s');

        $query = "Select * from corte where fecha_cierre = '$fecha' order by id ASC LIMIT 1";

        $res = $db->executeQueryEx($query);

        while($row = $db->fecth_array($res)) {
            $fecha_inicio_ = $row['inicio'];
        }

        // echo "<br>" . $fecha_inicio_ . "<br>"; 

        $kardexHelper = new KardexHelper();

        $data_movimientos = $kardexHelper->_getDataMovimientosGroupAlmacen($fecha);

        // echo "<br>" . json_encode($data_movimientos) . "<br>"; 

        $data_ventas = $kardexHelper->_getDataVentasGroupAlmacen($fecha);

        // echo "<br>" . json_encode($data_ventas) . "<br>"; 

        $query = "UPDATE n_historial_stock_insumo
            SET stock_final = stock_inicial, 
                updated_at = now()
            WHERE
                fecha = '$fecha'
            ";

        // echo "<br>" . $query . "<br>"; 

        $db->executeQueryEx($query);

        // actualizar stock final = stock inicial + movimientos_almacen

        foreach ($data_movimientos as $movimiento) {

            $query = "select 1 from n_historial_stock_insumo where fecha = '$fecha' and
                almacen_id = ${movimiento['almacen_id']} and
                insumo_id = ${movimiento['insumo_id']} ";

            if ($movimiento['insumo_porcion_id']) {
                $query .= " and insumo_porcion_id = ${movimiento['insumo_porcion_id']}";
            } else {
                $query .= " and insumo_porcion_id is null";
            }

            $query .= " limit 1";

            $res = $db->executeQueryEx($query);

            $existe = false;

            while ($row = $db->fecth_array($res)) {
                $existe = true;
            }

            if ($existe) {
                $query = "UPDATE n_historial_stock_insumo
                    SET stock_final = stock_inicial + ${movimiento['cantidad']}, 
                        updated_at = now()
                    WHERE
                        fecha = '$fecha' and
                        almacen_id = ${movimiento['almacen_id']} and
                        insumo_id = ${movimiento['insumo_id']}
                    ";

                if ($movimiento['insumo_porcion_id']) {
                    $query .= " and insumo_porcion_id = ${movimiento['insumo_porcion_id']}";
                } else {
                    $query .= " and insumo_porcion_id is null";
                }

                $db->executeQueryEx($query);
            } else {

                $_ip = $movimiento['insumo_porcion_id'] ? $movimiento['insumo_porcion_id'] : 'null';

                $query = "INSERT INTO n_historial_stock_insumo ( insumo_id, insumo_porcion_id, almacen_id, stock_inicial, stock_final, fecha, trabajador_id, created_at)
                    values( ${movimiento['insumo_id']}, $_ip, ${movimiento['almacen_id']}, 0, ${movimiento['cantidad']}, '$fecha', $pkTrabajador, now() )";
                $db->executeQuery($query);
            }
            
            // echo "<br>" . $query . "<br>"; 
        }

        // actualizar stock final = stock final - ventas

        foreach ($data_ventas as $movimiento) {

            $query = "select 1 from n_historial_stock_insumo where fecha = '$fecha' and
                almacen_id = ${movimiento['almacen_id']} and
                insumo_id = ${movimiento['insumo_id']} ";

            if ($movimiento['insumo_porcion_id']) {
                $query .= " and insumo_porcion_id = ${movimiento['insumo_porcion_id']}";
            } else {
                $query .= " and insumo_porcion_id is null";
            }

            $query .= " limit 1";

            $res = $db->executeQueryEx($query);

            $existe = false;

            while ($row = $db->fecth_array($res)) {
                $existe = true;
            }

            if ($existe) {

                $query = "UPDATE n_historial_stock_insumo
                    SET stock_final = stock_final - ${movimiento['cantidad_insumo']}, 
                        updated_at = now()
                    WHERE
                        fecha = '$fecha' and
                        almacen_id = ${movimiento['almacen_id']} and
                        insumo_id = ${movimiento['insumo_id']}
                    ";

                if ($movimiento['insumo_porcion_id']) {
                    $query .= " and insumo_porcion_id = ${movimiento['insumo_porcion_id']}";
                } else {
                    $query .= " and insumo_porcion_id is null";
                }

                $db->executeQueryEx($query);
            } else {

                $_ip = $movimiento['insumo_porcion_id'] ? $movimiento['insumo_porcion_id'] : 'null';

                $query = "INSERT INTO n_historial_stock_insumo ( insumo_id, insumo_porcion_id, almacen_id, stock_inicial, stock_final, fecha, trabajador_id, created_at)
                    values( ${movimiento['insumo_id']}, $_ip, ${movimiento['almacen_id']}, 0, -${movimiento['cantidad_insumo']}, '$fecha', $pkTrabajador, now() )";
                $db->executeQuery($query);
            }

            
            // echo "<br>" . $query . "<br>"; 

        }

        // die(0);
    }
    
    public function actualizaCantidadPedido(){
        $db = new SuperDataBase();
        $db2 = new SuperDataBase();
        $db3 = new SuperDataBase();
        $obj = new Application_Models_CajaModel();
        $fecha = $obj->fechaCierre();

        $tiempo_inicio = null;
        $tiempo_fin = date("Y-m-d H:i:s");
        //Gino Lluen 02/06/2019
        //Solucion al problema del kardex cuando se deja abierta una mesa varios dias
        $query_corte_min = "Select * from corte where fecha_cierre = '".$fecha."' order by id ASC LIMIT 1";
        $result_c = $db->executeQueryEx($query_corte_min);
        while ($row = $db->fecth_array($result_c)){
            $tiempo_inicio = $row["inicio"];
        }

        //Borramos porciacaso
        $db->executeQueryEx("SET SQL_SAFE_UPDATES = 0");
        $query = "delete from insumoporpedido where fecha='$fecha'";
        $db->executeQueryEx($query);

        //Insertamos insumo por pedido
        $query = "SELECT pkInsumo FROM insumos WHERE estado=0;";
        $result= $db->executeQueryEx($query);
        while ($row = $db->fecth_array($result)){
            $query2 = "SELECT ifnull(SUM(ROUND((cantidadTotal*dp.cantidad),5)),0.00000) as tvendido, dp.pkPediido FROM insumo_menu im INNER JOIN detallepedido dp ON dp.pkPlato=im.pkPlato WHERE im.pkInsumo=" . $row['pkInsumo'] . " AND dp.estado > 0 AND dp.estado < 3 AND dp.horaPedido BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' GROUP BY dp.pkPediido";

            // $db2->executeQueryEx('delete from almacen');
            // $db2->executeQuery('select * from cajas11');

            $result2= $db2->executeQueryEx($query2);
            while ($row2 = $db2->fecth_array($result2)){
                if($row2['tvendido']>0){
                    $query3 = "INSERT INTO insumoporpedido(pkPediido, pkInsumo, cantidad, fecha) VALUES('".utf8_encode($row2['pkPediido'])."',".$row['pkInsumo'].",".$row2['tvendido'].",'$fecha')";
                    $db3->executeQueryEx($query3);
                }
            }
        }               
    }
    
    public function AgregarHistorialStock() {
        $db = new SuperDataBase();
        $obj = new Application_Models_CajaModel();
        $fecha = $obj->fechaCierre();
        $pkTrabajador = UserLogin::get_idTrabajador();

        $query = "select 1 from n_historial_stock_insumo where fecha = '$fecha' ";

        $res = $db->executeQueryEx($query);

        $existe = false;

        while ($row = $db->fecth_array($res)) {
            $existe = true;
        }

        if ($existe) {
            $db->executeQueryEx('ROLLBACK');

            echo "error -> El día ya ha sido aperturado";
            
            throw new Exception('El dia ya ha sido aperturado');
        }
        
        //Borramos porciacaso
        $db->executeQueryEx("SET SQL_SAFE_UPDATES = 0");
        $query = "delete from n_historial_stock_insumo where fecha = '$fecha'";
        $db->executeQueryEx($query);
        
        //Insertamos cantidad del dia
        
        // $query = "INSERT INTO historial_stock_insumos  ( pkInsumo, cantidadInicial, cantidadFinal, fecha, pkTrabajador, dateModify)
        //         SELECT pkInsumo, IFNULL((SELECT cantidadFinal
        //                                  FROM historial_stock_insumos h
        //                                  WHERE h.pkInsumo=i.pkInsumo ORDER BY fecha DESC LIMIT 1),0) AS cInicial,(select cInicial) as cFinal,'$fecha',$pkTrabajador,now()
        //         FROM insumos i WHERE i.estado = 0;";
        // $db->executeQueryEx($query);

        $query = "SELECT
            *
        FROM
            corte
        WHERE
            fecha_cierre < '$fecha'
        ORDER BY
            fecha_cierre DESC
        LIMIT 1";

        $res = $db->executeQueryEx($query);

        $ayer = null;

        while($row = $db->fecth_array($res)) {
            $ayer = $row['fecha_cierre'];
        }

        // echo "Ayer " . $ayer;

        if (is_null($ayer)) return;

        $query = "INSERT INTO n_historial_stock_insumo ( insumo_id, insumo_porcion_id, almacen_id, stock_inicial, stock_final, fecha, trabajador_id, created_at)
        SELECT
            insumo_id,
            insumo_porcion_id,
            almacen_id,
            stock_final AS stock_inicial,
            NULL AS stock_final,
            '$fecha' AS fecha,
            $pkTrabajador AS trabajador_id,
            now() AS created_at
        FROM
            n_historial_stock_insumo
        where fecha = '$ayer'";

        // echo "<br>" . $query;

        $db->executeQueryEx($query);

        // die(0);
    }

}
