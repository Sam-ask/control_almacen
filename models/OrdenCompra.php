<?php
class OrdenCompra {
    private $conn;
    private $table_name = "ordenes_compra";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function registrar($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (numero_orden, id_proveedor, id_planta_destino, id_empleado, id_embarcacion, ruta_archivo, estado) 
                  VALUES (:numero_orden, :id_proveedor, :id_planta, :id_empleado, :id_embarcacion, :ruta_archivo, 'Pendiente')";

        $stmt = $this->conn->prepare($query);

        $numero_orden = htmlspecialchars(strip_tags($datos['numero_orden']));

        $stmt->bindParam(":numero_orden", $numero_orden);
        $stmt->bindParam(":id_proveedor", $datos['id_proveedor']);
        $stmt->bindParam(":id_planta", $datos['id_planta_destino']); 
        $stmt->bindParam(":id_empleado", $datos['id_empleado']);     
        $stmt->bindParam(":id_embarcacion", $datos['id_embarcacion']);
        $stmt->bindParam(":ruta_archivo", $datos['ruta_archivo']);

        return $stmt->execute();
    }
    
    public function cambiarEstadoAclaracion($id_orden, $comentarios) {
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = 'En Aclaración', comentarios_aclaracion = :comentarios 
                  WHERE id = :id";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":comentarios", $comentarios);
        $stmt->bindParam(":id", $id_orden);
        
        return $stmt->execute();
    }

    public function obtenerDetallesParaCorreo($id_orden) {
        $query = "SELECT o.numero_orden, o.comentarios_aclaracion, 
                         p.empresa, p.email_contacto, p.contacto_nombre 
                  FROM " . $this->table_name . " o
                  INNER JOIN proveedores p ON o.id_proveedor = p.id
                  WHERE o.id = :id LIMIT 0,1";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_orden);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function buscarConFiltros($filtros) {
        $query = "SELECT 
                    o.id, o.numero_orden, o.estado, o.ruta_archivo, o.fecha_creacion, o.urgencia,
                    p.empresa AS proveedor,
                    pl.nombre AS planta
                  FROM " . $this->table_name . " o
                  INNER JOIN proveedores p ON o.id_proveedor = p.id
                  INNER JOIN plantas pl ON o.id_planta_destino = pl.id
                  WHERE 1=1 "; 

        $params = [];

        if (!empty($filtros['id_proveedor'])) {
            $query .= " AND o.id_proveedor = :id_proveedor";
            $params[':id_proveedor'] = $filtros['id_proveedor'];
        }
        
        if (!empty($filtros['id_planta'])) {
            $query .= " AND o.id_planta_destino = :id_planta";
            $params[':id_planta'] = $filtros['id_planta'];
        }
        
        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $query .= " AND DATE(o.fecha_creacion) BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params[':fecha_fin'] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['estado'])) {
            $query .= " AND o.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $query .= " ORDER BY o.fecha_creacion DESC";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function procesarRecepcion($id_orden, $id_usuario, $detalles_recepcion) {
        try {
            $this->conn->beginTransaction();

            $queryEstado = "UPDATE " . $this->table_name . " SET estado = 'Procesada' WHERE id = :id";
            $stmtEstado = $this->conn->prepare($queryEstado);
            $stmtEstado->bindParam(':id', $id_orden);
            $stmtEstado->execute();

            $queryKardex = "INSERT INTO movimientos_inventario 
                            (id_producto, id_orden_compra, id_usuario, tipo_movimiento, cantidad, observaciones) 
                            VALUES (:id_producto, :id_orden, :id_usuario, 'ENTRADA', :cantidad, 'Recepción de PO')";
            $stmtKardex = $this->conn->prepare($queryKardex);

            $queryStock = "UPDATE productos SET stock = stock + :cantidad WHERE id = :id_producto";
            $stmtStock = $this->conn->prepare($queryStock);

            foreach ($detalles_recepcion as $item) {
                $stmtKardex->bindValue(':id_producto', $item['id_producto']);
                $stmtKardex->bindValue(':id_orden', $id_orden);
                $stmtKardex->bindValue(':id_usuario', $id_usuario);
                $stmtKardex->bindValue(':cantidad', $item['cantidad']);
                $stmtKardex->execute();

                $stmtStock->bindValue(':cantidad', $item['cantidad']);
                $stmtStock->bindValue(':id_producto', $item['id_producto']);
                $stmtStock->execute();
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e; 
        }
    }

    
    public function obtenerPorId($id) {
        $query = "SELECT id, numero_orden, id_proveedor, id_embarcacion 
                  FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($datos) {
        $query = "UPDATE " . $this->table_name . " 
                  SET id_proveedor = :id_proveedor, id_embarcacion = :id_embarcacion
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_proveedor", $datos['id_proveedor']);
        $stmt->bindParam(":id_embarcacion", $datos['id_embarcacion']);
        $stmt->bindParam(":id", $datos['id_orden']);
        return $stmt->execute();
    }
}
?>