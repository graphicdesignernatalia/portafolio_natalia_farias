<?php
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir dependencias de PHPMailer y dotenv
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Dirección de correo de destino
$receiving_email_address = 'natalia.graphicdesigner.cl@gmail.com';

// Comprueba si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recoge los datos del formulario
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"]));
    $message = trim($_POST["message"]);

    // Valida los campos
    if (empty($name) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Envía un código de error
        http_response_code(400);
        echo "<div class='error-message'>Por favor, completa correctamente todos los campos.</div>";
        exit;
    }

    // Configurar PHPMailer
    $mail = new PHPMailer(true); // Habilitar excepciones
    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];

        // Configurar el correo electrónico
        $mail->setFrom($email, $name);
        $mail->addAddress($receiving_email_address);
        $mail->Subject = $subject;
        $mail->Body = "Nombre: $name\nEmail: $email\n\nMensaje:\n$message";
        $mail->AltBody = "Nombre: $name\nEmail: $email\n\nMensaje:\n$message"; // Para clientes de correo que no soportan HTML

     // Enviar correo
$mail->send();
http_response_code(200);
echo 'OK';
    } catch (Exception $e) {
      http_response_code(500);
      echo "Oops, algo salió mal y no pudimos enviar tu mensaje. Error: {$mail->ErrorInfo}"; // Mensaje sin HTML
          }
} else {
    // Si no se accede mediante POST, deniega el acceso
    http_response_code(403);
    echo "<div class='error-message'>Hubo un problema con el envío, por favor intenta de nuevo.</div>";
}
?>



