<?php
class Producto {
    private $conn;
    private $table_name = "productos";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listarTodos($filtros = []) {
        $query = "SELECT p.id, p.sku, p.nombre, p.stock, p.stock_minimo, 
                         m.nombre as tipo_material, pr.empresa as proveedor
                  FROM productos p
                  LEFT JOIN tipos_material m ON p.id_tipo_material = m.id
                  LEFT JOIN proveedores pr ON p.id_proveedor = pr.id
                  WHERE 1=1"; 
        if (!empty($filtros['sku'])) {
            $query .= " AND p.sku LIKE :sku";
        }
        
        if (!empty($filtros['id_proveedor'])) {
            $query .= " AND p.id_proveedor = :id_proveedor";
        }

        $query .= " ORDER BY p.nombre ASC";
        $stmt = $this->conn->prepare($query);

        if (!empty($filtros['sku'])) {
            $sku_param = "%" . $filtros['sku'] . "%"; 
            $stmt->bindParam(':sku', $sku_param);
        }
        if (!empty($filtros['id_proveedor'])) {
            $stmt->bindParam(':id_proveedor', $filtros['id_proveedor']);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function crear($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (sku, nombre, descripcion, stock, stock_minimo, id_tipo_material, id_proveedor) 
                  VALUES (:sku, :nombre, :descripcion, :stock, :stock_minimo, :id_tipo_material, :id_proveedor)";

        $stmt = $this->conn->prepare($query);

       $sku = htmlspecialchars(strip_tags($datos['sku']));
        $nombre = htmlspecialchars(strip_tags($datos['nombre']));

        $stmt->bindParam(":sku", $sku);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $datos['descripcion']);
        $stmt->bindParam(":stock", $datos['stock']);
        $stmt->bindParam(":stock_minimo", $datos['stock_minimo']);
        $stmt->bindParam(":id_tipo_material", $datos['id_tipo_material']);
        $stmt->bindParam(":id_proveedor", $datos['id_proveedor']);

        return $stmt->execute();
    }

    public function eliminarLogico($id) {
        $query = "UPDATE " . $this->table_name . " SET estatus = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>