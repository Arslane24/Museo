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
        $link = "http://museo.alwaysdata.net/activate.php?token=$token";

        $this->mail->addAddress($email);
        $this->mail->Subject = "Activation de votre compte MuseoLink";
        $this->mail->Body = "
            <p>Bienvenue sur <b>MuseoLink</b> !</p>
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

        $link = "http://museo.alwaysdata.net/reset_password.php?token=$token";

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

    /**
     * ---- ENVOI DU MAIL DE CONFIRMATION DE RÉSERVATION ----
     */
    public function sendReservationConfirmationMail($email, $reservationData)
    {
        $this->resetRecipients();

        $museumName = htmlspecialchars($reservationData['museum_name']);
        $visitDate = date('d/m/Y', strtotime($reservationData['visit_date']));
        $visitTime = $reservationData['visit_time'];
        $numberOfPeople = $reservationData['number_of_people'];
        $reservationCode = htmlspecialchars($reservationData['reservation_code']);
        $museumCity = htmlspecialchars($reservationData['museum_city'] ?? '');
        $museumCountry = htmlspecialchars($reservationData['museum_country'] ?? '');

        $this->mail->addAddress($email);
        $this->mail->Subject = "Confirmation de réservation - $museumName";
        $this->mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                    .reservation-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                    .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
                    .detail-label { font-weight: bold; color: #666; }
                    .detail-value { color: #333; }
                    .reservation-code { background: #d4af37; color: white; padding: 15px; text-align: center; font-size: 18px; font-weight: bold; border-radius: 8px; margin: 20px 0; }
                    .footer { text-align: center; color: #666; margin-top: 20px; font-size: 12px; }
                    .button { display: inline-block; background: #d4af37; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>MuseoLink</h1>
                        <p>Confirmation de réservation</p>
                    </div>
                    <div class='content'>
                        <h2>Bonjour,</h2>
                        <p>Votre réservation a été confirmée avec succès !</p>
                        
                        <div class='reservation-code'>
                            Code de réservation : $reservationCode
                        </div>
                        
                        <div class='reservation-details'>
                            <h3>Détails de votre visite</h3>
                            <div class='detail-row'>
                                <span class='detail-label'>Musée :</span>
                                <span class='detail-value'>$museumName</span>
                            </div>
                            <div class='detail-row'>
                                <span class='detail-label'>Lieu :</span>
                                <span class='detail-value'>$museumCity, $museumCountry</span>
                            </div>
                            <div class='detail-row'>
                                <span class='detail-label'>Date :</span>
                                <span class='detail-value'>$visitDate</span>
                            </div>
                            <div class='detail-row'>
                                <span class='detail-label'>Heure :</span>
                                <span class='detail-value'>$visitTime</span>
                            </div>
                            <div class='detail-row'>
                                <span class='detail-label'>Nombre de personnes :</span>
                                <span class='detail-value'>$numberOfPeople</span>
                            </div>
                        </div>
                        
                        <p style='text-align: center;'>
                            <a href='http://museo.alwaysdata.net/private_reservations.php' class='button'>Voir mes réservations</a>
                        </p>
                        
                        <p><strong>Informations importantes :</strong></p>
                        <ul>
                            <li>Présentez votre code de réservation à l'entrée du musée</li>
                            <li>Arrivez 15 minutes avant l'heure prévue</li>
                            <li>Vous pouvez annuler jusqu'à 24h avant votre visite</li>
                        </ul>
                        
                        <p>Nous vous souhaitons une excellente visite !</p>
                        <p>L'équipe MuseoLink</p>
                    </div>
                    <div class='footer'>
                        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                        <p>&copy; 2025 MuseoLink - Votre passerelle vers l'art et la culture</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->mail->send();
    }

}
