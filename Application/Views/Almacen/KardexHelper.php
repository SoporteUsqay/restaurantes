<?php

class KardexHelper 
{

    public $almacen_id;

    public function setAlmacen($value)
    {
        $this->almacen_id = ($value) ? $value : 'null';
    }

    public function getDataHistorial($fecha) 
    {
        return self::_getDataHistorial($fecha);
    }

    public function getCorteAnterior($fecha, $fechaFin) 
    {
        $db = new SuperDataBase();

        $fecha_inicio_ = $fecha . " 00:00:00";
        $fecha_fin_ = date('Y-m-d H:i:s');

        $query = "Select * from corte where fecha_cierre = '$fecha' order by id ASC LIMIT 1";

        $res = $db->executeQueryEx($query);

        $existe_dia = false;

        while($row = $db->fecth_array($res)) {
            $existe_dia = true;
            $fecha_inicio_ = $row['inicio'];
        }

        if (!$existe_dia) {
            $query = "Select * from corte where fecha_cierre < '$fecha' order by id DESC LIMIT 1";

            $res = $db->executeQueryEx($query);

            while($row = $db->fecth_array($res)) {
                $fecha_inicio_ = $row['fin'];
            }
        }

        $query = "Select * from corte where fecha_cierre = '$fechaFin' order by id DESC LIMIT 1";

        $res = $db->executeQueryEx($query);

        while($row = $db->fecth_array($res)) {
            if ($row['fin']) {
                $fecha_fin_ = $row['fin'];
            }
        }

        return [
            "inicio" => $fecha_inicio_,
            "fin" => $fecha_fin_
        ];
    }

    /**
     * Obtiene el historial de stocks del dia de ayer a la fecha
     */
    public function _getDataHistorial($fecha, $insumo_id = null, $insumo_porcion_id = null)
    {
        $db = new SuperDataBase();

        $query = "SELECT
            *
        FROM
            corte
        WHERE
            fecha_cierre <= '$fecha'
        ORDER BY
            id DESC
        LIMIT 1";

        $res = $db->executeQueryEx($query);

        $ayer = date("Y-m-d", strtotime(date($fecha) . " - 1 days"));

        $_field = "stock_final";

        while ($row = $db->fecth_array($res)) {
            $ayer = $row['fecha_cierre'];
        }

        if ($fecha == $ayer) {
            $_field = "stock_inicial";
        }

        $query = "SELECT
            *
        FROM
            n_historial_stock_insumo
        WHERE
            fecha = '$ayer'
        AND 
            almacen_id = $this->almacen_id";

        if ($insumo_id && !$insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id is null";
        } 

        if ($insumo_id && $insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id = $insumo_porcion_id";
        } 

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = array_merge($row, [
                "stock" => $row[$_field]
            ]);
        }

