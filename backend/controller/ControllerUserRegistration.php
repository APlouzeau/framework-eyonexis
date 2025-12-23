<?php


class ControllerUserRegistration extends ControllerBaseUser
{
    protected $controllerMail;

    public function __construct()
    {
        parent::__construct();
        $this->controllerMail = new ControllerBaseMail();
    }
    public function register()
    {
        $data = $this->ensureDataReady();
        $this->ensureRequiredFields($data, [NICK_NAME, FIRST_NAME, LAST_NAME, MAIL, PASSWORD, 'passwordConfirm']);

        $validationUserInformations = $this->controlUserInformations($data);
        if ($validationUserInformations['code'] == 0) {
            JsonResponse::error(
                $validationUserInformations['message'],
                400
            );
        }

        $validationPassword = $this->controlUserPasswordFormat($data);
        if ($validationPassword['code'] == 0) {
            JsonResponse::error(
                $validationPassword['message'],
                400
            );
        }

        $checkMail = $this->modelUser->checkMail($data[MAIL]);
        if ($checkMail) {
            JsonResponse::error(
                'Cette adresse e-mail est déjà utilisée.',
                409
            );
        }

        $verificationToken = hash('sha256', $data[MAIL] . $data[NICK_NAME] . CRON_KEY);
        $user = new EntitieUser([
            NICK_NAME => $data[NICK_NAME],
            FIRST_NAME => $data[FIRST_NAME],
            LAST_NAME => $data[LAST_NAME],
            MAIL => $data[MAIL],
            PASSWORD => password_hash($data[PASSWORD], PASSWORD_BCRYPT),
            'verifyToken' => $verificationToken,
        ]);

        $register = $this->modelUser->register($user);

        if (!$register) {
            JsonResponse::error(
                'Erreur lors de l\'inscription. Veuillez réessayer.',
                500
            );
        }

        $this->controllerMail->sendMailToRegister($user, $verificationToken);
        JsonResponse::success(
            'Inscription réussie. Un e-mail de vérification a été envoyé.',
            []
        );
    }

    public function verifyEmail($token = null)
    {
        if (!$token) {

            header('Location: ' . URI_FRONT . 'echec?raison=token_manquant');
            exit();
        }
        $user = $this->modelUser->verifyEmail($token);
        if ($user) {
            header('Location: ' . URI_FRONT . 'success');
            exit();
        }

        header('Location: ' . URI_FRONT . 'echec?raison=token_invalide');
        exit();
    }
}
