<?php
session_start();

require_once '../config/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$database = new Database();
$db = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recover_password'])) {
    $email = trim($_POST['email']);

    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expire = date("Y-m-d H:i:s", time() + 3600); 

        $update = $db->prepare("UPDATE usuarios SET reset_token = :token, reset_token_expire = :expire WHERE email = :email");
        $update->execute(['token' => $token, 'expire' => $expire, 'email' => $email]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            // Usa tus credenciales
            $mail->Username   = ''; 
            $mail->Password   = ''; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('', 'Soporte WMS');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Recuperación de contraseña - WMS Almacén';
            
            $enlace = "http://localhost/control_almacen_wms/views/reset_password.php?token=" . $token;
            
            $mail->Body = "Hola,<br><br>Has solicitado restablecer tu contraseña para el WMS. Haz clic en el siguiente enlace para crear una nueva (caduca en 1 hora):<br><br><a href='$enlace'>Restablecer Contraseña</a><br><br>Si no solicitaste esto, ignora este correo.";

            $mail->send();
            
            header("Location: ../views/recuperar.php?exito=1&planta=Washington");
            exit();
        } catch (Exception $e) {
            header("Location: ../views/recuperar.php?error=1");
            exit();
        }
    } else {
        header("Location: ../views/recuperar.php?exito=1&planta=Washington");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $token = trim($_POST['token']);
    $nueva_password = trim($_POST['nueva_password']);

    $stmt = $db->prepare("SELECT id FROM usuarios WHERE reset_token = :token AND reset_token_expire > NOW() LIMIT 1");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $hash_password = password_hash($nueva_password, PASSWORD_DEFAULT);

        $update = $db->prepare("UPDATE usuarios SET password = :password, reset_token = NULL, reset_token_expire = NULL WHERE id = :id");
        $update->execute([
            'password' => $hash_password,
            'id' => $user['id']
        ]);

        header("Location: ../views/login.php?mensaje=password_actualizada");
        exit();
    } else {
        header("Location: ../views/recuperar.php?error=token_invalido");
        exit();
    }
}
?>