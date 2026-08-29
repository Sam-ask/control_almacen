<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - WMS Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .recovery-wrapper { width:100%; max-width:440px; }
        .recovery-card { background: white; border-radius:20px; box-shadow: 0 20px 60px rgba(15,23,42,0.08); overflow:hidden; border:none; }
        .recovery-header { background:#0f172a; padding:28px 32px; position:relative; overflow:hidden; }
        .recovery-header::after { content:''; position:absolute; width:300px; height:300px; background: radial-gradient(circle, rgba(30,64,175,0.5) 0%, transparent 70%); top:-80px; right:-80px; }
        .form-control { border-radius:12px; padding:12px 16px; background:#f8fafc; border-color:#e2e8f0; }
        .form-control:focus { background:white; border-color:#1e40af; box-shadow: 0 0 0 3px rgba(30,64,175,0.1); }
        .btn-recovery { background:#0f172a; border:none; border-radius:12px; padding:12px; font-weight:600; transition:.2s; }
        .btn-recovery:hover { background:#1e40af; transform: translateY(-1px); }
        .alert-pro { border-radius:12px; border:none; font-size:13px; font-weight:500; }
    </style>
</head>
<body>

<div class="recovery-wrapper">
    <div class="text-center mb-4 d-flex align-items-center justify-content-center gap-2">
        <div class="bg-dark text-white rounded-3 p-2"><i class="bi bi-box-seam-fill"></i></div>
        <span class="fw-bold">WMS ALMACÉN</span>
    </div>

    <div class="card recovery-card">
        <div class="recovery-header">
            <div style="position:relative; z-index:1;">
                <h5 class="text-white fw-bold mb-1"><i class="bi bi-shield-lock me-2"></i>Recuperar acceso</h5>
                <p class="mb-0 small" style="color:#94a3b8;">Te enviaremos instrucciones para restablecer tu contraseña de forma segura.</p>
            </div>
        </div>

        <div class="card-body p-4">
            <?php if(isset($_GET['exito'])): ?>
                <div class="alert alert-success alert-pro bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill me-2"></i>Si el correo existe en Planta <?php echo htmlspecialchars($_GET['planta'] ?? 'Washington'); ?>, hemos enviado un enlace de recuperación. Revisa tu bandeja.
                </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-pro bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php 
                        if ($_GET['error'] == 'token_invalido') {
                            echo "El enlace de recuperación ha caducado o es inválido. Solicita uno nuevo.";
                        } else {
                            echo "Hubo un problema al procesar la solicitud. Verifica el correo e intenta de nuevo.";
                        }
                    ?>
                </div>
            <?php endif; ?>

            <form action="../controllers/PasswordController.php" method="POST" id="formRecovery">
                <div class="mb-4">
                    <label class="small fw-semibold text-muted mb-2">CORREO ELECTRÓNICO REGISTRADO</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius:12px 0 0 12px;"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" id="email" name="email" class="form-control border-start-0 ps-0" required placeholder="tu-correo@empresa.com" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
                    </div>
                    <div class="form-text small text-muted mt-2"><i class="bi bi-info-circle me-1"></i>Solo correos registrados en el WMS</div>
                </div>
                
                <button type="submit" name="recover_password" class="btn btn-recovery btn-dark w-100 text-white" id="btnRecovery">
                    <span id="btnText"><i class="bi bi-send me-2"></i>Enviar Instrucciones</span>
                </button>
            </form>
            
            <div class="text-center mt-4">
                <a href="login.php" class="small text-muted text-decoration-none fw-medium"><i class="bi bi-arrow-left me-1"></i> Volver al inicio de sesión</a>
            </div>

            <div class="mt-4 pt-3 border-top small text-muted text-center" style="font-size:11px;">
                <i class="bi bi-lock-fill me-1"></i> Enlace válido por 60 minutos • Soporte: IT Planta Washington
            </div>
        </div>
    </div>

    <div class="text-center mt-3 small text-muted" style="font-size:11px;">© <?php echo date('Y'); ?> WMS v1.0 - Sistema de Control de Almacén</div>
</div>

<script>
document.getElementById('formRecovery')?.addEventListener('submit', function(){
    let btn = document.getElementById('btnRecovery');
    let txt = document.getElementById('btnText');
    txt.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
    btn.disabled = true;
});
</script>
</body>
</html>