<?php
use PHPMailer\PHPMailer\PHPMailer;

class MailService {

    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);

        // Configure ton SMTP ici
        $this->mail->isSMTP();
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->SMTPAuth = true;
        $this->mail->Username = "museoconfirmation@gmail.com";
        $this->mail->Password = "motdepasse_app";
        $this->mail->SMTPSecure = "tls";
        $this->mail->Port = 587;
        $this->mail->isHTML(true);
    }

    public function sendActivationMail($email, $token) {
        $link = "http://localhost/activate.php?token=$token";

        $this->mail->addAddress($email);
        $this->mail->Subject = "Activation de votre compte Museo";
        $this->mail->Body = "Cliquez ici pour activer votre compte : <a href='$link'>$link</a>";

        return $this->mail->send();
    }
}