        return $lista;
    }

    /**
     * Busca el stock del insumo o porcion de una lista
     */
    public function getStockPorInsumo($item, $data) 
    {
        $key = false;

        if (!array_key_exists('insumo_porcion_id', $item)) {

            $key = array_search($item['insumo_id'], array_column($data, 'insumo_id'));
            if ($key && $data[$key]['insumo_porcion_id'] != null) {
                $key = false;
            }

        } else if (array_key_exists('insumo_porcion_id', $item)) {

            $key = array_search($item['insumo_porcion_id'], array_column($data, 'insumo_porcion_id'));
        }

        if ($key === false) return null;

        return $data[$key];
    }

    /**
     * Agrupa los insumos y sus porciones en una sola lista
     */
    public function getListaCompleta() 
    {
        $db = new SuperDataBase();

        $lista_insumos = [];

        $query = "select insumos.*, pkInsumo as insumo_id, descripcionInsumo as nombre_insumo, unidad.descripcion as unidad from insumos
        LEFT JOIN unidad ON insumos.pkUnidad = unidad.pkUnidad where insumos.estado = 0";

        $res = $db->executeQueryEx($query);

        while($row = $db->fecth_array($res)) {
            $lista_insumos[] = $row;
        }
        
        $query = "SELECT
            insumo_porcion.*, id AS insumo_porcion_id,
            (insumos.stockMinimo / insumo_porcion.valor) as stockMinimo,
            insumos.descripcionInsumo AS nombre_insumo,
            unidad.descripcion as nombre_unidad,
            u1.descripcion as unidad
        FROM
            insumo_porcion
        LEFT JOIN insumos ON insumo_porcion.insumo_id = insumos.pkInsumo
        LEFT JOIN unidad ON insumo_porcion.unidad_id = unidad.pkUnidad
        LEFT JOIN unidad u1 ON insumos.pkUnidad = u1.pkUnidad
        where insumo_porcion.deleted_at is null";

        $res = $db->executeQueryEx($query);

        while($row = $db->fecth_array($res)) {
            $lista_insumos[] = $row;
        }

        return $lista_insumos;
    }

    /**
     * Obtiene un solo insumo o porcion
     */
    public function _getListaCompleta($insumo_id = null, $insumo_porcion_id = null)
    {
        $db = new SuperDataBase();

        $lista_insumos = [];

        $query = "select insumos.*, pkInsumo as insumo_id, descripcionInsumo as nombre_insumo, unidad.descripcion as unidad from insumos
        LEFT JOIN unidad ON insumos.pkUnidad = unidad.pkUnidad  where insumos.estado = 0";
        
        if ($insumo_id && !$insumo_porcion_id) {
            $query .= " and pkInsumo = $insumo_id";

            $res = $db->executeQueryEx($query);

            while($row = $db->fecth_array($res)) {
                $lista_insumos[] = $row;
            }
        } 
        
        $query = "SELECT
            insumo_porcion.*, id AS insumo_porcion_id,
            insumos.descripcionInsumo AS nombre_insumo,
            unidad.descripcion as nombre_unidad,
            u1.descripcion as unidad
        FROM
            insumo_porcion
        LEFT JOIN insumos ON insumo_porcion.insumo_id = insumos.pkInsumo
        LEFT JOIN unidad ON insumo_porcion.unidad_id = unidad.pkUnidad
        LEFT JOIN unidad u1 ON insumos.pkUnidad = u1.pkUnidad
        where insumo_porcion.deleted_at is null";
        
        if ($insumo_id && $insumo_porcion_id) {
            $query .= " and insumo_porcion.id = $insumo_porcion_id";

            $res = $db->executeQueryEx($query);

            while($row = $db->fecth_array($res)) {
                $lista_insumos[] = $row;
            }
        }

        return $lista_insumos;
    }

    public function getDataMovimientos($fecha) 
    {
        return self::_getDataMovimientos($fecha);
    }

    /**
     * Obtiene la lista de movimientos de la fecha de todos los insumos o porciones
     */
    public function _getDataMovimientos($fecha, $insumo_id = null, $insumo_porcion_id = null)
    {
        $db = new SuperDataBase();

        $query = "SELECT
            insumo_id,
            insumo_porcion_id,
            SUM(
                IF (tipo = 1, cantidad, - cantidad)
            ) AS cantidad
        FROM
            n_detalle_movimiento_almacen
        WHERE
            cast(fecha AS date) = '$fecha' and deleted_at is null AND 
            almacen_id = $this->almacen_id";

        if ($insumo_id && !$insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id is null";
        } 

        if ($insumo_id && $insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id = $insumo_porcion_id";
        } 

        $query .= " GROUP BY
            insumo_id,
            insumo_porcion_id";

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = $row;
        }

        return $lista;
    }
    /**
     * Obtiene la lista de movimientos de la fecha de todos los insumos o porciones
     */
    public function _getDataMovimientosGroupAlmacen($fecha, $insumo_id = null, $insumo_porcion_id = null)
    {
        $db = new SuperDataBase();

        $query = "SELECT
            insumo_id,
            insumo_porcion_id,
            almacen_id,
            SUM(
                IF (tipo = 1, cantidad, - cantidad)
            ) AS cantidad
        FROM
            n_detalle_movimiento_almacen
        WHERE
            cast(fecha AS date) = '$fecha' and deleted_at is null";

        if ($insumo_id && !$insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id is null";
        } 

        if ($insumo_id && $insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id = $insumo_porcion_id";
        } 

        $query .= " GROUP BY
            insumo_id,
            insumo_porcion_id,
            almacen_id";

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = $row;
        }

        return $lista;
    }

    /**
     * Obtiene la lista de movimientos de la fecha inicio hasta fin separados en ingresos e egresos de todos los insumos o porciones
     */
    public function getDataMovimientosSeparados($fecha, $fechaFin, $insumo_id = null, $insumo_porcion_id = null)
    {
        $db = new SuperDataBase();

        $query = "SELECT
            insumo_id,
            insumo_porcion_id,
            SUM(
                IF (tipo = 1, cantidad, 0)
            ) AS stock_ingresos,
            SUM(
                IF (tipo = 2, cantidad, 0)
            ) AS stock_salidas
        FROM
            n_detalle_movimiento_almacen
        WHERE
            cast(fecha AS date) BETWEEN '$fecha' AND '$fechaFin' and deleted_at is null AND 
            almacen_id = $this->almacen_id";

        if ($insumo_id && !$insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id is null";
        } 

        if ($insumo_id && $insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id = $insumo_porcion_id";
        } 

        $query .= " GROUP BY
            insumo_id,
            insumo_porcion_id";

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = $row;
        }

        return $lista;
    }
    /**
     * Obtiene la lista de movimientos de la fecha inicio hasta fin detallados de todos los insumos o porciones
     */
    public function getDataMovimientosDetail($fecha, $fechaFin, $insumo_id = null, $insumo_porcion_id = null)
    {
        $db = new SuperDataBase();

        $query = "SELECT
            id as code,
            insumo_id,
            insumo_porcion_id,
            cantidad,
            tipo,
            fecha,
            motivo,
            IF (tipo = 1, 'Ingreso', 'Salida') as descripcion
        FROM
            n_detalle_movimiento_almacen
        WHERE
            cast(fecha AS date) BETWEEN '$fecha' AND '$fechaFin' and deleted_at is null AND 
            almacen_id = $this->almacen_id";

        if ($insumo_id && !$insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id is null";
        } 

        if ($insumo_id && $insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id = $insumo_porcion_id";
        } 

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = $row;
        }

        return $lista;
    }

    /**
     * Obtiene la lista de insumos vendidos en la fecha
     * **se puede optimizar mas - consultar recetas solo de platos vendidos
     */
    public function getDataPlatosVendidos($fecha, $fechaFin = null) 
    {
        $dat_corte = $this->getCorteAnterior($fecha, $fechaFin);

        $fI = $dat_corte['inicio'];
        $fF = $dat_corte['fin'];

        // dp.horaPedido BETWEEN CAST('".$tiempo_inicio."' as DATETIME) AND CAST('".$tiempo_fin."' as DATETIME)

        $db = new SuperDataBase();

        $query = "SELECT
            detallepedido.pkPlato AS plato_id,
            detallepedido.terminal,
            sum(detallepedido.cantidad) AS cantidad
        FROM
            detallepedido
        LEFT JOIN pedido ON pedido.pkPediido = detallepedido.pkPediido
        WHERE
            detallepedido.estado > 0
        AND detallepedido.estado < 3
        AND pedido.estado != 3
        AND pedido.estado != 0
        AND detallepedido.horaPedido BETWEEN CAST(
            '$fI' AS DATETIME
        )
        AND CAST(
            '$fF' AS DATETIME
        )
        GROUP BY
            pkPlato, terminal";

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = $row;
        }

        $data = [];

        foreach ($lista as $plato) {

            $query = "SELECT
                plato_id,
                insumo_id,
                insumo_porcion_id,
                cantidad
            FROM
                n_receta
            WHERE
                plato_id = '${plato['plato_id']}'
            and 
                ( terminal is null
            or 
                terminal = '${plato['terminal']}')
            AND 
                almacen_id = $this->almacen_id and deleted_at is null";

            $res = $db->executeQueryEx($query);

            while($row = $db->fecth_array($res)) {

                $key_exists = array_search($row['insumo_id'], array_column($data, 'insumo_id'));

                if ($key_exists !== false) {
                    if ($data[$key_exists]['insumo_porcion_id'] == $row['insumo_porcion_id']) {
                        
                        $data[$key_exists]['cantidad_plato'] += $plato['cantidad'];
                        $data[$key_exists]['cantidad_insumo'] += $plato['cantidad'] * $row['cantidad'];
                        continue;
                    }
                }

                $data[] = [
                    "insumo_id" => $row['insumo_id'],
                    "insumo_porcion_id" => $row['insumo_porcion_id'],
                    // "plato_id" => $plato['plato_id'],
                    "cantidad_plato" => $plato['cantidad'],
                    "cantidad_insumo" => $plato['cantidad'] * $row['cantidad'],
                ];
            }
        }

        return $data;
    }
    public function getDataPlatosVendidosPorCobrar($fecha, $fechaFin = null) 
    {
        $dat_corte = $this->getCorteAnterior($fecha, $fechaFin);

        $fI = $dat_corte['inicio'];
        $fF = $dat_corte['fin'];

        // dp.horaPedido BETWEEN CAST('".$tiempo_inicio."' as DATETIME) AND CAST('".$tiempo_fin."' as DATETIME)

        $db = new SuperDataBase();

        $query = "SELECT
            detallepedido.pkPlato AS plato_id,
            detallepedido.terminal,
            sum(detallepedido.cantidad) AS cantidad
        FROM
            detallepedido
        LEFT JOIN pedido ON pedido.pkPediido = detallepedido.pkPediido
        WHERE
            detallepedido.estado > 0
        AND detallepedido.estado < 3
        AND pedido.estado != 3
        AND pedido.estado = 0
        AND detallepedido.horaPedido BETWEEN CAST(
            '$fI' AS DATETIME
        )
        AND CAST(
            '$fF' AS DATETIME
        )
        GROUP BY
            pkPlato, terminal";

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = $row;
        }

        $data = [];

        foreach ($lista as $plato) {

            $query = "SELECT
                plato_id,
                insumo_id,
                insumo_porcion_id,
                cantidad
            FROM
                n_receta
            WHERE
                plato_id = '${plato['plato_id']}'
            and 
                ( terminal is null
            or 
                terminal = '${plato['terminal']}')
            AND 
                almacen_id = $this->almacen_id and deleted_at is null";

            $res = $db->executeQueryEx($query);

            while($row = $db->fecth_array($res)) {

                $key_exists = array_search($row['insumo_id'], array_column($data, 'insumo_id'));

                if ($key_exists !== false) {
                    if ($data[$key_exists]['insumo_porcion_id'] == $row['insumo_porcion_id']) {
                        
                        $data[$key_exists]['cantidad_plato'] += $plato['cantidad'];
                        $data[$key_exists]['cantidad_insumo'] += $plato['cantidad'] * $row['cantidad'];
                        continue;
                    }
                }

                $data[] = [
                    "insumo_id" => $row['insumo_id'],
                    "insumo_porcion_id" => $row['insumo_porcion_id'],
                    // "plato_id" => $plato['plato_id'],
                    "cantidad_plato" => $plato['cantidad'],
                    "cantidad_insumo" => $plato['cantidad'] * $row['cantidad'],
                ];
            }
        }

        return $data;
    }

    public function getDataPlatosVendidosPagadosHoy($fecha, $fechaFin = null) 
    {
        $dat_corte = $this->getCorteAnterior($fecha, $fechaFin);

        $fI = $dat_corte['inicio'];
        $fF = $dat_corte['fin'];

        $db = new SuperDataBase();

        $obj = new Application_Models_CajaModel();
        $fechaHoy = $obj->fechaCierre();

        $query = "SELECT
            detallepedido.pkPlato AS plato_id,
            detallepedido.terminal,
            sum(detallepedido.cantidad) as cantidad,
            detallepedido.horaPedido
        FROM
            detallepedido
        LEFT JOIN pedido ON detallepedido.pkPediido = pedido.pkPediido
        WHERE
            detallepedido.estado > 0
        AND detallepedido.estado < 3
        AND pedido.estado != 0
        AND detallepedido.horaPedido BETWEEN CAST(
            '$fI' AS DATETIME
        )
        AND CAST(
            '$fF' AS DATETIME
        )
        AND pedido.fechaCierre = '$fechaHoy'
        GROUP BY
            pkPlato, terminal";

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = $row;
        }

        $data = [];

        foreach ($lista as $plato) {

            $query = "SELECT
                plato_id,
                insumo_id,
                insumo_porcion_id,
                cantidad
            FROM
                n_receta
            WHERE
                plato_id = '${plato['plato_id']}'
            and 
                ( terminal is null
            or 
                terminal = '${plato['terminal']}')
            AND 
                almacen_id = $this->almacen_id and deleted_at is null";

            $res = $db->executeQueryEx($query);

            while($row = $db->fecth_array($res)) {

                $key_exists = array_search($row['insumo_id'], array_column($data, 'insumo_id'));

                if ($key_exists !== false) {
                    if ($data[$key_exists]['insumo_porcion_id'] == $row['insumo_porcion_id']) {
                        
                        $data[$key_exists]['cantidad_plato'] += $plato['cantidad'];
                        $data[$key_exists]['cantidad_insumo'] += $plato['cantidad'] * $row['cantidad'];
                        continue;
                    }
                }

                $data[] = [
                    "insumo_id" => $row['insumo_id'],
                    "insumo_porcion_id" => $row['insumo_porcion_id'],
                    // "plato_id" => $plato['plato_id'],
                    "cantidad_plato" => $plato['cantidad'],
                    "cantidad_insumo" => $plato['cantidad'] * $row['cantidad'],
                ];
            }
        }

        return $data;
    }

    public function _getDataInsumoPorDetalle($plato)
    {
        $db = new SuperDataBase();

        $data = [];
        $index = 0;

        $query = "SELECT
            plato_id,
            insumo_id,
            insumo_porcion_id,
            almacen_id,
            cantidad
        FROM
            n_receta
        WHERE
            plato_id = '${plato['plato_id']}' 
        and 
            ( terminal is null
        or 
            terminal = '${plato['terminal']}')
        AND 
            deleted_at is null";

        $res = $db->executeQueryEx($query);

        while($row = $db->fecth_array($res)) {

            $founded = array_filter($data, function ($i) use ($row) {
                return $i['insumo_id'] == $row['insumo_id'] && $i['insumo_porcion_id'] == $row['insumo_porcion_id'] && $i['almacen_id'] == $row['almacen_id'];
            });
            
            if (count($founded) > 0) {

                $founded = $founded[array_key_first($founded)];

                if ($founded) {

                    $key_exists = $founded['index'];
    
                    $data[$key_exists]['cantidad_plato'] += $plato['cantidad'];
                    $data[$key_exists]['cantidad_insumo'] += $plato['cantidad'] * $row['cantidad'];
                    continue;
                }
            }

            $data[] = [
                "index" => $index++,
                "insumo_id" => $row['insumo_id'],
                "insumo_porcion_id" => $row['insumo_porcion_id'],
                "almacen_id" => $row['almacen_id'],
                // "plato_id" => $plato['plato_id'],
                "cantidad_plato" => $plato['cantidad'],
                "cantidad_insumo" => $plato['cantidad'] * $row['cantidad'],
            ];
        }

        return $data;
    }

    public function _getDataVentasGroupAlmacen($fecha, $fechaFin = null) 
    {
        $dat_corte = $this->getCorteAnterior($fecha, $fechaFin);

        $fI = $dat_corte['inicio'];
        $fF = $dat_corte['fin'];

        // dp.horaPedido BETWEEN CAST('".$tiempo_inicio."' as DATETIME) AND CAST('".$tiempo_fin."' as DATETIME)

        $db = new SuperDataBase();

        $query = "SELECT
            detallepedido.pkPlato AS plato_id,
            detallepedido.terminal,
            sum(detallepedido.cantidad) AS cantidad
        FROM
            detallepedido
        LEFT JOIN pedido ON pedido.pkPediido = detallepedido.pkPediido
        WHERE
            detallepedido.estado > 0
        AND detallepedido.estado < 3
        AND pedido.estado != 3
        AND pedido.estado != 0
        AND detallepedido.horaPedido BETWEEN CAST(
            '$fI' AS DATETIME
        )
        AND CAST(
            '$fF' AS DATETIME
        )
        GROUP BY
            pkPlato, terminal";

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = $row;
        }

        $data = [];
        $index = 0;

        foreach ($lista as $plato) {

            $query = "SELECT
                plato_id,
                insumo_id,
                insumo_porcion_id,
                almacen_id,
                cantidad
            FROM
                n_receta
            WHERE
                plato_id = '${plato['plato_id']}' 
            and 
                ( terminal is null
            or 
                terminal = '${plato['terminal']}')
            AND 
                deleted_at is null";

            $res = $db->executeQueryEx($query);

            while($row = $db->fecth_array($res)) {

                $founded = array_filter($data, function ($i) use ($row) {
                    return $i['insumo_id'] == $row['insumo_id'] && $i['insumo_porcion_id'] == $row['insumo_porcion_id'] && $i['almacen_id'] == $row['almacen_id'];
                });
                
                if (count($founded) > 0) {

                    $founded = $founded[array_key_first($founded)];

                    if ($founded) {
                        $key_exists = $founded['index'];

                        $data[$key_exists]['cantidad_plato'] += $plato['cantidad'];
                        $data[$key_exists]['cantidad_insumo'] += $plato['cantidad'] * $row['cantidad'];
                        continue;
                    }
                }

                $data[] = [
                    "index" => $index++,
                    "insumo_id" => $row['insumo_id'],
                    "insumo_porcion_id" => $row['insumo_porcion_id'],
                    "almacen_id" => $row['almacen_id'],
                    // "plato_id" => $plato['plato_id'],
                    "cantidad_plato" => $plato['cantidad'],
                    "cantidad_insumo" => $plato['cantidad'] * $row['cantidad'],
                ];
            }
        }

        return $data;
    }

    /**
     * Obtiene la lista de insumos vendidos en la fecha
     * **se puede optimizar mas - consultar recetas solo de platos vendidos
     */
    public function getDataVentasDetail($fecha, $fechaFin, $insumo_id, $insumo_porcion_id) 
    {
        $dat_corte = $this->getCorteAnterior($fecha, $fechaFin);

        $fI = $dat_corte['inicio'];
        $fF = $dat_corte['fin'];

        // dp.horaPedido BETWEEN CAST('".$tiempo_inicio."' as DATETIME) AND CAST('".$tiempo_fin."' as DATETIME)

        $db = new SuperDataBase();

        $query = "SELECT
            *
        FROM
            n_receta
        WHERE
            deleted_at is null and almacen_id = $this->almacen_id ";

        if ($insumo_id && !$insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id is null";
        } 

        if ($insumo_id && $insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id = $insumo_porcion_id";
        } 

        $res = $db->executeQueryEx($query);

        $recetas = [];

        while ($row = $db->fecth_array($res)) {
            $recetas[] = $row;
        }

        $query = "SELECT
            detallepedido.pkDetallePedido,
            detallepedido.pkPlato AS plato_id,
            plato.descripcion AS nombre_plato,
            terminal,
            horaTermino,
            cantidad
        FROM
            detallepedido
        LEFT JOIN plato ON plato.pkPlato = detallepedido.pkPlato
        LEFT JOIN pedido ON pedido.pkPediido = detallepedido.pkPediido
        WHERE
            detallepedido.estado > 0
        AND detallepedido.estado < 3
        AND pedido.estado != 3
        AND pedido.estado != 0
        AND horaPedido BETWEEN CAST(
            '$fI' AS DATETIME
        )
        AND CAST(
            '$fF' AS DATETIME
        )";

        if (count($recetas) == 0) {
            return [];
        } else {
            $temp = implode(', ', array_map(function ($i) {
                return "'${i['plato_id']}'";
            }, $recetas));

            $query .= " and detallepedido.pkPlato in ($temp)";
        }

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = $row;
        }

        $data = [];

        foreach ($lista as $pedido) {

            $receta = array_filter($recetas, function ($i) use ($pedido) {
                return $i['plato_id'] == $pedido['plato_id'] && ( is_null($i['terminal'] ) || $i['terminal'] == $pedido['terminal'] );
            });


            foreach ($receta as $row) {
                $data[] = [
                    "insumo_id" => $row['insumo_id'],
                    "insumo_porcion_id" => $row['insumo_porcion_id'],
                    // "plato_id" => $pedido['plato_id'],
                    "cantidad_plato" => $pedido['cantidad'],
                    "cantidad" => $pedido['cantidad'] * $row['cantidad'],
                    "tipo" => 2,
                    "fecha" => $pedido['horaTermino'],
                    "motivo" => $pedido['nombre_plato'],
                    "descripcion" => "Pedido",
                    "code" => $pedido['pkDetallePedido'],

                ];
            }
        }

        return $data;
    }
    public function getDataVentasDetailPorCobrar($fecha, $fechaFin, $insumo_id, $insumo_porcion_id) 
    {
        $dat_corte = $this->getCorteAnterior($fecha, $fechaFin);

        $fI = $dat_corte['inicio'];
        $fF = $dat_corte['fin'];

        // dp.horaPedido BETWEEN CAST('".$tiempo_inicio."' as DATETIME) AND CAST('".$tiempo_fin."' as DATETIME)

        $db = new SuperDataBase();

        $query = "SELECT
            *
        FROM
            n_receta
        WHERE
            deleted_at is null and almacen_id = $this->almacen_id ";

        if ($insumo_id && !$insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id is null";
        } 

        if ($insumo_id && $insumo_porcion_id) {
            $query .= " and insumo_id = $insumo_id and insumo_porcion_id = $insumo_porcion_id";
        } 

        $res = $db->executeQueryEx($query);

        $recetas = [];

        while ($row = $db->fecth_array($res)) {
            $recetas[] = $row;
        }

        $query = "SELECT
            detallepedido.pkDetallePedido,
            detallepedido.pkPlato AS plato_id,
            plato.descripcion AS nombre_plato,
            terminal,
            horaTermino,
            cantidad
        FROM
            detallepedido
        LEFT JOIN plato ON plato.pkPlato = detallepedido.pkPlato
        LEFT JOIN pedido ON pedido.pkPediido = detallepedido.pkPediido
        WHERE
            detallepedido.estado > 0
        AND detallepedido.estado < 3
        AND pedido.estado != 3
        AND pedido.estado = 0
        AND horaPedido BETWEEN CAST(
            '$fI' AS DATETIME
        )
        AND CAST(
            '$fF' AS DATETIME
        )";

        if (count($recetas) == 0) {
            return [];
        } else {
            $temp = implode(', ', array_map(function ($i) {
                return "'${i['plato_id']}'";
            }, $recetas));

            $query .= " and detallepedido.pkPlato in ($temp)";
        }

        $res = $db->executeQueryEx($query);

        $lista = [];

        while($row = $db->fecth_array($res)) {
            $lista[] = $row;
        }

        $data = [];

        foreach ($lista as $pedido) {

            $receta = array_filter($recetas, function ($i) use ($pedido) {
                return $i['plato_id'] == $pedido['plato_id'] && ( is_null($i['terminal'] ) || $i['terminal'] == $pedido['terminal'] );
            });


            foreach ($receta as $row) {
                $data[] = [
                    "insumo_id" => $row['insumo_id'],
                    "insumo_porcion_id" => $row['insumo_porcion_id'],
                    // "plato_id" => $pedido['plato_id'],
                    "cantidad_plato" => $pedido['cantidad'],
                    "cantidad" => $pedido['cantidad'] * $row['cantidad'],
                    "tipo" => 3,
                    "fecha" => $pedido['horaTermino'],
                    "motivo" => $pedido['nombre_plato'],
                    "descripcion" => "Pedido",
                    "code" => $pedido['pkDetallePedido'],
                ];
            }
        }

        return $data;
    }

    /**
     * Obtiene el stock (usando las funciones anteriores) de un solo insumo o porcion
     */
    public function getStockPorInsumoPorcionDetail($fecha, $insumo_id, $insumo_porcion_id = null)
    {
        $data_historial = self::_getDataHistorial($fecha, $insumo_id, $insumo_porcion_id);

        $data_movimientos = self::_getDataMovimientos($fecha, $insumo_id, $insumo_porcion_id); 
                        
        $data_platos_vendidos = self::getDataPlatosVendidos($fecha);

        $data_movimientos_ventas_por_cobrar = self::getDataVentasDetailPorCobrar($fecha, null, $insumo_id, $insumo_porcion_id);

        $db = new SuperDataBase();

        $query = "select fecha from n_historial_stock_insumo order by id asc limit 1";

        $res = $db->executeQueryEx($query);

        $fechaInicioHistorial = date('Y-m-d');

        while ($row = $db->fecth_array($res)) {
            $fechaInicioHistorial = $row['fecha'];
        }

        $data_platos_vendidos_por_cobrar_init = [];
                
        if ($fechaInicioHistorial != $fechaInicio) {
            $data_platos_vendidos_por_cobrar_init = self::getDataPlatosVendidosPorCobrar($fechaInicioHistorial, self::getDiaAnterior($fecha));
        } 

        $data_platos_vendidos_cobrados_hoy = [];

        if ($fechaInicioHistorial != $fechaInicio) {
            $data_platos_vendidos_cobrados_hoy = self::getDataPlatosVendidosPagadosHoy($fechaInicioHistorial, self::getDiaAnterior($fecha));
        } 

        $list = self::_getListaCompleta($insumo_id, $insumo_porcion_id);
        
        if (count($list) > 0) {
            $item = $list[0]; 
        } else {
            echo "Ocurrió un error al obtener el stock del insumo";
            return [];
        }

        $stock_ayer = self::getStockPorInsumo($item, $data_historial);

        $item['stock_ayer'] = is_null($stock_ayer) ? 0 : $stock_ayer['stock'];

        $stock_movimiento = self::getStockPorInsumo($item, $data_movimientos);

        $item['stock_movimiento'] = is_null($stock_movimiento) ? 0 : $stock_movimiento['cantidad'];

        $stock_ventas = self::getStockPorInsumo($item, $data_platos_vendidos);

        $item['stock_ventas'] = is_null($stock_ventas) ? 0 : $stock_ventas['cantidad_insumo'];

        $stock_ventas_cobrar = self::getStockPorInsumo($item, $data_movimientos_ventas_por_cobrar);

        $item['stock_ventas_cobrar'] = is_null($stock_ventas_cobrar) ? 0 : $stock_ventas_cobrar['cantidad'];

        $stock_ventas_cobrar_init = self::getStockPorInsumo($item, $data_platos_vendidos_por_cobrar_init);

        $stock_ventas_cobrados_hoy = self::getStockPorInsumo($item, $data_platos_vendidos_cobrados_hoy);

        $item['stock'] = $item['stock_ayer'] + $item['stock_movimiento'] - $item['stock_ventas'] - $item['stock_ventas_cobrar'];

        $item['stock'] = $item['stock'] - (is_null($stock_ventas_cobrar_init) ? 0 : $stock_ventas_cobrar_init['cantidad_insumo']);

        $item['stock'] = $item['stock'] - (is_null($stock_ventas_cobrados_hoy) ? 0 : $stock_ventas_cobrados_hoy['cantidad_insumo']);

        return $item;
    }

    public function getHistorialesPorHoraPedido($horaPedido, $insumo_id, $insumo_porcion_id, $almacen_id)
    {   
        $db = new SuperDataBase();

        $query = "select * from corte where inicio <= '$horaPedido' and fin >= '$horaPedido' limit 1";

        $res = $db->executeQueryEx($query);

        $data_corte = null;

        while ($row = $db->fecth_array($res)) {
            $data_corte = $row;
        }

        if (is_null($data_corte)) {
            return [];
        }

        $fecha_inicio = $data_corte['fecha_cierre'];

        $query = "SELECT
            *
        FROM
            n_historial_stock_insumo
        WHERE
            insumo_id = $insumo_id";
      
        if ($insumo_porcion_id) {
            $query .= " and insumo_porcion_id = $insumo_porcion_id";
        } else {
            $query .= " and insumo_porcion_id is null";
        }

        $query .= " AND almacen_id = $almacen_id
        AND fecha >= '$fecha_inicio'
        ORDER BY
            fecha";

        $res = $db->executeQueryEx($query);

        $historiales = [];

        while ($row = $db->fecth_array($res)) {
            if ($row['fecha'] == $fecha_inicio) {
                $row['is_primera'] = true;
            }
            $historiales[] = $row;
        }

        return $historiales;
    }

    public function getDiaAnterior($fecha)
    {    
        $db = new SuperDataBase();

        $query = "SELECT
            fecha_cierre
        FROM
            corte
        WHERE
            fecha_cierre < '$fecha'
        ORDER BY
            fecha_cierre DESC
        LIMIT 1";

        $res = $db->executeQueryEx($query);

        $_fecha = date("Y-m-d", strtotime($fechaInicio."- 1 day"));

        while($row = $db->fecth_array($res)) {
            $_fecha = $row['fecha_cierre'];
        }

        return $_fecha;
    }

    public function getStocksFinal($fecha)
    {
        $db = new SuperDataBase();

        $data_historial = $this->getDataHistorial($fecha);

        $data_movimientos = $this->getDataMovimientos($fecha);

        $lista_completa = $this->getListaCompleta();

        $data_platos_vendidos = $this->getDataPlatosVendidos($fecha);

        $query = "select fecha from n_historial_stock_insumo order by id asc limit 1";

        $res = $db->executeQueryEx($query);

        $fechaInicioHistorial = date('Y-m-d');

        while ($row = $db->fecth_array($res)) {
            $fechaInicioHistorial = $row['fecha'];
        }

        $data_platos_vendidos_por_cobrar = $this->getDataPlatosVendidosPorCobrar($fecha);

        $data_platos_vendidos_por_cobrar_init = [];

        if ($fechaInicioHistorial != $fecha) {
            $data_platos_vendidos_por_cobrar_init = $this->getDataPlatosVendidosPorCobrar($fechaInicioHistorial, date("Y-m-d", strtotime($fecha."- 1 day")));
        } 

        $data_platos_vendidos_cobrados_hoy = [];

        if ($fechaInicioHistorial != $fecha) {
            $data_platos_vendidos_cobrados_hoy = $this->getDataPlatosVendidosPagadosHoy($fechaInicioHistorial, date("Y-m-d", strtotime($fecha."- 1 day")));
        } 

        $final = [];

        foreach ($lista_completa as $item) {

            $stock_ayer = $this->getStockPorInsumo($item, $data_historial);

            $item['stock_ayer'] = is_null($stock_ayer) ? 0 : $stock_ayer['stock'];

            $stock_ayer_cobrar = $this->getStockPorInsumo($item, $data_platos_vendidos_por_cobrar_init);

            $item['stock_ayer_cobrar'] = is_null($stock_ayer_cobrar) ? 0 : $stock_ayer_cobrar['cantidad_insumo'];

            $item['stock_ayer'] -= $item['stock_ayer_cobrar'];

            $stock_ventas_cobrados_hoy = $this->getStockPorInsumo($item, $data_platos_vendidos_cobrados_hoy);

            $item['stock_ayer_cobrado'] = (is_null($stock_ventas_cobrados_hoy) ? 0 : $stock_ventas_cobrados_hoy['cantidad_insumo']);
            
            $item['stock_ayer'] -= $item['stock_ayer_cobrado'];

            $stock_movimiento = $this->getStockPorInsumo($item, $data_movimientos);

            $item['stock_movimiento'] = is_null($stock_movimiento) ? 0 : $stock_movimiento['cantidad'];

            $stock_ventas = $this->getStockPorInsumo($item, $data_platos_vendidos);

            $item['stock_ventas'] = is_null($stock_ventas) ? 0 : $stock_ventas['cantidad_insumo'];

            $stock_ventas_cobrar = $this->getStockPorInsumo($item, $data_platos_vendidos_por_cobrar);

            $item['stock_ventas_cobrar'] = is_null($stock_ventas_cobrar) ? 0 : $stock_ventas_cobrar['cantidad_insumo'];


            $item['stock'] = $item['stock_ayer'] + $item['stock_movimiento'] - $item['stock_ventas'] - $item['stock_ventas_cobrar'];

            if (!array_key_exists('insumo_porcion_id', $item)) {
                $item['insumo_porcion_id'] = null;
            }

            $final[] = $item;
        }

        return $final;
    }
}