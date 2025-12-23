<?php

class ControllerUserProfile extends ControllerBaseUser
{

    protected $modelUserProfile;
    public function __construct()
    {
        $this->modelUserProfile = new ModelUserProfile();
    }
    public function getUserInformations()
    {
        $this->requireAuth();
        $user = $this->modelUserProfile->getUser(new EntitieUser([ID_USER => $_SESSION[ID_USER]]));
        if (!$user) {
            JsonResponse::error(
                'Utilisateur non trouvé',
                404
            );
        };
        JsonResponse::success(
            'Informations utilisateur récupérées avec succès',
            [
                NICK_NAME => $user->getNickName(),
                FIRST_NAME => $user->getFirstName(),
                LAST_NAME => $user->getLastName(),
                MAIL => $user->getMail(),
                ROLE => $user->getRole(),
                'address' => $user->getAddress(),
                'address2' => $user->getAddress2(),
                'address3' => $user->getAddress3(),
                'zip' => $user->getZip(),
                'city' => $user->getCity(),
                'country' => $user->getCountry(),
            ]
        );
    }


    public function updateUserProfile()
    {
        $this->requireAuth();
        $data = $this->ensureDataReady();
        $this->ensureRequiredFields($data, [FIRST_NAME, LAST_NAME, MAIL]);

        $verification = $this->controlUserInformations($data, false);
        if ($verification['code'] == 0) {
            JsonResponse::error(
                $verification['message'],
                400
            );
        }

        $existingUserId = $this->modelUserProfile->checkMail($data[MAIL]);
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
        $updateResult = $this->modelUserProfile->updateUser($user);

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

    public function updateUserPassword()
    {
        $this->requireAuth();
        $data = $this->ensureDataReady();
        $this->ensureRequiredFields($data, ['oldPassword', 'newPassword', 'confirmNewPassword']);

        $passwordsOk = $this->modelUserProfile->checkPassword($_SESSION[ID_USER], $data['oldPassword']);
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

        $idUser = $_SESSION[ID_USER];
        $password = password_hash($data['newPassword'], PASSWORD_BCRYPT);

        $updateResult = $this->modelUserProfile->updatePassword($idUser, $password);
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
}
