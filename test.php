<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function testEmail() {
    $mail = new PHPMailer(true);

    try {
        // SMTP beállítások
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->CharSet = 'UTF-8';

        // Email beállítások
        $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress('laszlobogdan0619@gmail.com');

        // email tartalom
        $mail->isHTML(true);
        $mail->Subject = 'Teszt Email - KKZRT';
        
        $mail->Body = "
            <html>
            <body>
                <h2>Email Teszt</h2>
                <p>Ha ezt az emailt megkaptad, akkor a beállítások sikeresek!</p>
                <p>Időbélyeg: " . date('Y-m-d H:i:s') . "</p>
            </body>
            </html>
        ";

        $mail->send();
        echo "Teszt email sikeresen elküldve!";
    } catch (Exception $e) {
        echo "Hiba történt az email küldése során: " . $mail->ErrorInfo;
    }
}

// teszt futtatása
testEmail();