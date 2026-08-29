<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - WMS Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; margin:0; }
        .login-wrapper { min-height: 100vh; display:flex; }
        .login-left { background: #0f172a; color: white; flex:1; padding:60px; display:flex; flex-direction:column; justify-content:space-between; position:relative; overflow:hidden; }
        .login-left::before { content:''; position:absolute; width:600px; height:600px; background: radial-gradient(circle, rgba(30,64,175,0.4) 0%, transparent 70%); top:-100px; left:-100px; }
        .login-right { flex:1; display:flex; align-items:center; justify-content:center; padding:40px; background:white; }
        .login-card { width:100%; max-width:380px; }
        .form-control { border-radius:12px; padding:12px 16px; border-color:#e2e8f0; background:#f8fafc; }
        .form-control:focus { background:white; border-color:#1e40af; box-shadow: 0 0 0 3px rgba(30,64,175,0.1); }
        .btn-login { background:#0f172a; border:none; border-radius:12px; padding:12px; font-weight:600; transition:.2s; }
        .btn-login:hover { background:#1e40af; transform: translateY(-1px); }
        .feature { display:flex; gap:12px; margin-bottom:20px; }
        .feature-icon { width:40px; height:40px; background: rgba(255,255,255,0.08); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-left d-none d-lg-flex">
        <div style="position:relative; z-index:1;">
            <div class="d-flex align-items-center mb-5"><i class="bi bi-box-seam-fill fs-4 me-2"></i><span class="fw-bold fs-5">WMS ALMACÉN</span></div>
            <h1 class="fw-bold display-6" style="line-height:1.1;">Control total de tu almacén en tiempo real.</h1>
            <p class="mt-3" style="color:#94a3b8;">Sistema de gestión de órdenes, inventario y trazabilidad por planta.</p>
        </div>
        
        <div style="position:relative; z-index:1;">
            <div class="feature"><div class="feature-icon"><i class="bi bi-receipt"></i></div><div><div class="fw-semibold small">Gestión de POs</div><div class="small" style="color:#94a3b8;">Carga de soporte físico y PDF oficial</div></div></div>
            <div class="feature"><div class="feature-icon"><i class="bi bi-boxes"></i></div><div><div class="fw-semibold small">Inventario con Stock Crítico</div><div class="small" style="color:#94a3b8;">Alertas automáticas y reportes PDF</div></div></div>
            <div class="feature"><div class="feature-icon"><i class="bi bi-shield-check"></i></div><div><div class="fw-semibold small">Auditable y Seguro</div><div class="small" style="color:#94a3b8;">Roles, planta y bitácora de movimientos</div></div></div>
            <div class="mt-5 small" style="color:#475569;">© 2026 WMS v1.0 - Planta Washington</div>
        </div>
    </div>

    <div class="login-right">
        <div class="login-card">
            <div class="d-lg-none mb-4 text-center"><i class="bi bi-box-seam-fill fs-2"></i><div class="fw-bold">WMS ALMACÉN</div></div>
            <h3 class="fw-bold mb-1">Bienvenido de vuelta</h3>
            <p class="text-muted small mb-4">Ingresa tus credenciales para acceder al sistema</p>
            
            <div id="alertMessage" class="alert d-none small rounded-3" role="alert"></div>

            <?php if(isset($_GET['mensaje']) && $_GET['mensaje'] == 'password_actualizada'): ?>
                <div class="alert alert-success small rounded-3 bg-success bg-opacity-10 text-success border-0" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>¡Contraseña actualizada con éxito!
                </div>
            <?php endif; ?>

            <form id="loginForm">
                <div class="mb-3">
                    <label class="small fw-semibold text-muted mb-1">CORREO ELECTRÓNICO</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius:12px 0 0 12px;"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" required placeholder="usuario@empresa.com">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="small fw-semibold text-muted mb-1">CONTRASEÑA</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius:12px 0 0 12px;"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" required placeholder="••••••••">
                    </div>
                    <div class="text-end mt-2"><a href="recuperar.php" class="small text-muted text-decoration-none fw-medium">¿Olvidaste tu contraseña?</a></div>
                </div>
                <button type="submit" class="btn btn-login btn-dark w-100 text-white" id="btnSubmit">Iniciar Sesión <i class="bi bi-arrow-right ms-1"></i></button>
            </form>

            <div class="mt-4 p-3 rounded-3 small" style="background:#f8fafc; border:1px solid #e2e8f0;">
                <div class="fw-semibold mb-1"><i class="bi bi-info-circle me-1"></i> Demo:</div>
                <div class="text-muted">admin@empresa.com / password<br>Planta: Washington</div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/auth.js"></script>
<script>
document.getElementById('loginForm')?.addEventListener('submit', function(){
    let btn = document.getElementById('btnSubmit');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Ingresando...';
    btn.disabled = true;
});
</script>
</body>
</html>