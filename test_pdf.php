<?php
require_once 'libs/fpdf/vendor/autoload.php';

if (!isset($_GET['po'])) {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Generador de POs</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <style>
            body { background-color: #f4f7f6; padding-top: 40px; }
            .po-card { transition: transform 0.2s; }
            .po-card:hover { transform: translateY(-5px); }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">📦 Generador Avanzado de Purchase Orders</h2>
                <p class="text-muted">Descarga estos PDFs de prueba para subirlos a tu sistema WMS.</p>
            </div>
            
            <div class="row g-4">
                <!-- Lote Original -->
                <div class="col-md-4">
                    <div class="card shadow-sm po-card border-primary">
                        <div class="card-body text-center">
                            <h5 class="card-title">PO-2026-002</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Global Packaging</h6>
                            <p class="card-text small">Empaques y Cintas</p>
                            <a href="?po=1" class="btn btn-outline-primary w-100"><i class="bi bi-download"></i> Descargar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm po-card border-success">
                        <div class="card-body text-center">
                            <h5 class="card-title">PO-2026-003</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Químicos Industriales</h6>
                            <p class="card-text small">Acetona y Solventes</p>
                            <a href="?po=2" class="btn btn-outline-success w-100"><i class="bi bi-download"></i> Descargar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm po-card border-warning">
                        <div class="card-body text-center">
                            <h5 class="card-title">PO-2026-004</h5>
                            <h6 class="card-subtitle mb-2 text-muted">AceroMex S.A.</h6>
                            <p class="card-text small">Bobinas y Tornillería</p>
                            <a href="?po=3" class="btn btn-outline-warning text-dark w-100"><i class="bi bi-download"></i> Descargar</a>
                        </div>
                    </div>
                </div>

                <!-- Nuevas Órdenes con el Catálogo Extendido -->
                <div class="col-md-4">
                    <div class="card shadow-sm po-card border-dark">
                        <div class="card-body text-center">
                            <h5 class="card-title">PO-2026-005</h5>
                            <h6 class="card-subtitle mb-2 text-muted">TechSupply MX</h6>
                            <p class="card-text small">Motores y Sensores (Alto Valor)</p>
                            <a href="?po=4" class="btn btn-outline-dark w-100"><i class="bi bi-download"></i> Descargar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm po-card border-info">
                        <div class="card-body text-center">
                            <h5 class="card-title">PO-2026-006</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Ferretera Industrial</h6>
                            <p class="card-text small">Rodamientos y Bandas</p>
                            <a href="?po=5" class="btn btn-outline-info w-100"><i class="bi bi-download"></i> Descargar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm po-card border-secondary">
                        <div class="card-body text-center">
                            <h5 class="card-title">PO-2026-007</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Polímeros del Norte</h6>
                            <p class="card-text small">Pellets de Polietileno (Volumen)</p>
                            <a href="?po=6" class="btn btn-outline-secondary w-100"><i class="bi bi-download"></i> Descargar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm po-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">PO-2026-008</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Cartonera Nacional</h6>
                            <p class="card-text small">Cajas Reforzadas y Cinta Canela</p>
                            <a href="?po=7" class="btn btn-primary w-100"><i class="bi bi-download"></i> Descargar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm po-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">PO-2026-009</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Aceros Monterrey</h6>
                            <p class="card-text small">Láminas de Aluminio y Cobre</p>
                            <a href="?po=8" class="btn btn-dark w-100"><i class="bi bi-download"></i> Descargar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
    exit;
}


class PDF_PO extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, 'ORDEN DE COMPRA (PURCHASE ORDER)', 0, 1, 'C');
        $this->Ln(5);
    }
}

$id_po = $_GET['po'];

