<?php

class ComprasHelper 
{
    protected $igv = 0.18;
    protected $tasa_icbper = 0.2;

    public function setIGV($igv)
    {
        if ($igv > 1) {
            $igv = $igv / 100;
        } 
        
        $this->igv = $igv; 
    }

    public function setICBPER($icbper)
    {
        $this->tasa_icbper = $icbper; 
    }

    public function getSQLDetalles($id) 
    {   
        $query = "SELECT
            detalle_compras.*, insumos.descripcionInsumo AS insumo_nombre,
            compras_concepto.nombre AS concepto_nombre,
            tipo_impuesto.nombre AS tipo_impuesto_nombre
        FROM
            detalle_compras
        LEFT JOIN insumos ON detalle_compras.insumo_id = insumos.pkInsumo
        LEFT JOIN tipo_impuesto ON detalle_compras.tipo_impuesto_id = tipo_impuesto.id
        LEFT JOIN compras_concepto ON detalle_compras.concepto_id = compras_concepto.id
        WHERE compra_id = $id
        AND deleted_at is null
        ";

        return $query;
    }
    public function getSQLDocumentos($id) 
    {   
        $query = "SELECT
            *
        FROM
            compras_documentos
        WHERE
            compra_id = $id
        AND deleted_at is null
        ";

        return $query;
    }

    public function calculateTotales($compra_id)
    {

        $db = new SuperDataBase();

        $totales = [
            "gravado" => 0,
            "inafecto" => 0,
            "exonerado" => 0,
            "gratuito" => 0, 
            "icbper" => 0,

            "subtotal" => 0,
            "igv" => 0,
            "descuento" => 0,
            "total" => 0,

            "total_detraccion" => 0,
            "total_percepcion" => 0,
            "total_retencion" => 0,
        ];

        $query = $this->getSQLDetalles($compra_id);

        $res = $db->executeQueryEx($query);

        while ($row = $db->fecth_array($res)) {
            $_temp = $this->calculateDetailTotales($row);

            foreach ($totales as $index_total => $total) {
                if (array_key_exists($index_total, $_temp)) {
                    $totales[$index_total] += $_temp[$index_total];
                }
            }
        }

        $query = $this->getSQLDocumentos($compra_id);

        $res = $db->executeQueryEx($query);

        while ($row = $db->fecth_array($res)) {
            $_temp = $this->calculateDocumentosTotales($row, $totales['total']);

            foreach ($totales as $index_total => $total) {
                if (array_key_exists($index_total, $_temp)) {
                    $totales[$index_total] += $_temp[$index_total];
                }
            }
        }


        // echo "<br>" . json_encode($totales);

        return $totales;
    }

    public function calculateDetailTotales($row)
    {
        
        $db = new SuperDataBase();

        $totales = [
            "gravado" => 0,
            "inafecto" => 0,
            "exonerado" => 0,
            "gratuito" => 0, 
            "icbper" => 0,

            "subtotal" => 0,
            "igv" => 0,
            "descuento" => 0,
            "total" => 0,
        ];

        switch ($row['tipo_impuesto_id']) {

            case 1:
                $subtotal = floatval(($row['precio'] * $row['cantidad']) - $row['descuento']);

                $valor_unitario = floatval($subtotal / (1 + $this->igv));
                $igv_unitario = floatval($subtotal - $valor_unitario);

                $totales['gravado'] = floatval($valor_unitario);
                
                $neto = floatval($row['precio'] * $row['cantidad']);
                break;

            case 2:
                $subtotal = floatval(($row['precio'] * $row['cantidad']) - $row['descuento']);

                $valor_unitario = floatval($subtotal);
                $igv_unitario = 0;
                $totales['inafecto'] = floatval($subtotal);
                $neto = floatval($row['precio'] * $row['cantidad']);
                break;

            case 3:
                $subtotal = floatval(($row['precio'] * $row['cantidad']) - $row['descuento']);

                $valor_unitario = floatval($subtotal);
                $igv_unitario = 0;
                $totales['exonerado'] = floatval($subtotal);
                $neto = floatval($row['precio'] * $row['cantidad']);
                break;

            case 4:
                $subtotal = floatval(($row['precio'] * $row['cantidad']) - $row['descuento']);

                $valor_unitario = floatval($subtotal);
                $igv_unitario = 0;
                $totales['gratuito'] = floatval($subtotal);
                $neto = 0;
                $row['descuento'] = 0;
                break;

            case 5:
                // echo "<b>---------------->VERIFICAR LOGICA DE ICBPER<----------------</b>";

                $precio_sin_impuesto = floatval($row['precio']) - $this->tasa_icbper;

                if ($precio_sin_impuesto > 0) {

                    $valor_unitario = floatval($precio_sin_impuesto / (1 + $this->igv));
                    $igv_unitario = floatval($precio_sin_impuesto - $valor_unitario);

                    $totales['gravado'] = floatval($valor_unitario * $row['cantidad']);
                    
                    $neto = floatval($row['precio'] * $row['cantidad']);

                    $igv_unitario = floatval($igv_unitario * $row['cantidad']);

                    $totales['icbper'] = floatval($this->tasa_icbper * $row['cantidad']);

                } else {

                    $igv_unitario = 0;
                    $totales['icbper'] = floatval($row['precio'] * $row['cantidad']);
                    $neto = floatval(($row['precio'] + $igv_unitario) * $row['cantidad']);
                }

                
                break;

            default:
                $valor_unitario = 0;
                $igv_unitario = 0;
                $neto = 0;
                $row['descuento'] = 0;
        }

        $totales['igv'] = $igv_unitario;
        $totales['subtotal'] = $neto;
        $totales['descuento'] = $row['descuento'];
        $totales['total'] = $neto - $row['descuento'];

        return $totales;
    }

    public function calculateDocumentosTotales($row, $total)
    {

        $totales = [
            "total_detraccion" => 0,
            "total_percepcion" => 0,
            "total_retencion" => 0,

            "total" => 0,
        ];

        switch ($row['documento_id']) {
            case 1: 
                //echo 'DETRACCIÓN'; 
                $totales['total_detraccion'] = $total * $row['porcentaje'] / 100;
                break;
            case 2: 
                //echo 'PERCEPCIÓN'; 
                $totales['total_percepcion'] = $total * $row['porcentaje'] / 100;
                break;
            case 3: 
                //echo 'RETENCIÓN';
                $totales['total_retencion'] = $total * $row['porcentaje'] / 100;
                break;
        }

        $totales['total'] += $totales['total_percepcion'];
        $totales['total'] -= $totales['total_retencion'];

        return $totales;

    }
}