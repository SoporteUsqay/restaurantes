<?php

class Application_Controllers_DashboardController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ShowAction':
                require_once 'Application/Views/Dashboard/Dashboard.php';
                break;

            case 'GetTotalesAction':
                $this->_GetTotales();
                break;

            case 'GetDataGraphVentasAction':
                $this->_GetDataGraphVentas();
                break;

            case 'GetDataGraphSalonesAction':
                $this->_GetDataGraphSalones();
                break;

            case 'GetDataExtraAction':
                $this->_GetDataExtra();
                break;

            case 'GetDataTablesAction':
                $this->_GetDataTables();
                break;

        }
    }

    private function _getRangeDates()
    {
        $caja = new Application_Models_CajaModel();

        $fecha = $caja->fechaCierre();

        $fechaFin = date('Y-m-d');

        $type = $_REQUEST['filter'];

        switch ($type) {
            case 1:
            break;
            case 2:
                $fecha = date('Y-m-d', strtotime($fecha . "- 1 week"));
            break;
            case 3:
                $fecha = date('Y-m-d', strtotime($fecha . "- 1 month"));
            break;
        }

        return [$fecha, $fechaFin];
    }

    public function _GetTotales()
    {
        $data_range_dates = $this->_getRangeDates();

        $fecha = $data_range_dates[0];

        $fechaFin = $data_range_dates[1];

        $data = [];

        $db = new SuperDataBase();

        $query = "SELECT
            IFNULL(sum(cantidad * precio), 0.00) as total_mesas
        FROM
            detallepedido, pedido
        WHERE detallepedido.estado > 0 and detallepedido.estado < 3
        AND detallepedido.pkPediido = pedido.pkPediido
        AND pedido.estado = 0 
        AND pedido.fechaCierre BETWEEN '$fecha'
        AND '$fechaFin'";

        $res = $db->executeQueryEx($query);

        $total = 0;
        $total_anulado = 0;
        $total_consumo = 0;
        $total_credito = 0;
        $total_mesas = 0;
        $cantidad = 0;
        $cantidad_mesas_por_cobrar = 0;
        $cantidad_consumo = 0;
        $cantidad_credito = 0;
        $cantidad_anuladas = 0;
        $cantidad_atendidas = 0;
        $descuentos = 0;

        while ($row = $db->fecth_array($res)) {
            $total_mesas = $row['total_mesas'];
        }

        $query = "SELECT
            COUNT(*) AS cantidad,
            COUNT(CASE WHEN estado = 0 THEN 1 END) AS cantidad_mesas_por_cobrar,
            COUNT(CASE WHEN estado = 1 THEN 1 END) AS cantidad_atendidas,
            COUNT(CASE WHEN estado = 4 THEN 1 END) AS cantidad_credito,
            COUNT(CASE WHEN estado = 5 THEN 1 END) AS cantidad_consumo,
            COUNT(CASE WHEN estado = 3 THEN 1 END) AS cantidad_anuladas,

            IFNULL(sum(if(estado = 1, total, 0)), 0.00) AS total_venta,
            IFNULL(sum(if(estado = 3, total, 0)), 0.00) AS total_anulado,
            IFNULL(sum(if(estado = 4, total, 0)), 0.00) AS total_credito,
            IFNULL(sum(if(estado = 5, total, 0)), 0.00) AS total_consumo,
            IFNULL(sum(descuento), 0.00) AS descuentos
        FROM
            pedido
        WHERE
            fechaCierre BETWEEN '$fecha'
        AND '$fechaFin'";

        $res = $db->executeQueryEx($query);

        while ($row = $db->fecth_array($res)) {
            $total = $row['total_venta'];
            $total_anulado = $row['total_anulado'];
            $total_consumo = $row['total_consumo'];
            $total_credito = $row['total_credito'];
            $cantidad = $row['cantidad'];
            $cantidad_credito = $row['cantidad_credito'];
            $cantidad_consumo = $row['cantidad_consumo'];
            $cantidad_anuladas = $row['cantidad_anuladas'];
            $cantidad_atendidas = $row['cantidad_atendidas'];
            $cantidad_mesas_por_cobrar = $row['cantidad_mesas_por_cobrar'];
            $descuentos = $row['descuentos'];
        }

        $query = "SELECT
            medio_pago.nombre,
            moneda.simbolo AS moneda,
            (
                SELECT
                    IFNULL(sum(movimiento_dinero.monto), 0) AS monto
                FROM
                    movimiento_dinero, pedido
                WHERE
                    movimiento_dinero.fecha_cierre BETWEEN '$fecha' AND '$fechaFin'
                AND movimiento_dinero.tipo_origen = 'PED'
                AND pedido.pkPediido = movimiento_dinero.id_origen
                AND movimiento_dinero.id_medio = medio_pago.id
                AND pedido.estado != 3
            ) AS total
        FROM
            medio_pago
        LEFT JOIN moneda ON medio_pago.moneda = moneda.id
        WHERE medio_pago.estado = 1
        ORDER BY medio_pago.id";

        $res = $db->executeQueryEx($query);

        $data['total'] = [
            "nombre" => "total",
            "moneda" => "S/ ",
            "total" => number_format($total, 2)
        ];

        $data['total_anulado'] = [
            "nombre" => "total_anulado",
            "moneda" => "S/ ",
            "total" => number_format($total_anulado, 2)
        ];

        $data['total_mesas'] = [
            "nombre" => "total_mesas",
            "moneda" => "S/ ",
            "total" => number_format($total_mesas, 2)
        ];

        $data['total_consumo'] = [
            "nombre" => "total_consumo",
            "moneda" => "S/ ",
            "total" => number_format($total_consumo, 2)
        ];

        $data['total_credito'] = [
            "nombre" => "total_credito",
            "moneda" => "S/ ",
            "total" => number_format($total_credito, 2)
        ];

        $data['total_with_mesas'] = [
            "nombre" => "total_with_mesas",
            "moneda" => "S/ ",
            "total" => number_format($total + $total_mesas, 2)
        ];

        $data['total_with_cred_con'] = [
            "nombre" => "total_with_cred_con",
            "moneda" => "S/ ",
            "total" => number_format($total + $total_mesas + $total_credito + $total_consumo, 2)
        ];

        $data['cantidad_mesas_por_cobrar'] = [
            "nombre" => "cantidad_mesas_por_cobrar",
            "total" => number_format($cantidad_mesas_por_cobrar, 0)
        ];

        $data['cantidad_consumo'] = [
            "nombre" => "cantidad_consumo",
            "total" => number_format($cantidad_consumo, 0)
        ];

        $data['cantidad_credito'] = [
            "nombre" => "cantidad_credito",
            "total" => number_format($cantidad_credito, 0)
        ];

        $data['cantidad_atendidas'] = [
            "nombre" => "cantidad_atendidas",
            "total" => number_format($cantidad_atendidas, 0)
        ];

        $data['cantidad_anuladas'] = [
            "nombre" => "cantidad_anuladas",
            "total" => number_format($cantidad_anuladas, 0)
        ];

        $data['descuentos'] = [
            "nombre" => "descuentos",
            "moneda" => "S/ ",
            "total" => number_format($descuentos, 2)
        ];

        $data['medios'] = [];

        while ($row = $db->fecth_array($res)) {
            $row['total'] = number_format($row['total'], 2);
            $data['medios'][] = $row;
        }

        $query = "SELECT
            medio_pago.nombre,
            moneda.simbolo AS moneda, 
            (
                SELECT
                    IFNULL(sum(movimiento_dinero.monto), 0) AS monto
                FROM
                    movimiento_dinero
                WHERE
                    movimiento_dinero.fecha_cierre BETWEEN '$fecha' AND '$fechaFin'
                AND movimiento_dinero.tipo_origen = 'COM'
                AND movimiento_dinero.id_medio = medio_pago.id
                AND movimiento_dinero.estado = 1
            ) AS total
        FROM
            medio_pago
        LEFT JOIN moneda ON medio_pago.moneda = moneda.id
        WHERE medio_pago.estado = 1
        ORDER BY medio_pago.id";

        $res = $db->executeQueryEx($query);

        $data['medios_compras'] = [];
        $total_compras = 0;

        while ($row = $db->fecth_array($res)) {
            $total_compras += $row['total'];
            $row['total'] = number_format(abs($row['total']), 2);
            $data['medios_compras'][] = $row;
        }

        $data['total_compras'] = [
            "nombre" => "total_compras",
            "moneda" => "S/ ",
            "total" => number_format(abs($total_compras), 2)
        ];

        $query = "SELECT
            IFNULL(
                sum(movimiento_dinero.monto),
                0
            ) AS total
        FROM
            movimiento_dinero
        WHERE
            movimiento_dinero.fecha_cierre BETWEEN '$fecha' AND '$fechaFin'
        AND movimiento_dinero.tipo_origen = 'GAS'
        AND movimiento_dinero.estado = 1";

        $res = $db->executeQueryEx($query);

        $total_movimientos = 0;

        while ($row = $db->fecth_array($res)) {
            $total_movimientos = $row['total'];
        }

        $data['total_movimientos'] = [
            "nombre" => "total_movimientos",
            "moneda" => "S/ ",
            "total" => number_format(($total_movimientos), 2)
        ];

        $query = "SELECT
            IFNULL(
                sum(monto),
                0
            ) AS total
        FROM
            pedido_propina
        WHERE
            fecha_cierre BETWEEN '$fecha' AND '$fechaFin'
        ";

        $res = $db->executeQueryEx($query);

        $total_propinas = 0;

        while ($row = $db->fecth_array($res)) {
            $total_propinas = $row['total'];
        }

        $data['total_propinas'] = [
            "nombre" => "total_propinas",
            "moneda" => "S/ ",
            "total" => number_format(($total_propinas), 2)
        ];


        $data['total_caja'] = [
            "nombre" => "total_caja",
            "moneda" => "S/ ",
            "total" => number_format($total + $total_compras + $total_movimientos, 2)
        ];

        $data['ref_fechas'] = [
            "inicio" => $fecha,
            "fin" => $fechaFin 
        ];

        echo json_encode($data);
    }

    private function parseHora($hora)
    {
        if (is_null($hora)) return 0;

        $sections = explode(':', $hora);

        $string = "";

        foreach ($sections as $index => $item) {

            if ($index == 0) {
                if ($item > 168) {
                    $string = "Más de 1 sem"; break;
                } else {
                    if ($item > 24) {
                        $string = intval($item / 24) . "d";
                    } else {
                        if ($item > 0) {
                            $string = "${item}h";
                        }
                    }
                }
            }

            if ($index == 1) {
                if ($item > 0) {
                    $string .= " ${item}m";
                }
            }

            if ($index == 2) {
                $string .= " ${item}s";
            }

        }

        return $string;
    }

    public function _GetDataExtra()
    {
        $data_range_dates = $this->_getRangeDates();

        $fecha = $data_range_dates[0];

        $fechaFin = $data_range_dates[1];

        $data = [];

        $db = new SuperDataBase();

        $query = "SELECT
            AVG(total) AS promedio_mesa,
            MIN(TIMEDIFF(fechaFin, fechaApertura)) as tiempo_minimo,
	        MAX(TIMEDIFF(fechaFin, fechaApertura)) as tiempo_maximo
        FROM
            pedido
        WHERE
            pedido.fechaCierre BETWEEN '$fecha'
        AND '$fechaFin'
        AND estado IN (1, 4, 5)";

        $res = $db->executeQueryEx($query);

        $promedio_mesa = 0;
        $tiempo_minimo = 0;
        $tiempo_maximo = 0;

        while ($row = $db->fecth_array($res)) {
            $promedio_mesa = $row['promedio_mesa'];
            $tiempo_minimo = $row['tiempo_minimo'];
            $tiempo_maximo = $row['tiempo_maximo'];
        }

        $data['promedio_mesa'] = [
            "nombre" => "promedio_mesa",
            "moneda" => "S/ ",
            "total" => number_format($promedio_mesa, 2)
        ];

        $data['tiempo_minimo'] = [
            "nombre" => "tiempo_minimo",
            "moneda" => "S/ ",
            "total" => $this->parseHora($tiempo_minimo)
        ];

        $data['tiempo_maximo'] = [
            "nombre" => "tiempo_maximo",
            "moneda" => "S/ ",
            "total" => $this->parseHora($tiempo_maximo)
        ];

        $query = "SELECT
            pkMozo,
            CONCAT(
                trabajador.apellidos,
                ' ',
                trabajador.nombres
            ) AS trabajador,
            count(
                detallepedido.pkDetallePedido
            ) AS cantidad,
            sum(
                detallepedido.cantidad * detallepedido.precio
            ) AS total
        FROM
            detallepedido,
            pedido,
            trabajador
        WHERE
            detallepedido.pkPediido = pedido.pkPediido
        AND detallepedido.pkMozo = trabajador.pkTrabajador
        AND pedido.fechaCierre BETWEEN '$fecha'
        AND '$fechaFin'
        AND detallepedido.estado != 3
        AND pedido.estado IN (1, 4, 5)
        GROUP BY
            pkMozo
        ORDER BY
            cantidad DESC
        LIMIT 1";

        $res = $db->executeQueryEx($query);

        $data['mozo'] = [];

        while ($row = $db->fecth_array($res)) {
            $data['mozo'] = [
                "nombre" => "mozo",
                "moneda" => "S/ ",
                "nombre" => $row['trabajador'],
                "cantidad" => $row['cantidad'],
                "total" => number_format($row['total'], 2)
            ];
        }

        

        echo json_encode($data);
    }

    public function _GetDataTables()
    {
        $data_range_dates = $this->_getRangeDates();

        $fecha = $data_range_dates[0];

        $fechaFin = $data_range_dates[1];

        $data = [];

        $db = new SuperDataBase();

        $query = "SELECT
            plato.pkPlato,
            plato.descripcion AS plato,
            sum(
                detallepedido.cantidad
            ) AS cantidad,
            sum(
                detallepedido.cantidad * detallepedido.precio
            ) AS total
        FROM
            detallepedido,
            pedido,
            plato
        WHERE
            detallepedido.pkPediido = pedido.pkPediido
        AND detallepedido.pkPlato = plato.pkPlato
        AND pedido.fechaCierre BETWEEN '$fecha'
        AND '$fechaFin'
        AND detallepedido.estado != 3
        AND pedido.estado IN (1, 4, 5)
        GROUP BY
            pkPlato
        ORDER BY
            total desc,
            cantidad DESC
        LIMIT 10";
        
        $res = $db->executeQueryEx($query);

        $data['platos'] = [];

        while ($row = $db->fecth_array($res)) {
            $data['platos'][] = [
                "moneda" => "S/ ",
                "nombre" => $row['plato'],
                "cantidad" => $row['cantidad'],
                "total" => number_format($row['total'], 2)
            ];
        }

        $query = "SELECT
            comprobante.ruc,
            comprobante.documento,
            person.nombres,
	        persona_juridica.razonSocial,
            count(
                detallecomprobante.pkPediido
            ) AS cantidad,
            sum(detallecomprobante.total) AS total
        FROM
            detallecomprobante
        LEFT JOIN pedido ON pedido.pkPediido = detallecomprobante.pkPediido
        LEFT JOIN comprobante ON comprobante.pkComprobante = detallecomprobante.pkComprobante
        LEFT JOIN person ON person.documento = comprobante.documento
        LEFT JOIN persona_juridica ON persona_juridica.ruc = comprobante.ruc
        WHERE
            pedido.fechaCierre BETWEEN '$fecha'
        AND '$fechaFin'
        AND pedido.estado IN (1, 4, 5)
        GROUP BY
            ruc,
            documento,
            nombres,
	        razonSocial
        ORDER BY
            total desc,
            cantidad desc
        LIMIT 10";
        
        $res = $db->executeQueryEx($query);

        $data['clientes'] = [];

        while ($row = $db->fecth_array($res)) {
            $data['clientes'][] = [
                "moneda" => "S/ ",
                "nombre" => implode(" ", [
                    $row['ruc'],
                    $row['documento'],
                    ",",
                    $row['nombres'],
                    $row['razonSocial'],
                ]),
                "cantidad" => $row['cantidad'],
                "total" => number_format($row['total'], 2)
            ];
        }
        

        echo json_encode($data);
    }

    public function _GetDataGraphVentas()
    {
        $data_range_dates = $this->_getRangeDates();

        $fecha = $data_range_dates[0];

        $fechaFin = $data_range_dates[1];

        $data = [];

        $type = $_REQUEST['filter'];

        $_sql1 = "";
        $_sql2 = "";
        $_title = "";

        switch ($type) {
            case 1:
                $_sql1 = "CONCAT(HOUR(pedido.fechaFin), ':00')";
                $_sql2 = "HOUR(pedido.fechaFin)";
                $_title = "Del día $fecha";
            break;
            case 2:
                $_sql1 = "pedido.fechaCierre";
                $_sql2 = "pedido.fechaCierre";
                $_title = "Del $fecha al $fechaFin";
            break;
            case 3:
                $_sql1 = "pedido.fechaCierre";
                $_sql2 = "pedido.fechaCierre";
                $_title = "Del $fecha al $fechaFin";
            break;
        }

        $db = new SuperDataBase();

        $query = "SELECT
            $_sql1 as label,
            count(detallepedido.cantidad) AS cantidad,
            sum(
                (detallepedido.cantidad * detallepedido.precio) - pedido.descuento
            ) AS total
        FROM
            detallepedido,
            pedido
        WHERE
            detallepedido.pkPediido = pedido.pkPediido
        AND pedido.fechaCierre BETWEEN '$fecha'
        AND '$fechaFin'
        AND detallepedido.estado != 3
        AND pedido.estado IN (1, 4, 5)
        GROUP BY
            $_sql2
        ";
        
        $res = $db->executeQueryEx($query);

        $labels = [];
        $data_cantidades = [];
        $data_montos = [];

        while ($row = $db->fecth_array($res)) {
            $labels[] = $row['label'];   
            $data_cantidades[] = $row['cantidad'];   
            $data_montos[] = round($row['total'], 2);   
        }
        
        $send = [
            "title" => $_title,
            "labels" => $labels,
            "data_cantidades" => $data_cantidades,
            "data_montos" => $data_montos,
        ];

        echo json_encode($send);
    }

    public function _GetDataGraphSalones()
    {
        $data_range_dates = $this->_getRangeDates();

        $fecha = $data_range_dates[0];

        $fechaFin = $data_range_dates[1];

        $data = [];

        $db = new SuperDataBase();

        $query = "SELECT
            salon.pkSalon,
            salon.nombre AS salon,
            count(detallepedido.cantidad) AS cantidad,
            sum(
                (detallepedido.cantidad * detallepedido.precio) - pedido.descuento
            ) AS total
        FROM
            detallepedido,
            pedido,
            mesas,
            salon
        WHERE
            detallepedido.pkPediido = pedido.pkPediido
        AND pedido.pkMesa = mesas.pkMesa
        AND mesas.pkSalon = salon.pkSalon
        AND pedido.fechaCierre BETWEEN '$fecha'
        AND '$fechaFin'
        AND detallepedido.estado != 3
        AND pedido.estado IN (0, 1, 4, 5)
        GROUP BY
            pkSalon
        ORDER BY
            cantidad DESC
        ";
        
        $res = $db->executeQueryEx($query);

        $labels = [];
        $data_cantidades = [];
        $data_montos = [];

        while ($row = $db->fecth_array($res)) {
            $labels[] = $row['salon'];   
            $data_cantidades[] = $row['cantidad'];   
            $data_montos[] = round($row['total'], 2);   
        }
        
        $send = [
            "labels" => $labels,
            "data_cantidades" => $data_cantidades,
            "data_montos" => $data_montos,
        ];

        echo json_encode($send);
    }
}