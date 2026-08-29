<?php
session_start();

require_once '../config/database.php';
require_once '../models/Usuario.php';

class AuthController {
    
    public function login() {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if(empty($email) || empty($password)) {
                echo json_encode(["status" => "error", "message" => "Completa todos los campos."]);
                return;
            }

            $database = new Database();
            $db = $database->getConnection();
            $usuario = new Usuario($db);
            
            $datos_usuario = $usuario->obtenerPorEmail($email);

            if($datos_usuario) {
                if($datos_usuario['estatus'] == 0) {
                    echo json_encode(["status" => "error", "message" => "Esta cuenta está inactiva. Contacta al administrador."]);
                    return;
                }

                if(password_verify($password, $datos_usuario['password_hash'])) {
                    
                    session_regenerate_id(true);
                    $_SESSION['usuario_id'] = $datos_usuario['id'];
                    $_SESSION['nombre'] = $datos_usuario['nombre_completo'];
                    $_SESSION['id_rol'] = $datos_usuario['id_rol'];
                    $_SESSION['id_planta'] = $datos_usuario['id_planta'];

                    echo json_encode(["status" => "success", "redirect" => "dashboard.php"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Credenciales incorrectas."]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Credenciales incorrectas."]);
            }
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        echo json_encode(["status" => "success", "redirect" => "login.php"]);
    }
}

if(isset($_GET['action'])) {
    $auth = new AuthController();
    if($_GET['action'] == 'login') {
        $auth->login();
    } else
    if ($_GET['action'] == 'logout') {
        session_start(); 
        session_unset(); 
        session_destroy(); 
        header("Location: ../views/login.php");
        exit();
    }
}
?>