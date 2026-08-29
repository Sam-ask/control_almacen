<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit();
}

require_once '../config/database.php';
require_once '../models/Dashboard.php';

header('Content-Type: application/json');

if (isset($_GET['action']) && $_GET['action'] == 'resumen') {
    $database = new Database();
    $db = $database->getConnection();
    $dashboard = new Dashboard($db);

    try {
        $datos = [
            'kpis' => $dashboard->obtenerKPIs(),
            'movimientos' => $dashboard->obtenerUltimosMovimientos()
        ];
        echo json_encode(["status" => "success", "data" => $datos]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>