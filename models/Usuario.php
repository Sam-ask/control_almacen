<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerPorEmail($email) {
        $query = "SELECT id, nombre_completo, email, password_hash, id_rol, id_planta, estatus 
                  FROM " . $this->table_name . " 
                  WHERE email = :email LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        
        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(':email', $email);
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>