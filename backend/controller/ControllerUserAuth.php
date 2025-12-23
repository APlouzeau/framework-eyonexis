<?php


class ControllerUserAuth extends ControllerBaseUser
{
    protected $controllerMail;
    protected $modelUserAuth;
    public function __construct()
    {
        $this->controllerMail = new ControllerBaseMail();
        $this->modelUserAuth = new ModelUserAuth();
    }
    public function login()
    {

        $data = $this->ensureDataReady();
        $mail = $data[MAIL];
        $password = $data[PASSWORD];

        $userVerify = $this->modelUserAuth->login($mail, $password);

        if ($userVerify === null) {
            JsonResponse::error(
                'Mail ou mot de passe incorrect',
                401
            );
        }

        $this->ensureRequiredFields($userVerify, ['isVerified']);
        $this->ensureRequiredFields($userVerify, [ID_USER]);
        JsonResponse::success(
            'Connexion réussie',
            [
                ID_USER => $userVerify[ID_USER],
                'isVerified' => $userVerify['isVerified']
            ]
        );
    }


    public function logout()
    {
        session_unset();
        session_destroy();
        setcookie(session_name(), "", time() - 3600, "/");
        JsonResponse::success(
            'Deconnexion réussie.'
        );
    }

    public function forgetedPassword()
    {
        $data = $this->ensureDataReady();
        $this->ensureRequiredFields($data, [MAIL]);

        $mail = $data[MAIL];
        $userId = $this->modelUserAuth->checkMail($mail);
        if ($userId) {
            $verificationToken = hash('sha256', $data[MAIL] . CRON_KEY);
            $this->modelUserAuth->setNewToken($userId, $verificationToken);

            $this->controllerMail->sendMailToForgetedPassword($mail, $verificationToken);

            JsonResponse::success(
                'Un e-mail de réinitialisation du mot de passe a été envoyé si l\'adresse e-mail est enregistrée.',
                []
            );
        } else {
            JsonResponse::error(
                'Aucun utilisateur trouvé avec cette adresse e-mail.',
                404
            );
            return;
        }
    }

    public function resetPasswordLink($token = null)
    {
        if (!$token) {
            header('Location: ' . URI_FRONT . 'echec-reinitialisation-du-mot-de-passe?raison=token_manquant');
            exit();
        }

        $userId = $this->modelUserAuth->verifyEmail($token);
        if (!$userId) {
            header('Location: ' . URI_FRONT . 'echec?raison=token_invalide');
            exit();
        }

        $_SESSION[ID_USER] = $userId;
        header('Location: ' . URI_FRONT . 'reset-password');
        exit();
    }

    public function resetPassword()
    {
        $data = $this->ensureDataReady();
        $this->ensureRequiredFields($data, ['newPassword', 'confirmNewPassword']);

        $passwords = [
            PASSWORD => $data['newPassword'],
            'passwordConfirm' => $data['confirmNewPassword']
        ];
        $validation = $this->controlUserPasswordFormat($passwords);
        if ($validation['code'] == 0) {
            JsonResponse::error(
                $validation['message'],
                400
            );
        }

        $password = password_hash($data['newPassword'], PASSWORD_BCRYPT);
        $updatePassword = $this->modelUserAuth->updatePassword($_SESSION[ID_USER], $password);
        if (!$updatePassword) {
            JsonResponse::error(
                'Erreur lors de la mise à jour du mot de passe',
                500
            );
        }

        session_unset();
        session_destroy();
        setcookie(session_name(), "", time() - 3600, "/");
        JsonResponse::success(
            'Mot de passe réinitialisé avec succès. Veuillez vous reconnecter.',
            []
        );
    }
}
