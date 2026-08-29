<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "No autorizado."]);
    exit();
}

require_once '../config/database.php';
require_once '../models/OrdenCompra.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../libs/phpmailer/src/Exception.php';
require '../libs/phpmailer/src/PHPMailer.php';
require '../libs/phpmailer/src/SMTP.php';

class MailController {
    
    public function enviarAclaracion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_orden'])) {
            
            $id_orden = $_POST['id_orden'];
            $comentarios = htmlspecialchars(strip_tags($_POST['comentarios']));

            $database = new Database();
            $db = $database->getConnection();
            $orden = new OrdenCompra($db);

            if ($orden->cambiarEstadoAclaracion($id_orden, $comentarios)) {
                
                $datos_correo = $orden->obtenerDetallesParaCorreo($id_orden);
                
                if ($datos_correo) {
                    $respuesta_email = $this->configurarYEnviarEmail($datos_correo);
                    
                    if ($respuesta_email === true) {
                        echo json_encode(["status" => "success", "message" => "Estado actualizado y correo enviado al proveedor."]);
                    } else {
                        echo json_encode(["status" => "warning", "message" => "Estado actualizado, pero hubo un error al enviar el correo: " . $respuesta_email]);
                    }
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Error al actualizar la base de datos."]);
            }
        }
    }

    private function configurarYEnviarEmail($datos) {
        $mail = new PHPMailer(true); 

        try {
            
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = ''; 
            $mail->Password   = ''; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8'; 

            $mail->setFrom('alertas@tuempresa.com', 'WMS Almacén - ' . $_SESSION['nombre']);
            $mail->addAddress($datos['email_contacto'], $datos['contacto_nombre']); 
            
            
            $mail->isHTML(true);
            $mail->Subject = 'URGENTE: Aclaración requerida para Orden ' . $datos['numero_orden'];
            
            $cuerpoHTML = "
            <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #ddd; border-top: 4px solid #dc3545;'>
                <div style='padding: 20px;'>
                    <h2 style='color: #dc3545;'>Solicitud de Aclaración de Mercancía</h2>
                    <p>Estimado/a <strong>{$datos['contacto_nombre']}</strong> ({$datos['empresa']}),</p>
                    <p>Nos comunicamos del departamento de almacén debido a una discrepancia identificada durante la recepción de la siguiente orden de compra:</p>
                    
                    <div style='background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-left: 4px solid #6c757d;'>
                        <p style='margin: 0;'><strong>Folio PO:</strong> {$datos['numero_orden']}</p>
                        <p style='margin: 10px 0 0 0;'><strong>Motivo / Comentarios del Operador:</strong></p>
                        <p style='margin: 5px 0 0 0; color: #555;'><em>\"{$datos['comentarios_aclaracion']}\"</em></p>
                    </div>
                    
                    <p>El proceso de recepción ha sido pausado. Le solicitamos amablemente responder a este correo o comunicarse con nosotros a la brevedad para resolver esta incidencia.</p>
                    <br>
                    <p>Atentamente,<br><strong>Equipo de Almacén</strong></p>
                </div>
            </div>";

            $mail->Body = $cuerpoHTML;
            $mail->AltBody = "Solicitud de Aclaración para la PO: {$datos['numero_orden']}. Motivo: {$datos['comentarios_aclaracion']}";

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            return $mail->ErrorInfo;
        }
    }
}

header('Content-Type: application/json');
if (isset($_GET['action']) && $_GET['action'] == 'aclarar') {
    $controller = new MailController();
    $controller->enviarAclaracion();
}
?>