<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "No autorizado."]);
    exit();
}

require_once '../config/database.php';
require_once '../models/OrdenCompra.php';

class OrderController {
    private $db;
    private $orden;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->orden = new OrdenCompra($this->db);
    }

    public function subirOrden() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo_po'])) {
            
            $archivo = $_FILES['archivo_po'];
            
            if ($archivo['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(["status" => "error", "message" => "Error al subir el archivo al servidor temporal."]);
                return;
            }

            $max_size = 5 * 1024 * 1024;
            if ($archivo['size'] > $max_size) {
                echo json_encode(["status" => "error", "message" => "El archivo excede los 5MB permitidos."]);
                return;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_real = finfo_file($finfo, $archivo['tmp_name']);
            finfo_close($finfo);

            $mimes_permitidos = [
                'application/pdf', 
                'application/vnd.ms-excel', 
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
            ];

            if (!in_array($mime_real, $mimes_permitidos)) {
                echo json_encode(["status" => "error", "message" => "Formato no válido. Solo se permiten PDF o Excel."]);
                return;
            }

            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            
            $nombre_seguro = 'po_' . uniqid() . '.' . $extension;
            
            $directorio_destino = '../uploads/purchase_orders/';
            
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0755, true);
            }

            $ruta_final = $directorio_destino . $nombre_seguro;

            if (move_uploaded_file($archivo['tmp_name'], $ruta_final)) {
                
                $datos_bd = [
                    'numero_orden' => $_POST['numero_orden'],
                    'id_proveedor' => $_POST['id_proveedor'],
                    'id_embarcacion' => $_POST['id_embarcacion'],
                    'id_planta_destino' => $_SESSION['id_planta'], 
                    'id_empleado' => $_SESSION['usuario_id'],
                    'ruta_archivo' => $ruta_final
                ];

                try {
                    if ($this->orden->registrar($datos_bd)) {
                        echo json_encode(["status" => "success", "message" => "Orden registrada y archivo guardado con éxito."]);
                    } else {
                        unlink($ruta_final);
                        echo json_encode(["status" => "error", "message" => "Error al guardar en la base de datos."]);
                    }
                } catch (Exception $e) {
                    unlink($ruta_final);
                    echo json_encode(["status" => "error", "message" => "El folio de la orden ya existe."]);
                }

            } else {
                echo json_encode(["status" => "error", "message" => "No se pudo mover el archivo al directorio final."]);
            }
        }
    }
    
    public function listar() {
        try {
            $filtros = [
                'id_proveedor' => $_GET['id_proveedor'] ?? '',
                'id_planta'    => $_GET['id_planta'] ?? '',
                'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
                'fecha_fin'    => $_GET['fecha_fin'] ?? '',
                'estado'       => $_GET['estado'] ?? ''
            ];

            $datos = $this->orden->buscarConFiltros($filtros);
            
            echo json_encode(["status" => "success", "data" => $datos]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Error al obtener órdenes: " . $e->getMessage()]);
        }
    }

    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_orden'])) {
            
            $id_orden = $_POST['id_orden'];
            $productos_json = $_POST['productos'] ?? '[]';
            $detalles_recepcion = json_decode($productos_json, true);

            if (empty($detalles_recepcion)) {
                echo json_encode(["status" => "error", "message" => "Debe especificar al menos un producto para recibir."]);
                return;
            }

            try {
                if ($this->orden->procesarRecepcion($id_orden, $_SESSION['usuario_id'], $detalles_recepcion)) {
                    echo json_encode(["status" => "success", "message" => "Orden procesada. Inventario actualizado correctamente."]);
                }
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Error crítico en la transacción: " . $e->getMessage()]);
            }
        }
    }

    public function toggleUrgencia() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_orden'])) {
            $id_orden = $_POST['id_orden'];
            $estado_actual = $_POST['urgencia_actual'] ?? 0;
            
+            $nuevo_estado = ($estado_actual == 1) ? 0 : 1; 

            try {
                $query = "UPDATE ordenes_compra SET urgencia = :urgencia WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->execute([':urgencia' => $nuevo_estado, ':id' => $id_orden]);
                
                echo json_encode(["status" => "success"]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        }
    }
    public function obtener() {
        if (isset($_GET['id'])) {
            $datos = $this->orden->obtenerPorId($_GET['id']);
            if ($datos) {
                echo json_encode(["status" => "success", "data" => $datos]);
            } else {
                echo json_encode(["status" => "error", "message" => "Orden no encontrada."]);
            }
        }
    }

    public function editar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_orden'])) {
            $datos = [
                'id_orden' => $_POST['id_orden'],
                'id_proveedor' => $_POST['id_proveedor'],
                'id_embarcacion' => $_POST['id_embarcacion']
            ];
            
            if ($this->orden->actualizar($datos)) {
                echo json_encode(["status" => "success", "message" => "Orden actualizada correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al actualizar la orden."]);
            }
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new OrderController();
    
    if ($_GET['action'] == 'subir') {
        $controller->subirOrden();
    } elseif ($_GET['action'] == 'listar') {
        $controller->listar();
    } elseif ($_GET['action'] == 'procesar') { 
        $controller->procesar();
    } elseif ($_GET['action'] == 'toggle_urgencia') { 
        $controller->toggleUrgencia();
    } elseif ($_GET['action'] == 'obtener') { 
        $controller->obtener();
    } elseif ($_GET['action'] == 'editar') { 
        $controller->editar();
    }
}
?>