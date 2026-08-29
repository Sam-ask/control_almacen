<?php
session_start();
require_once '../config/database.php';

if (isset($_GET['action']) && $_GET['action'] == 'inventario') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Inventario_WMS_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, ['SKU', 'Descripción', 'Categoría de Material', 'Proveedor', 'Stock Actual', 'Stock Mínimo']);
    
    $db = (new Database())->getConnection();
    
    $query = "SELECT p.sku, p.nombre, tm.nombre as material, pr.empresa, p.stock, p.stock_minimo 
              FROM productos p 
              LEFT JOIN tipos_material tm ON p.id_tipo_material = tm.id 
              LEFT JOIN proveedores pr ON p.id_proveedor = pr.id
              ORDER BY p.nombre ASC";
              
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}
?>