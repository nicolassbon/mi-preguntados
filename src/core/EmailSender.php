<?php

namespace App\core;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailSender
{

    private string $host;
    private string $username;
    private string $password;
    private string $port;

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

        } catch (Exception) {
            error_log('EmailSender Error: ' . $mail->ErrorInfo);
            return false;
        }
    }
}

