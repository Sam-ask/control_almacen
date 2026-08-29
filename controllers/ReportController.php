<?php
require_once '../config/security.php'; 
require_once '../config/database.php';
require_once '../models/Producto.php';
require_once '../libs/fpdf/vendor/autoload.php';

class PDF extends FPDF {
    function Header() {
        $this->SetFillColor(15, 23, 42); 
        $this->Rect(0,0,210,35,'F');
        
        $this->SetY(8);
        $this->SetFont('Arial','B',16);
        $this->SetTextColor(255,255,255);
        $this->Cell(0, 8, utf8_decode('WMS ALMACÉN - Reporte de Inventario'), 0, 1, 'L');
        
        $this->SetFont('Arial','',9);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(0, 5, utf8_decode('Planta: Washington | Generado por: Sistema WMS | ') . date('d/m/Y H:i'), 0, 1, 'L');
        $this->Ln(15);

        $this->SetFillColor(30, 64, 175); 
        $this->SetTextColor(255);
        $this->SetDrawColor(226, 232, 240);
        $this->SetLineWidth(.2);
        $this->SetFont('Arial','B',9);
        
        $this->Cell(28, 9, 'SKU', 1, 0, 'C', true);
        $this->Cell(70, 9, utf8_decode('Descripción'), 1, 0, 'C', true);
        $this->Cell(42, 9, 'Proveedor', 1, 0, 'C', true);
        $this->Cell(25, 9, 'Stock Min', 1, 0, 'C', true);
        $this->Cell(25, 9, 'Stock Actual', 1, 1, 'C', true);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',7);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(0, 10, utf8_decode('Documento interno controlado - WMS v1.0 | Página ').$this->PageNo().'/{nb}', 0, 0, 'C');
        $this->Cell(0, 10, 'control_almacen_wms', 0, 0, 'R');
    }
}

$database = new Database();
$db = $database->getConnection();
$producto = new Producto($db);
$datosInventario = $producto->listarTodos();

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);

$fill = false;
$totalCriticos = 0;

foreach($datosInventario as $row) {
    $isCritico = floatval($row['stock']) <= floatval($row['stock_minimo']);
    if($isCritico) $totalCriticos++;

    if($fill) $pdf->SetFillColor(248, 250, 252);
    else $pdf->SetFillColor(255,255,255);

    if($isCritico){
        $pdf->SetFillColor(254, 226, 226); 
    }

    $pdf->SetTextColor(30, 41, 59);
    $pdf->SetFont('Arial','',9);
    
    $pdf->Cell(28, 8, utf8_decode($row['sku']), 1, 0, 'L', true);
    $pdf->Cell(70, 8, utf8_decode(substr($row['nombre'], 0, 38)), 1, 0, 'L', true);
    $pdf->Cell(42, 8, utf8_decode(substr($row['proveedor'], 0, 22)), 1, 0, 'L', true);
    $pdf->Cell(25, 8, $row['stock_minimo'], 1, 0, 'C', true);

    if ($isCritico) {
        $pdf->SetTextColor(220, 38, 38);
        $pdf->SetFont('Arial','B',9);
    } else {
        $pdf->SetTextColor(22, 101, 52);
        $pdf->SetFont('Arial','B',9);
    }
    $pdf->Cell(25, 8, $row['stock'], 1, 1, 'C', true);
    
    $fill = !$fill;
}

$pdf->Ln(5);
$pdf->SetFillColor(15, 23, 42);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(0, 8, '  Resumen: Total Productos: '.count($datosInventario).' | Stock Critico: '.$totalCriticos.' | En Stock: '.(count($datosInventario)-$totalCriticos), 1, 1, 'L', true);

$pdf->Output('I', 'Reporte_Inventario_' . date('Ymd') . '.pdf');
?>