$ordenes = [
    '1' => [
        'folio' => 'PO-2026-002', 'proveedor' => 'Global Packaging Inc.', 'planta' => 'Apodaca',
        'productos' => [
            ['cant' => 500, 'sku' => 'EM-CJ-500', 'desc' => 'Caja de Carton Corrugado 50x50', 'precio' => 15.00],
            ['cant' => 200, 'sku' => 'EM-CI-100', 'desc' => 'Cinta Adhesiva Industrial', 'precio' => 25.50]
        ]
    ],
    '2' => [
        'folio' => 'PO-2026-003', 'proveedor' => 'Quimicos Industriales', 'planta' => 'Santa Catarina',
        'productos' => [
            ['cant' => 15, 'sku' => 'QM-AC-990', 'desc' => 'Acetona Industrial (Tambor)', 'precio' => 850.00],
            ['cant' => 30, 'sku' => 'QM-SL-200', 'desc' => 'Solvente Limpiador', 'precio' => 420.00]
        ]
    ],
    '3' => [
        'folio' => 'PO-2026-004', 'proveedor' => 'AceroMex S.A. de C.V.', 'planta' => 'Washington',
        'productos' => [
            ['cant' => 100, 'sku' => 'MP-AC-001', 'desc' => 'Bobina de Acero 3mm', 'precio' => 1200.50],
            ['cant' => 5000, 'sku' => 'RF-TR-005', 'desc' => 'Tornillo de Acero Inox 1/2"', 'precio' => 2.50]
        ]
    ],
    // NUEVAS ÓRDENES CON EL CATÁLOGO NUEVO
    '4' => [
        'folio' => 'PO-2026-005', 'proveedor' => 'TechSupply MX', 'planta' => 'Apodaca',
        'productos' => [
            ['cant' => 2, 'sku' => 'RF-MO-040', 'desc' => 'Motor Trifasico 2HP', 'precio' => 8500.00],
            ['cant' => 10, 'sku' => 'RF-SE-050', 'desc' => 'Sensor Optico de Barrera', 'precio' => 1250.00]
        ]
    ],
    '5' => [
        'folio' => 'PO-2026-006', 'proveedor' => 'Ferretera Industrial', 'planta' => 'Santa Catarina',
        'productos' => [
            ['cant' => 50, 'sku' => 'RF-RD-020', 'desc' => 'Rodamiento de Bolas 6204', 'precio' => 120.00],
            ['cant' => 20, 'sku' => 'RF-BN-030', 'desc' => 'Banda de Transmision A-42', 'precio' => 340.50]
        ]
    ],
    '6' => [
        'folio' => 'PO-2026-007', 'proveedor' => 'Polimeros del Norte', 'planta' => 'García',
        'productos' => [
            ['cant' => 150, 'sku' => 'MP-PL-020', 'desc' => 'Pellet de Polietileno (Saco 25kg)', 'precio' => 650.00]
        ]
    ],
    '7' => [
        'folio' => 'PO-2026-008', 'proveedor' => 'Cartonera Nacional', 'planta' => 'Apodaca',
        'productos' => [
            ['cant' => 1000, 'sku' => 'EM-CJ-035', 'desc' => 'Caja de Carton Reforzada 60x60x40', 'precio' => 28.50],
            ['cant' => 50, 'sku' => 'EM-CI-045', 'desc' => 'Cinta Canela Industrial (Caja 36 rollos)', 'precio' => 450.00]
        ]
    ],
    '8' => [
        'folio' => 'PO-2026-009', 'proveedor' => 'Aceros Monterrey', 'planta' => 'Washington',
        'productos' => [
            ['cant' => 300, 'sku' => 'MP-AL-010', 'desc' => 'Lamina de Aluminio 2mm', 'precio' => 580.00],
            ['cant' => 50, 'sku' => 'MP-CU-030', 'desc' => 'Rollo de Alambre de Cobre cal. 12', 'precio' => 1100.00]
        ]
    ]
];

if (!isset($ordenes[$id_po])) {
    $id_po = '1';
}

$datos = $ordenes[$id_po];

$pdf = new PDF_PO();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'Folio PO:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 6, $datos['folio'], 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'Fecha:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 6, date('d/m/Y'), 0, 1); 

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'Proveedor:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 6, $datos['proveedor'], 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'Planta Destino:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 6, $datos['planta'], 0, 1);
$pdf->Ln(10);

$pdf->SetFillColor(41, 128, 185); 
$pdf->SetTextColor(255); 
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, 'CANT.', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'SKU', 1, 0, 'C', true);
$pdf->Cell(75, 8, utf8_decode('DESCRIPCIÓN'), 1, 0, 'C', true);
$pdf->Cell(30, 8, 'PRECIO U.', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'TOTAL', 1, 1, 'C', true);

$pdf->SetTextColor(0); 
$pdf->SetFont('Arial', '', 10);
$gran_total = 0;

foreach ($datos['productos'] as $prod) {
    $total_fila = $prod['cant'] * $prod['precio'];
    $gran_total += $total_fila;

    $pdf->Cell(20, 8, $prod['cant'], 1, 0, 'C');
    $pdf->Cell(35, 8, $prod['sku'], 1, 0, 'C');
    $pdf->Cell(75, 8, utf8_decode($prod['desc']), 1, 0, 'L');
    $pdf->Cell(30, 8, '$' . number_format($prod['precio'], 2), 1, 0, 'R');
    $pdf->Cell(30, 8, '$' . number_format($total_fila, 2), 1, 1, 'R');
}

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(160, 8, 'TOTAL DE LA ORDEN:', 1, 0, 'R');
$pdf->Cell(30, 8, '$' . number_format($gran_total, 2), 1, 1, 'R');

$pdf->Ln(15);
$pdf->SetFont('Arial', 'I', 9);
$pdf->MultiCell(0, 6, utf8_decode('Nota: Favor de entregar la mercancia con su respectiva factura y remision sellada. El material sujeto a inspección de calidad antes de ingreso al Kardex.'));


$pdf->Output('D', $datos['folio'] . '-Documento.pdf');
?>