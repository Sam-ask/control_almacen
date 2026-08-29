<?php
require_once '../config/security.php';
/**
 * @var string $usuario_nombre
 * @var string $usuario_planta
 * @var int $usuario_rol
 */
 ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WMS Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --navy: #0f172a; --blue: #1e40af; }
        body { background: #f1f5f9; font-family: 'Inter', sans-serif; }
        .navbar-wms { background: white; border-bottom: 1px solid #e2e8f0; }
        .navbar-wms .navbar-brand { font-weight: 800; color: var(--navy) !important; letter-spacing:-0.5px; }
        .navbar-wms .nav-link { color: #64748b !important; font-weight: 500; }
        .navbar-wms .nav-link.active { color: var(--blue) !important; background: #eff6ff; border-radius: 8px; }
        .kpi-card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(15,23,42,0.05); transition: all .2s; }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(15,23,42,0.08); }
        .kpi-icon { width: 48px; height: 48px; border-radius: 12px; display:flex; align-items:center; justify-content:center; font-size:20px; }
        .table-wms thead th { background: #f8fafc; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; border: none; font-weight: 700; }
        .table-wms tbody td { font-size: 13.5px; vertical-align: middle; border-color: #f1f5f9; }
        .card-pro { border:none; border-radius:16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-wms mb-4 py-3">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="dashboard.php"><i class="bi bi-box-seam-fill me-2 text-primary"></i>WMS ALMACÉN</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-4">
                <li class="nav-item"><a class="nav-link active px-3" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="ordenes.php"><i class="bi bi-receipt me-1"></i> Órdenes</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="inventario.php"><i class="bi bi-boxes me-1"></i> Inventario</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="text-end me-3"><div class="fw-bold" style="font-size:13px;"><?php echo htmlspecialchars($usuario_nombre); ?></div><div class="text-muted" style="font-size:11px;">Planta: <?php echo htmlspecialchars($usuario_planta); ?></div></div>
                <a href="../controllers/AuthController.php?action=logout" class="btn btn-sm btn-light rounded-circle"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    <div class="mb-4"><h3 class="fw-bold mb-1" style="color:var(--navy);">Resumen Operativo</h3><p class="text-muted small">Estado en tiempo real de la planta <?php echo htmlspecialchars($usuario_planta); ?> - <?php echo date('d/m/Y'); ?></p></div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card kpi-card p-3"><div class="d-flex justify-content-between align-items-start"><div><div class="text-muted small fw-semibold">ÓRDENES PENDIENTES</div><h2 class="fw-bold mt-2 mb-0" id="kpiPendientes">-</h2><div class="small text-muted mt-1"><span class="text-primary fw-semibold">●</span> Por procesar</div></div><div class="kpi-icon" style="background:#eff6ff; color:#1e40af;"><i class="bi bi-clock-history"></i></div></div></div></div>
        <div class="col-md-3"><div class="card kpi-card p-3"><div class="d-flex justify-content-between align-items-start"><div><div class="text-muted small fw-semibold">EN ACLARACIÓN</div><h2 class="fw-bold mt-2 mb-0 text-warning" id="kpiAclaracion">-</h2><div class="small text-muted mt-1">Requieren revisión</div></div><div class="kpi-icon" style="background:#fef9c3; color:#a16207;"><i class="bi bi-exclamation-circle"></i></div></div></div></div>
        <div class="col-md-3"><div class="card kpi-card p-3" style="border:1px solid #fecaca;"><div class="d-flex justify-content-between align-items-start"><div><div class="text-muted small fw-semibold">STOCK CRÍTICO</div><h2 class="fw-bold mt-2 mb-0 text-danger" id="kpiStock">-</h2><div class="small text-danger mt-1 fw-semibold"><i class="bi bi-arrow-down-short"></i> Bajo mínimo</div></div><div class="kpi-icon" style="background:#fee2e2; color:#dc2626;"><i class="bi bi-box-seam"></i></div></div></div></div>
        <div class="col-md-3"><div class="card kpi-card p-3" style="background: var(--navy); color:white;"><div class="d-flex justify-content-between align-items-start"><div><div class="small fw-semibold" style="color:#94a3b8;">RECEPCIONES HOY</div><h2 class="fw-bold mt-2 mb-0" id="kpiRecepciones">-</h2><div class="small mt-1" style="color:#94a3b8;">Entradas registradas</div></div><div class="kpi-icon" style="background:rgba(255,255,255,0.1); color:white;"><i class="bi bi-check2-all"></i></div></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-md-8"><div class="card card-pro"><div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center"><h6 class="fw-bold mb-0">Últimos Movimientos</h6><span class="badge bg-light text-muted border fw-normal">Tiempo real</span></div>
        <div class="card-body p-0 table-responsive"><table class="table table-hover table-wms mb-0"><thead><tr><th>Fecha y Hora</th><th>Tipo</th><th>Producto</th><th>Cant.</th><th>Ref. PO</th></tr></thead><tbody id="tablaMovimientos"><tr><td colspan="5" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr></tbody></table></div></div></div>
        
        <div class="col-md-4">
            <div class="card card-pro mb-3" style="background: linear-gradient(135deg, #1e40af 0%, #0f172a 100%); color:white; border-radius:16px;">
                <div class="card-body p-4"><h6 class="fw-bold"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Acciones Rápidas</h6><p class="small mb-4" style="color:#cbd5e1;">Gestiona el flujo de tu almacén</p>
                <div class="d-grid gap-2"><a href="ordenes.php" class="btn btn-white bg-white text-dark fw-semibold rounded-3"><i class="bi bi-upload me-2"></i>Subir Orden de Compra</a><a href="inventario.php" class="btn btn-outline-light rounded-3 border-0" style="background: rgba(255,255,255,0.1);"><i class="bi bi-boxes me-2"></i>Ver Inventario</a><a href="../controllers/ReportController.php" target="_blank" class="btn btn-outline-light rounded-3 border-0" style="background: rgba(255,255,255,0.1);"><i class="bi bi-file-earmark-pdf me-2"></i>Generar Reporte PDF</a><!-- Agrega este botón justo debajo del botón de "Generar Reporte PDF" -->
<a href="../controllers/ExcelController.php?action=inventario" target="_blank" class="btn text-white w-100 mt-2" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.1);">
    <i class="bi bi-file-earmark-excel"></i> Exportar Inventario a Excel
</a></div></div>
            </div>
            <div class="card card-pro p-3"><div class="d-flex align-items-center"><div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3"><i class="bi bi-shield-check"></i></div><div><div class="fw-semibold small">Sistema Operativo</div><div class="small text-muted">WMS v1.0 - Planta Washington online</div></div></div></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/dashboard.js"></script>
</body>
</html>