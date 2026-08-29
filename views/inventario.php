<?php
require_once '../config/security.php';
/**
 * Declaración de variables importadas de security.php para el IDE (Intelephense)
 * 
 * @var string $usuario_nombre
 * @var string $usuario_planta
 * @var int $usuario_rol
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - WMS Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --navy: #0f172a; --blue: #1e40af; }
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; }
        .navbar-wms { background: white; border-bottom: 1px solid #e2e8f0; }
        .navbar-wms .navbar-brand { font-weight: 700; color: var(--navy) !important; }
        .navbar-wms .nav-link { color: #64748b !important; font-weight: 500; }
        .navbar-wms .nav-link.active { color: var(--blue) !important; background: #eff6ff; border-radius: 8px; }
        .kpi-card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .table-wms thead th { background: #f8fafc; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; border: none; font-weight: 600; }
        .table-wms tbody td { font-size: 14px; vertical-align: middle; border-color: #f1f5f9; }
        .badge-stock-ok { background: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 20px; font-weight: 600; }
        .badge-stock-low { background: #fee2e2; color: #991b1b; padding: 5px 10px; border-radius: 20px; font-weight: 600; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.8; } 100% { opacity: 1; } }
        .card-filter { border: none; border-radius: 12px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-wms mb-4 py-3">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="dashboard.php"><i class="bi bi-box-seam-fill me-2 text-primary"></i>WMS ALMACÉN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-4">
                <li class="nav-item"><a class="nav-link px-3" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="ordenes.php"><i class="bi bi-receipt me-1"></i> Órdenes</a></li>
                <li class="nav-item"><a class="nav-link active px-3" href="inventario.php"><i class="bi bi-boxes me-1"></i> Inventario</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="text-end me-3">
                    <div class="fw-semibold" style="font-size:13px; line-height:1;"><?php echo htmlspecialchars($usuario_nombre); ?></div>
                    <div class="text-muted" style="font-size:11px;">Planta: <?php echo htmlspecialchars($usuario_planta); ?></div>
                </div>
                <a href="../controllers/AuthController.php?action=logout" class="btn btn-sm btn-outline-secondary rounded-pill px-3"><i class="bi bi-box-arrow-right"></i></a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    <!-- HEADER + KPIs -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color: #0f172a;">Catálogo de Inventario</h4>
            <small class="text-muted">Control de stock y trazabilidad por SKU</small>
        </div>
        <div class="d-flex gap-2">
            <a href="../controllers/ExcelController.php?action=inventario" target="_blank" class="btn btn-outline-success bg-white shadow-sm">
                <i class="bi bi-file-earmark-excel"></i> Exportar Excel
            </a>
            
            <a href="../controllers/ReportController.php" target="_blank" class="btn btn-outline-danger bg-white shadow-sm">
                <i class="bi bi-file-earmark-pdf"></i> Reporte PDF
            </a>
            
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto" style="background:var(--blue); border:none;">
                <i class="bi bi-plus-lg"></i> Nuevo Producto
            </button>
        </div>
    </div>

    <div class="card card-filter shadow-sm mb-4">
        <div class="card-body py-3">
            <form id="formFiltrosInventario" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">BUSCAR POR SKU O NOMBRE</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="busquedaGeneral" class="form-control border-start-0 ps-0" placeholder="Ej. QM-AC-990, Acetona...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">PROVEEDOR</label>
                    <select name="id_proveedor" id="filtroProveedor" class="form-select"><option value="">Todos los proveedores</option></select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">ESTATUS STOCK</label>
                    <select id="filtroEstatus" class="form-select"><option value="">Todos</option><option value="critico">Solo Stock Crítico</option><option value="ok">En Stock</option></select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-dark w-100 rounded-3"><i class="bi bi-funnel"></i> Filtrar</button></div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-wms mb-0">
                <thead><tr><th>SKU</th><th>Descripción / Material</th><th>Proveedor</th><th>Stock Actual</th><th style="width:120px;">Acciones</th></tr></thead>
                <tbody id="tablaInventario"><tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary spinner-border-sm"></div><div class="small text-muted mt-2">Cargando inventario...</div></td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevoProducto" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
<div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold">Registrar Mercancía</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form id="formProducto"><div class="modal-body">
<div class="mb-3"><label class="small fw-semibold">SKU *</label><input type="text" name="sku" class="form-control" placeholder="Ej. QM-AC-990" required></div>
<div class="mb-3"><label class="small fw-semibold">Nombre / Descripción *</label><input type="text" name="nombre" class="form-control" placeholder="Acetona Industrial" required></div>
<div class="row"><div class="col-md-6 mb-3"><label class="small fw-semibold">Stock Inicial</label><input type="number" name="stock" class="form-control" step="0.01" value="0" required></div><div class="col-md-6 mb-3"><label class="small fw-semibold">Stock Mínimo</label><input type="number" name="stock_minimo" class="form-control" step="0.01" value="5" required></div></div>
<div class="mb-3"><label class="small fw-semibold">Tipo de Material</label><select name="id_tipo_material" id="selectMaterial" class="form-select" required><option value="">Seleccionar...</option></select></div>
<div class="mb-3"><label class="small fw-semibold">Proveedor</label><select name="id_proveedor" id="selectProveedor" class="form-select" required><option value="">Seleccionar...</option></select></div>
</div><div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100 rounded-3 py-2" style="background: var(--blue);">Guardar Producto</button></div></form></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/inventory.js"></script>
<script>
document.getElementById('busquedaGeneral')?.addEventListener('keyup', function(){
    let v = this.value.toLowerCase();
    document.querySelectorAll('#tablaInventario tr').forEach(tr=>{
        tr.style.display = tr.innerText.toLowerCase().includes(v) ? '' : 'none';
    });
});
</script>
</body>
</html>