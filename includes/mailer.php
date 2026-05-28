<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

function send_email($to_email, $to_name, $subject, $body) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'youremail@gmail.com'; // TODO: Replace with actual credentials
    $mail->Password   = 'your_app_password';  // TODO: Replace with actual credentials
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    $mail->setFrom('youremail@gmail.com', 'Election System'); // TODO: Replace with actual credentials
    $mail->addAddress($to_email, $to_name);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
}
?>
