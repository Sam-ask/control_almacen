<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit(); 
}

$usuario_nombre = $_SESSION['nombre'] ?? 'Usuario';
$usuario_rol = $_SESSION['id_rol'] ?? '';
$usuario_planta = $_SESSION['id_planta'] ?? '';
?>