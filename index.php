<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';               
    $mail->SMTPAuth   = true;                          
    $mail->Username   = 'Dark.Web.DT@gmail.com';       
    $mail->Password   = 'nqnm ysos xszg ukjv';          
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                              

    $mail->setFrom('Dark.Web.DT@gmail.com', 'DARK WEB');
    $mail->addAddress('factotask@gmail.com', 'Facto Task');   

    $mail->isHTML(true);                                 
    $mail->Subject = 'testing web';
    $mail->Body    = 'working web';
    $mail->AltBody = 'working web';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
