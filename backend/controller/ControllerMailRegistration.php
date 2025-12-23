<?php

require_once APP_PATH . 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ControllerMailRegistration extends ControllerBaseMail
{

    public function sendMailToRegister(EntitieUser $user, $verificationToken)
    {
        try {
            $this->mailer->addAddress($user->getMail());
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Vérification de votre adresse e-mail pour le site YOUR_APP_NAME";

            $emailBody = "Bonjour " . htmlspecialchars($user->getFirstName() . " " . $user->getLastName()) . ",<br><br>";
            $emailBody .= "Merci de vous être inscrit sur YOUR_APP_NAME ! Veuillez cliquer sur le lien ci-dessous pour vérifier votre adresse email :<br>";
            $emailBody .= "<a href=\"" . htmlspecialchars(URI_MAIL . "api/verify-email/" . $verificationToken) . "\">Cliquez ici pour confirmer votre email</a><br><br>";
            $emailBody .= "Si vous n'avez pas créé de compte, veuillez ignorer cet email.<br><br>";
            $emailBody .= "Cordialement,<br>L'équipe YOUR_APP_NAME";
            $this->mailer->Body = $emailBody;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            return false;
        } finally {
            if ($this->mailer) {
                $this->mailer->clearAddresses(); // Clear addresses after sending
                $this->mailer->clearAttachments(); // Clear attachments if any
            }
        }
    }

    public function sendMailToForgetedPassword($mail, $token)
    {
        try {
            $this->mailer->addAddress($mail);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Réinitialisation de votre mot de passe sur YOUR_APP_NAME";

            $emailBody = "Bonjour " . ",<br><br>";
            $emailBody .= "Nous avons reçu une demande de réinitialisation de votre mot de passe. Veuillez cliquer sur le lien ci-dessous pour réinitialiser votre mot de passe :<br>";
            $emailBody .= "<a href=\"" . htmlspecialchars(URI . "api/reset-password/" . $token) . "\">" . "Lien de réinitialisation</a><br><br>";
            $emailBody .= "Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.<br><br>";
            $emailBody .= "Cordialement,<br>L'équipe YOUR_APP_NAME";
            $this->mailer->Body = $emailBody;

            $this->mailer->send();
            Logger::logs("Password reset email sent", [
                "Email sent to: " . $mail,
            ], self::MAIL_LOG_FILE);
            return true;
        } catch (Exception $e) {
            Logger::logs(
                "Password reset email fail ",
                [$this->mailer->ErrorInfo, $e->getMessage()],
                self::MAIL_LOG_FILE
            );
            return false;
        } finally {
            if ($this->mailer) {
                $this->mailer->clearAddresses(); // Clear addresses after sending
                $this->mailer->clearAttachments(); // Clear attachments if any
            }
        }
    }
}
