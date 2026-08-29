<?php
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: recuperar.php?error=token_invalido");
    exit();
}
$token = $_GET['token'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - WMS Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .recovery-wrapper { width:100%; max-width:440px; }
        .recovery-card { background: white; border-radius:20px; box-shadow: 0 20px 60px rgba(15,23,42,0.08); overflow:hidden; border:none; }
        .recovery-header { background:#0f172a; padding:28px 32px; position:relative; overflow:hidden; }
        /* Efecto verde para la nueva contraseña */
        .recovery-header::after { content:''; position:absolute; width:300px; height:300px; background: radial-gradient(circle, rgba(16, 185, 129, 0.4) 0%, transparent 70%); top:-80px; right:-80px; }
        .form-control { border-radius:12px; padding:12px 16px; background:#f8fafc; border-color:#e2e8f0; }
        .form-control:focus { background:white; border-color:#10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
        .btn-recovery { background:#0f172a; border:none; border-radius:12px; padding:12px; font-weight:600; transition:.2s; }
        .btn-recovery:hover { background:#10b981; transform: translateY(-1px); }
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
                <h5 class="text-white fw-bold mb-1"><i class="bi bi-key-fill me-2"></i>Nueva Contraseña</h5>
                <p class="mb-0 small" style="color:#94a3b8;">Escribe tu nueva clave de acceso seguro.</p>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="../controllers/PasswordController.php" method="POST" id="formReset">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="mb-3">
                    <label class="small fw-semibold text-muted mb-2">NUEVA CONTRASEÑA</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius:12px 0 0 12px;"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" id="nueva_password" name="nueva_password" class="form-control border-start-0 ps-0" required placeholder="Mínimo 8 caracteres" minlength="8">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="small fw-semibold text-muted mb-2">CONFIRMAR CONTRASEÑA</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius:12px 0 0 12px;"><i class="bi bi-lock-fill text-muted"></i></span>
                        <input type="password" id="confirm_password" class="form-control border-start-0 ps-0" required placeholder="Vuelve a escribirla" minlength="8">
                    </div>
                    <div id="errorPassword" class="form-text small text-danger d-none mt-2"><i class="bi bi-exclamation-circle me-1"></i>Las contraseñas no coinciden.</div>
                </div>
                
                <button type="submit" name="reset_password" class="btn btn-recovery btn-dark w-100 text-white" id="btnReset">
                    <span id="btnText"><i class="bi bi-check2-circle me-2"></i>Guardar y Entrar</span>
                </button>
            </form>
        </div>
    </div>
    
    <div class="text-center mt-3 small text-muted" style="font-size:11px;">© <?php echo date('Y'); ?> WMS v1.0 - Sistema de Control de Almacén</div>
</div>

<script>
document.getElementById('formReset')?.addEventListener('submit', function(e){
    let p1 = document.getElementById('nueva_password').value;
    let p2 = document.getElementById('confirm_password').value;
    let errorMsg = document.getElementById('errorPassword');
    
    if(p1 !== p2) {
        e.preventDefault();
        errorMsg.classList.remove('d-none');
    } else {
        errorMsg.classList.add('d-none');
        let btn = document.getElementById('btnReset');
        let txt = document.getElementById('btnText');
        txt.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualizando...';
        btn.disabled = true;
    }
});
</script>
</body>
</html>