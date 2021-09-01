<?php

class Application_Controllers_PDFController {

    function __construct() {
        
    }

    /**
     * Genera Reporte de para las vetas y cotizaciones
     * @param int $typeSale 1->Boleta
     *                      2->Factura
     *                      3->Cotizacion
     * @access public
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>  
     * * */
    public function PdfSaleFactureReport($typeFacture, $numberDocument) {

        switch ($typeFacture) {
            case 1:$pkTypeSale = "Boleta";
                break;
            case 2:$pkTypeSale = "Factura";
                break;
            case 3: $pkTypeSale = "Cotizacion";
                break;
        }


        $objModel = new Application_Models_SaleModel();
        $objModel->set_pkSale($numberDocument);
        $objModel->set_typeSale($typeFacture);

        $result = $objModel->listDetailSaleReport();

        $total = 0;
        $company = "";
        $address = "";
        $document = "";
        $fecha = "";
        foreach ($result as $row) {
            $total = $total + $row['subtotal'];
            $company = "";
            $company = $row['company'];
            $address = "";
            $address = $row['address'];
            $document = "";
            $document = $row['document'];
            $fecha = "";
            $fecha = $row['dateSale'];
        }

        $date = strtotime($fecha);
        $showDate = "Piura: " . date('d', $date) . ' de ' . date('m', $date) . " del " . date('Y', $date);
        require('././Components/Library/fpdf/fpdf.php');

        $pdf = new FPDF('P');

        $pdf->AliasNbPages();
        $pdf->SetFont('Times', 'B', 12);

//        $pdf->PrintChapter(1, 'S', '././Components/Library/fpdf/probando.txt');
        $pdf->AddPage();
//         Boleta de Venta 001- N° 000931'
        $pdf->SetXY(150, 5);
        $pdf->Cell(50, 10, utf8_decode('R.U.C. ' . Class_config::get('rucEntreprise')), 1, 1, 'C', false, "as");
        $pdf->SetXY(150, 15);
        $pdf->Cell(50, 10, utf8_decode($pkTypeSale), 1, 1, 'C', false, "as");
        $pdf->SetXY(150, 25);
        $pdf->Cell(50, 10, utf8_decode($numberDocument), 1, 1, 'C', false, "as");

        $pdf->Cell(100, 10, $showDate);
        $pdf->Ln(8);
        $pdf->Cell(100, 10, utf8_decode("Señores : " . $company));
        $pdf->Ln(8);
        $pdf->Cell(100, 10, "Direccion: " . $address);
        $pdf->Ln(8);
        $pdf->Cell(100, 10, utf8_decode("R.U.C. N°: " . $document . " N° Orden de Compra: .......................  N° Guia de Remision: .................."));
        $pdf->Ln(16);
        $header = array('CANT.', 'D E S C R I P C I O N', 'P.UNITARIO', ' VALOR VENTA');
//        $data = $pdf->LoadData('././Components/Library/fpdf/probando.txt');

        $calculaIgv = "1." . Class_config::get('igv');
        $subtotal = round($total / $calculaIgv, 2);

        $data = $result;

        $pdf->boletaVenta($header, $data);
        $pdf->Ln(0);
        $pdf->SetX(128);
        $pdf->Cell(35, 8, "SUB-TOTAL: ", 1, 1, 'C');
        //88
        $next = count($data) * 6;
        $pdf->SetXY(163, ($next + 82));
        $pdf->Cell(35, 8, " S/. " . $subtotal, 1, 1, 'C');

        //IGV
        $pdf->Ln(0);
        $pdf->SetX(128);
        $pdf->Cell(35, 8, "I.G.V(" . Class_config::get('igv') . "%): ", 1, 1, 'C');
        //96
        $pdf->SetXY(163, $next + 90);
        $pdf->Cell(35, 8, " S/. " . ($total - $subtotal), 1, 1, 'C');

        //Total
        $pdf->Ln(0);
        $pdf->SetX(128);
        $pdf->Cell(35, 8, "T O T A L: ", 1, 1, 'C');
        $pdf->SetXY(163, $next + 98);
        $pdf->Cell(35, 8, " S/. " . $total, 1, 1, 'C');

        $pdf->Ln(10);
        $pdf->SetX(90);
        $pdf->Cell(100, 10, $showDate);
        $pdf->Ln(10);
        $pdf->SetX(80);
        $pdf->Cell(90, 10, "...................................................................");

        $pdf->Output();
    }

