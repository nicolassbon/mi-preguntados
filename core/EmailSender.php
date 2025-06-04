<?php

require_once 'vendor/phpmailer/src/Exception.php';
require_once 'vendor/phpmailer/src/PHPMailer.php';
require_once 'vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender
{

  public function send($email, $body)
  {
    $mail = new PHPMailer(true);

    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'nicolasconfig@gmail.com';
      $mail->Password = 'wtgp vnwc mybe hhuy';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      $mail->setFrom('nicolasconfig@gmail.com', 'Preguntopolis');
      $mail->addAddress($email);

      $mail->isHTML(true);
      $mail->Subject = 'Validacion de cuenta en Preguntopolis';
      $mail->Body = $body;

      $mail->send();
      return true;

    } catch (Exception $e) {
      error_log('EmailSender Error: ' . $mail->ErrorInfo);
      return false;
    }
  }
}
