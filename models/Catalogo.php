<?php
class Catalogo {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTiposMaterial() {
        $query = "SELECT id, nombre FROM tipos_material WHERE estatus = 1 ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProveedores() {
        $query = "SELECT id, empresa FROM proveedores WHERE estatus = 1 ORDER BY empresa ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerEmbarcaciones() {
        $query = "SELECT id, nombre, tipo FROM embarcaciones ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>