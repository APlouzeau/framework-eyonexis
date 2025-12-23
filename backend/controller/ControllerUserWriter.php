<?php


class ControllerUserWriter extends ControllerUserAuth
{
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

    public function updateUserProfile()
    {
        $this->verifyConnectBack();
        $data = $this->ensureDataReady();
        $this->ensureRequiredFields($data, [FIRST_NAME, LAST_NAME, MAIL]);

        $verification = $this->controlUserInformations($data, false);
        if ($verification['code'] == 0) {
            JsonResponse::error(
                $verification['message'],
                400
            );
        }

        $existingUserId = $this->modelUser->checkMail($data[MAIL]);
        if ($existingUserId && $existingUserId != $_SESSION[ID_USER]) {
            JsonResponse::error(
                'Cette adresse e-mail est déjà utilisée par un autre utilisateur.',
                409
            );
        }
        // Préparer les données pour la mise à jour
        $updateData = [
            ID_USER => $_SESSION[ID_USER],
            NICK_NAME => isset($data[NICK_NAME]) ? $data[NICK_NAME] : null,
            FIRST_NAME => $data[FIRST_NAME],
            LAST_NAME => $data[LAST_NAME],
            MAIL => $data[MAIL],
            'address' => isset($data['address']) ? $data['address'] : null,
            'address2' => isset($data['address2']) ? $data['address2'] : null,
            'address3' => isset($data['address3']) ? $data['address3'] : null,
            'zip' => isset($data['zip']) ? $data['zip'] : null,
            'city' => isset($data['city']) ? $data['city'] : null,
            'country' => isset($data['country']) ? $data['country'] : null,
        ];

        $user = new EntitieUser($updateData);
        $updateResult = $this->modelUser->updateUser($user);

        if ($updateResult) {
            JsonResponse::success(
                'Profil mis à jour avec succès',
                []
            );
        } else {
            JsonResponse::error(
                'Erreur lors de la mise à jour du profil',
                500
            );
        }
    }

    public function deleteUser()
    {
        $this->verifyConnectAdmin(); // Vérifie connexion + role admin
        $data = $this->ensureDataReady();
        $this->ensureRequiredFields($data, ['idUser']);

        $idUser = $data['idUser'];
        $user = new EntitieUser(['idUser' => $idUser]);
        $this->modelUser->deleteUser($user);

        JsonResponse::success('Utilisateur supprimé', ['idUser' => $idUser]);
    }

    public function updateUserPassword()
    {
        $this->verifyConnectBack();
        $data = $this->ensureDataReady();
        $this->ensureRequiredFields($data, ['oldPassword', 'newPassword', 'confirmNewPassword']);

        $passwordsOk = $this->modelUser->checkPassword($_SESSION[ID_USER], $data['oldPassword']);
        if (!$passwordsOk) {
            JsonResponse::error(
                'Ancien mot de passe incorrect',
                400
            );
        }

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

        $user = new EntitieUser([
            ID_USER => $_SESSION[ID_USER],
            PASSWORD => password_hash($data['newPassword'], PASSWORD_BCRYPT),
        ]);

        $updateResult = $this->modelUser->updateUserPassword($user);

        if (!$updateResult) {
            JsonResponse::error(
                'Erreur lors de la mise à jour du mot de passe',
                500
            );
        }
        JsonResponse::success(
            'Mot de passe mis à jour avec succès',
            []
        );
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
        $updatePassword = $this->modelUser->updatePassword($_SESSION[ID_USER], $password);
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
