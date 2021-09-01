<?php

class Application_Controllers_MontosCajaGraphController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'GetDataGraphAction':
                $this->_GetDataGraph();
                break;

        }
    }

    public function _GetDataGraph()
    {

        $fecha = $_REQUEST['inicio'];

        $fechaFin = $_REQUEST['fin'];

        $data = [];

        $type = $_REQUEST['group_by'];

        $_sql1 = "";
        $_sql2 = "";
        $_sql3 = "";

        $convertir_mes = false;

        switch ($type) {
            case 1:
                $_sql1 = "pedido.fechaCierre";
                $_sql2 = "compras.fecha";
                $_sql3 = "fecha_cierre";
            break;
            case 2:
                $_sql1 = "CONCAT(MONTH(pedido.fechaCierre), '-', YEAR(pedido.fechaCierre))";
                $_sql2 = "CONCAT(MONTH(compras.fecha), '-', YEAR(compras.fecha))";
                $_sql3 = "CONCAT(MONTH(fecha_cierre), '-', YEAR(fecha_cierre))";
                $convertir_mes = true;
            break;
            case 3:
                $_sql1 = "YEAR(pedido.fechaCierre)";
                $_sql2 = "YEAR(compras.fecha)";
                $_sql3 = "YEAR(fecha_cierre)";
            break;
        }

        $db = new SuperDataBase();

        $labels = [];
        $data_ventas = [];
        $data_compras = [];
        $data_gastos = [];

        $query = "SELECT
            $_sql1 as label,
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
            $_sql1
        ";

        $res = $db->executeQueryEx($query);

        while ($row = $db->fecth_array($res)) {
            $labels[] = $row['label'];   
            $data_ventas[$row['label']] = $row['total'];   
        }

        $query = "SELECT
            $_sql2 as label,
            sum(
                compras.total
            ) AS total
        FROM
            compras
        WHERE
            compras.fecha BETWEEN '$fecha'
        AND '$fechaFin'
        AND compras.fecha_caja is not null
        GROUP BY
            $_sql2
        ";

        $res = $db->executeQueryEx($query);

        while ($row = $db->fecth_array($res)) {
            $labels[] = $row['label'];   
            $data_compras[$row['label']] = $row['total'];   
        }

        $query = "SELECT
            $_sql3 AS label,
            sum(monto) AS total
        FROM
            movimiento_dinero
        WHERE
            fecha_cierre BETWEEN '2020-12-01'
        AND '2020-12-02'
        and tipo_origen = 'GAS'
        GROUP BY
            $_sql3
        ";

        $res = $db->executeQueryEx($query);

        while ($row = $db->fecth_array($res)) {
            $labels[] = $row['label'];   
            $data_gastos[$row['label']] = $row['total'];   
        }

        sort($labels);

        // echo "<br><br>".json_encode($labels);
        // echo "<br><br>".json_encode($data_ventas);
        // echo "<br><br>".json_encode($data_compras);

        $new_labels = [];
        $ventas = [];
        $compras = [];
        $gastos = [];

        foreach ($labels as $label) {

            $key = array_search($label, $new_labels);

            if ($key === false) {
                # no se encontró

                $ventas[] = array_key_exists($label, $data_ventas) ? round($data_ventas[$label], 2) : 0;
                $compras[] = array_key_exists($label, $data_compras) ? round($data_compras[$label], 2) : 0;
                $gastos[] = array_key_exists($label, $data_gastos) ? round($data_gastos[$label], 2) : 0;
                $new_labels[] = $label;
            }

            // echo "<br><br>".json_encode($new_labels);
            // echo "<br><br>".json_encode($ventas);
            // echo "<br><br>".json_encode($compras);
        }

        if ($convertir_mes) {
            foreach ($new_labels as $index => $label) {
                $new_labels[$index] = $this->convertirMes($label);
            }
        }
        
        $send = [
            "labels" => $new_labels,
            "ventas" => $ventas,
            "compras" => $compras,
            "gastos" => $gastos,
        ];

        echo json_encode($send);
    }

    private function convertirMes($label) {
        $parts = explode('-', $label);

        $mes = intval($parts[0]);

        switch ($mes) {
            case 1: $_mes = 'Ene'; break;
            case 2: $_mes = 'Feb'; break;
            case 3: $_mes = 'Mar'; break;
            case 4: $_mes = 'Mar'; break;
            case 5: $_mes = 'May'; break;
            case 6: $_mes = 'Jun'; break;
            case 7: $_mes = 'Jul'; break;
            case 8: $_mes = 'Ago'; break;
            case 9: $_mes = 'Set'; break;
            case 10: $_mes = 'Oct'; break;
            case 11: $_mes = 'Nov'; break;
            case 12: $_mes = 'Dic'; break;
        }

        return $_mes . ", " . $parts[1];
    }
}