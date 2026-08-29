<?php require_once '../config/security.php'; 
/**
 * @var string $usuario_nombre
 * @var string $usuario_planta
 * @var int $usuario_rol
 */?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes - WMS Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --navy: #0f172a; --blue: #1e40af; }
        body { background: #f1f5f9; font-family: 'Inter', sans-serif; }
        .navbar-wms { background: white; border-bottom: 1px solid #e2e8f0; }
        .navbar-wms .navbar-brand { font-weight: 800; color: var(--navy) !important; }
        .navbar-wms .nav-link { color: #64748b !important; font-weight: 500; }
        .navbar-wms .nav-link.active { color: var(--blue) !important; background: #eff6ff; border-radius: 8px; }
        .card-pro { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(15,23,42,0.05); }
        .form-control, .form-select { border-radius: 10px; padding: 10px 14px; border-color: #e2e8f0; }
        .form-control:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(30,64,175,0.1); }
        .table-wms thead th { background: #f8fafc; color: #94a3b8; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: .6px; border: none; }
        .table-wms tbody td { font-size: 13.5px; vertical-align: middle; }
        .badge-estado { padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 11px; }
        .badge-Pendiente { background:#fef9c3; color:#854d0e; }
        .badge-Procesada { background:#dcfce7; color:#166534; }
        .badge-En\ Recepción { background:#dbeafe; color:#1e40af; }
        .badge-En\ Aclaración { background:#fee2e2; color:#991b1b; }
        .upload-zone { border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; transition: .2s; }
        .upload-zone:hover { border-color: var(--blue); background: #eff6ff; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-wms mb-4 py-3">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="dashboard.php"><i class="bi bi-box-seam-fill me-2 text-primary"></i>WMS ALMACÉN</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-4">
                <li class="nav-item"><a class="nav-link px-3" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active px-3" href="ordenes.php"><i class="bi bi-receipt me-1"></i> Órdenes</a></li>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-0" style="color:var(--navy);">Gestión de Órdenes de Compra</h4><small class="text-muted">Registro, recepción y aclaraciones de POs</small></div>
        <div class="badge bg-white border text-dark fw-normal shadow-sm"><i class="bi bi-shield-lock text-success"></i> Flujo auditado</div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-pro">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3"><div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-2"><i class="bi bi-plus-lg"></i></div><h6 class="fw-bold mb-0">Registrar Nueva PO</h6></div>
                    <div id="alertMessage" class="alert d-none small" role="alert"></div>
                    <form id="formSubirPO">
                        <div class="mb-3"><label class="small fw-semibold text-muted">FOLIO DE LA ORDEN *</label><input type="text" name="numero_orden" class="form-control" placeholder="PO-2026-001" required></div>
                        <div class="mb-3"><label class="small fw-semibold text-muted">PROVEEDOR *</label><select name="id_proveedor" id="selectProveedor" class="form-select" required><option value="">Seleccionar proveedor...</option></select></div>
                        <div class="mb-3"><label class="small fw-semibold text-muted">TRANSPORTE / EMBARCACIÓN *</label><select name="id_embarcacion" id="selectEmbarcacion" class="form-select" required><option value="">Seleccionar transporte...</option></select></div>
                        <div class="mb-4"><label class="small fw-semibold text-muted">ARCHIVO FÍSICO (SOPORTE) *</label>
                            <div class="upload-zone p-4 text-center">
                                <i class="bi bi-cloud-arrow-up fs-3 text-muted"></i>
                                <div class="small mt-2">Arrastra o selecciona PDF / XLS</div>
                                <input type="file" name="archivo_po" class="form-control mt-2" accept=".pdf, .xls, .xlsx" required>
                                <div class="form-text small">Máx 5MB - Se guardará en /uploads/purchase_orders/</div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold" id="btnSubmitPO" style="background:var(--navy); border:none;"><i class="bi bi-cloud-arrow-up me-1"></i> Guardar y Subir Archivo</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-pro mb-3">
                <div class="card-body py-3">
                    <form id="formFiltros" class="row g-2 align-items-end">
                        <div class="col-md-3"><label class="small fw-semibold text-muted">PROVEEDOR</label><select name="id_proveedor" id="filtroProveedor" class="form-select form-select-sm"><option value="">Todos</option></select></div>
                        <div class="col-md-2"><label class="small fw-semibold text-muted">ESTADO</label><select name="estado" class="form-select form-select-sm"><option value="">Todos</option><option value="Pendiente">Pendiente</option><option value="En Recepción">En Recepción</option><option value="En Aclaración">En Aclaración</option><option value="Procesada">Procesada</option></select></div>
                        <div class="col-md-3"><label class="small fw-semibold text-muted">DESDE</label><input type="date" name="fecha_inicio" class="form-control form-control-sm"></div>
                        <div class="col-md-3"><label class="small fw-semibold text-muted">HASTA</label><input type="date" name="fecha_fin" class="form-control form-control-sm"></div>
                        <div class="col-md-1"><button type="submit" class="btn btn-dark btn-sm w-100 rounded-3"><i class="bi bi-search"></i></button></div>
                    </form>
                </div>
            </div>

            <div class="card card-pro" style="overflow:hidden;">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between"><h6 class="fw-bold mb-0">Órdenes Recientes</h6><small class="text-muted">Últimas 50 POs</small></div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover table-wms mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>FOLIO PO</th>
                                <th>PROVEEDOR</th>
                                <th>ESTADO</th>
                                <th>SOPORTE</th>
                                <th class="text-center">ACCIÓN</th> 
                            </tr>
                        </thead>                      
                        <tbody id="tablaOrdenes"><tr><td colspan="5" class="text-center py-5 text-muted small">Cargando historial...</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAclaracion" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
<div class="modal-header border-0"><h6 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Solicitar Aclaración</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form id="formAclaracion"><div class="modal-body"><p class="small text-muted">Se pausará la PO y se notificará al proveedor automáticamente.</p><input type="hidden" name="id_orden" id="aclaracion_id_orden"><div class="mb-3"><label class="small fw-semibold">Motivo</label><textarea name="comentarios" class="form-control" rows="4" required placeholder="Ej. Faltan 5 cajas del material X..."></textarea></div></div><div class="modal-footer border-0"><button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning rounded-3 fw-semibold" id="btnEnviarAclaracion">Enviar Correo</button></div></form></div></div></div>

<div class="modal fade" id="modalRecepcion" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
<div class="modal-header border-0" style="background:var(--navy); color:white; border-radius:16px 16px 0 0;"><h6 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i> Procesar Recepción de PO</h6><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
<div class="modal-body p-4">
<form id="formAgregarProducto" class="row g-2 mb-4 p-3 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0;"><input type="hidden" id="recepcion_id_orden" name="id_orden"><div class="col-md-7"><label class="small fw-semibold">Producto</label><select id="selectProductoRecepcion" class="form-select" required><option value="">Seleccionar producto...</option></select></div><div class="col-md-3"><label class="small fw-semibold">Cantidad</label><input type="number" id="inputCantidadRecepcion" class="form-control" step="0.01" min="0.01" required></div><div class="col-md-2 d-flex align-items-end"><button class="btn btn-success w-100 rounded-3" type="submit"><i class="bi bi-plus"></i></button></div></form>
<div class="table-responsive border rounded-3"><table class="table table-sm table-wms mb-0"><thead><tr><th>Producto (SKU)</th><th>Cantidad</th><th>Acción</th></tr></thead><tbody id="tablaProductosRecepcion"><tr id="filaVaciaRecepcion"><td colspan="3" class="text-center text-muted small py-4">Aún no hay mercancía en la lista.</td></tr></tbody></table></div>
</div><div class="modal-footer border-0"><button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success rounded-3 fw-semibold px-4" id="btnGuardarRecepcion" disabled>Confirmar Recepción y Actualizar Stock</button></div></div></div></div>
<div class="modal fade" id="modalEditarPO" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <div class="modal-header border-0" style="background:var(--navy); color:white; border-radius:16px 16px 0 0;">
                <h6 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i> Editar Orden de Compra</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarPO">
                <div class="modal-body p-4">
                    <input type="hidden" id="editar_id_orden" name="id_orden">
                    
                    <div class="mb-4">
                        <label class="small fw-semibold text-muted mb-1">FOLIO DE LA ORDEN</label>
                        <input type="text" id="editar_numero_orden" class="form-control bg-light text-muted" disabled>
                        <div class="form-text small mt-1">El folio y el archivo PDF no se pueden modificar.</div>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-semibold text-muted mb-1">PROVEEDOR *</label>
                        <select name="id_proveedor" id="editar_selectProveedor" class="form-select" required>
                            <option value="">Cargando opciones...</option>
                        </select>
                    </div>
                    
                    <div class="mb-2">
                        <label class="small fw-semibold text-muted mb-1">TRANSPORTE / EMBARCACIÓN *</label>
                        <select name="id_embarcacion" id="editar_selectEmbarcacion" class="form-select" required>
                            <option value="">Cargando opciones...</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-semibold px-4" id="btnGuardarEdicion" style="background:var(--blue); border:none;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/orders.js"></script>
</body>
</html>