<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "No autorizado."]);
    exit();
}

require_once '../config/database.php';
require_once '../models/Catalogo.php';

header('Content-Type: application/json');

if (isset($_GET['action'])) {
    $database = new Database();
    $db = $database->getConnection();
    $catalogo = new Catalogo($db);

    if ($_GET['action'] == 'getInventarioSelects') {
        try {
            $respuesta = [
                "status" => "success",
                "data" => [
                    "materiales" => $catalogo->obtenerTiposMaterial(),
                    "proveedores" => $catalogo->obtenerProveedores()
                ]
            ];
            echo json_encode($respuesta);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Error de BD: " . $e->getMessage()]);
        }
    } 
    elseif ($_GET['action'] == 'getOrdenesSelects') {
        try {
            $respuesta = [
                "status" => "success",
                "data" => [
                    "proveedores" => $catalogo->obtenerProveedores(),
                    "embarcaciones" => $catalogo->obtenerEmbarcaciones()
                ]
            ];
            echo json_encode($respuesta);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Error de BD: " . $e->getMessage()]);
        }
    }
}
?>