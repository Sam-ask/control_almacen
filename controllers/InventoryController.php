<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "No autorizado."]);
    exit();
}

require_once '../config/database.php';
require_once '../models/Producto.php';

class InventoryController {
    private $db;
    private $producto;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->producto = new Producto($this->db);
    }

    public function listar() {
        try {
            $filtros = [
                'sku' => isset($_GET['sku']) ? $_GET['sku'] : '',
                'id_proveedor' => isset($_GET['id_proveedor']) ? $_GET['id_proveedor'] : ''
            ];
            
            $datos = $this->producto->listarTodos($filtros);
            
            echo json_encode(["status" => "success", "data" => $datos]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Error al obtener inventario: " . $e->getMessage()]);
        }
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                if ($this->producto->crear($_POST)) {
                    echo json_encode(["status" => "success", "message" => "Producto registrado exitosamente."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "No se pudo registrar el producto."]);
                }
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Error de base de datos: " . $e->getMessage()]);
            }
        }
    }

    public function eliminar() {
        if (isset($_POST['id'])) {
            if ($this->producto->eliminarLogico($_POST['id'])) {
                echo json_encode(["status" => "success", "message" => "Producto eliminado del catálogo activo."]);
            } else {
                echo json_encode(["status" => "error", "message" => "No se pudo eliminar el producto."]);
            }
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new InventoryController();
    
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'listar':
            $controller->listar();
            break;
        case 'guardar':
            $controller->guardar();
            break;
        case 'eliminar':
            $controller->eliminar();
            break;
        default:
            echo json_encode(["status" => "error", "message" => "Acción no válida."]);
    }
}
?>