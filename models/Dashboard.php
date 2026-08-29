<?php
class Dashboard {
    /** @var PDO */
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerUltimosMovimientos() {
        $query = "SELECT 
                    m.tipo_movimiento, 
                    m.cantidad, 
                    m.fecha_movimiento, 
                    p.sku,
                    p.nombre as producto, 
                    o.numero_orden
                  FROM movimientos_inventario m
                  INNER JOIN productos p ON m.id_producto = p.id
                  LEFT JOIN ordenes_compra o ON m.id_orden_compra = o.id
                  ORDER BY m.fecha_movimiento DESC
                  LIMIT 6";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerKPIs() {
        $kpis = [];
        
        $stmt = $this->conn->query("SELECT COUNT(*) FROM ordenes_compra WHERE estado IN ('Pendiente', 'En Recepción')");
        $kpis['pendientes'] = $stmt->fetchColumn();

        
        $stmt = $this->conn->query("SELECT COUNT(*) FROM ordenes_compra WHERE estado = 'En Aclaración'");
        $kpis['aclaracion'] = $stmt->fetchColumn();

        $stmt = $this->conn->query("SELECT COUNT(*) FROM productos WHERE stock <= stock_minimo AND estatus = 1");
        $kpis['stock_critico'] = $stmt->fetchColumn();

        $stmt = $this->conn->query("SELECT COUNT(*) FROM movimientos_inventario WHERE tipo_movimiento = 'ENTRADA' AND DATE(fecha_movimiento) = CURDATE()");
        $kpis['recepciones_hoy'] = $stmt->fetchColumn();

        return $kpis;
    }
}
?>