    public function Cotizacion($typeFacture, $numberDocument) {

        switch ($typeFacture) {
            case 1:$pkTypeSale = "Boleta";
                break;
            case 2:$pkTypeSale = "Factura";
                break;
            case 3: $pkTypeSale = "Cotizacion";
                break;
        }
        $objModel = new Application_Models_SaleModel();
        $objModel->set_pkSale($numberDocument);
        $objModel->set_typeSale($typeFacture);

        $result = $objModel->listDetailSaleReport();

        $total = 0;
        $company = "";
        $address = "";
        $document = "";
        $fecha = "";
        foreach ($result as $row) {
            $total = $total + $row['subtotal'];
            $company = "";
            $company = $row['company'];
            $address = "";
            $address = $row['address'];
            $document = "";
            $document = $row['document'];
            $fecha = "";
            $fecha = $row['dateSale'];
        }

        $date = strtotime($fecha);
        $showDate = "Piura: " . date('d', $date) . ' de ' . date('m', $date) . " del " . date('Y', $date);
        require('././Components/Library/fpdf/fpdf.php');

        $pdf = new FPDF('P');

        $pdf->AliasNbPages();
        $pdf->SetFont('Times', 'B', 12);

//        $pdf->PrintChapter(1, 'S', '././Components/Library/fpdf/probando.txt');
        $pdf->AddPage();
//         Boleta de Venta 001- N° 000931'
        $pdf->SetXY(150, 5);
        $pdf->Cell(50, 10, utf8_decode("E-mail:Ventas@ghostsoluciones.com"), 0, 0, 'C', false, "as");
        $pdf->SetXY(150, 15);
        $pdf->Cell(50, 10, utf8_decode($pkTypeSale . " - " . $numberDocument), 0, 0, 'C', false, "as");
//        $pdf->SetXY(150, 25);
//        $pdf->Cell(50, 10, utf8_decode($numberDocument), 1, 1, 'C', false, "as");
        $pdf->Ln(10);
        $pdf->Cell(100, 10, $showDate);
        $pdf->Ln(8);
        $pdf->Cell(100, 10, utf8_decode("Señor(es) : "));
        $pdf->Ln(8);
        $pdf->Cell(100, 10, "                        $company");
        $pdf->Ln(8);
        $pdf->Cell(100, 10, "Nos complace enviar nuestra mejor propuesta de disponibilidad de precios del material solicitado:");
        $pdf->Ln(8);
//        $pdf->Cell(100, 10, utf8_decode("R.U.C. N°: " . $document . " N° Orden de Compra: .......................  N° Guia de Remision: .................."));
        $pdf->Ln(10);
        $header = array('Item', 'D E S C R I P C I O N', 'P.UNITARIO', ' VALOR VENTA');
//        $data = $pdf->LoadData('././Components/Library/fpdf/probando.txt');
 
       $calculaIgv = "1." . Class_config::get('igv');
        $subtotal = round($total / $calculaIgv, 2);

        $data = $result;

        $pdf->boletaVenta($header, $data);
        $pdf->Ln(0);
        $pdf->SetX(128);
        $pdf->Cell(35, 8, "SUB-TOTAL: ", 1, 1, 'C');
        //88
        $next = count($data) * 6;
        $pdf->SetXY(163, ($next + 74));
        $pdf->Cell(35, 8, " S/. " . $subtotal, 1, 1, 'C');

        //IGV
        $pdf->Ln(0);
        $pdf->SetX(128);
        $pdf->Cell(35, 8, "I.G.V(" . Class_config::get('igv') . "%): ", 1, 1, 'C');
        //96
        $pdf->SetXY(163, $next + 82);
        $pdf->Cell(35, 8, " S/. " . ($total - $subtotal), 1, 1, 'C');

        //Total
        $pdf->Ln(0);
        $pdf->SetX(128);
        $pdf->Cell(35, 8, "T O T A L: ", 1, 1, 'C');
        $pdf->SetXY(163, $next + 90);
        $pdf->Cell(35, 8, " S/. " . $total, 1, 1, 'C');

        $pdf->Ln(10);
        $pdf->Line(143, 150, 143, 150);
        $pdf->Write(5, "Condiciones Comerciales");

//        $pdf->SetX(90);
        
        $pdf->Output();
    }

}
