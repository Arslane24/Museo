<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {

    private $mail;
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->mail = new PHPMailer(true);

        // Configuration SMTP Gmail
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $config['SMTP_USER'];
        $this->mail->Password = $config['SMTP_PASS'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;

        // Meilleures pratiques
        $this->mail->CharSet = 'UTF-8';
        $this->mail->isHTML(true);
        $this->mail->SMTPAutoTLS = true;
        $this->mail->AuthType = 'LOGIN';

        // Expéditeur
        $this->mail->setFrom($config['SMTP_FROM_EMAIL'], $config['SMTP_FROM_NAME']);
    }

    /**
     * Réinitialise les destinataires à chaque envoi
     */
    private function resetRecipients()
    {
        $this->mail->clearAllRecipients();
        $this->mail->clearAttachments();
    }

    /**
     * ---- ENVOI DU MAIL D’ACTIVATION ----
     */
    public function sendActivationMail($email, $token)
    {
        $this->resetRecipients();

        // IMPORTANT : localhost doit inclure le port lors du dev local
        $link = "http://localhost:8000/activate.php?token=$token";

        $this->mail->addAddress($email);
        $this->mail->Subject = "Activation de votre compte Museo";
        $this->mail->Body = "
            <p>Bienvenue sur <b>Museo</b> !</p>
            <p>Pour activer votre compte, cliquez sur le lien suivant :</p>
            <p><a href='$link'>$link</a></p>
            <p>Merci !</p>
        ";

        return $this->mail->send();
    }

    /**
     * ---- ENVOI DU MAIL DE RÉINITIALISATION ----
     */
    public function sendResetMail($email, $token)
    {
        $this->resetRecipients();

        $link = "http://localhost:8000/reset_password.php?token=$token";

        $this->mail->addAddress($email);
        $this->mail->Subject = "Réinitialisation de votre mot de passe";
        $this->mail->Body = "
            <p>Vous avez demandé une réinitialisation de mot de passe.</p>
            <p>Cliquez ici pour continuer :</p>
            <p><a href='$link'>$link</a></p>
            <p><i>Ce lien est valable pendant 30 minutes.</i></p>
        ";

        return $this->mail->send();
    }

}
