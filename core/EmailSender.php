<?php

require_once 'vendor/phpmailer/src/Exception.php';
require_once 'vendor/phpmailer/src/PHPMailer.php';
require_once 'vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender
{

  private $host;
  private $username;
  private $password;
  private $port;

  public function __construct($host, $username, $password, $port)
  {
    $this->host = $host;
    $this->username = $username;
    $this->password = $password;
    $this->port = $port;
  }

  public function send($email, $body): bool
  {
    $mail = new PHPMailer(true);

    try {
      $mail->isSMTP();
      $mail->Host = $this->host;
      $mail->SMTPAuth = true;
      $mail->Username = $this->username;
      $mail->Password = $this->password;
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = $this->port;

      $mail->setFrom($this->username, 'Preguntopolis');
      $mail->addAddress($email);

      $mail->isHTML();
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

