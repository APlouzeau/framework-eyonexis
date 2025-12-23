<?php


class ControllerUserAuth extends ControllerUserVerify
{
    public function login()
    {
        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $mail = $data[MAIL];
            $password = $data[PASSWORD];

            $userVerify = $this->modelUser->login($mail, $password);

            if ($userVerify === null) {
                $response = [
                    'code' => 0,
                    'message' => 'Nom ou mot de passe incorrect.'
                ];
            }
            if (isset($userVerify['isVerified']) && $userVerify['isVerified'] === false) {
                $response = [
                    'code' => 0,
                    'message' => 'Utilisateur non vérifié. Veuillez vérifier votre adresse e-mail.'
                ];
            }


            if (isset($userVerify[ID_USER])) {
                $_SESSION[ID_USER] = $userVerify[ID_USER];
                $response = [
                    'code' => 1,
                    'message' => 'Connexion réussie.',
                ];
            }
        } else {
            $response = [
                'code' => 0,
                'message' => 'Erreur de méthode'
            ];
        }
        echo json_encode($response);
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        setcookie(session_name(), "", time() - 3600, "/");
        echo json_encode([
            'code' => 1,
            'message' => 'Deconnexion réussie.'
        ]);
    }

    public function getUserInformations()
    {
        $this->verifyConnectBack();
        $user = new EntitieUser([
            ID_USER => $_SESSION[ID_USER]
        ]);
        $response = [
            'code' => 1,
            'message' => 'Utilisateur trouvé',
            'data' =>
            $this->modelUser->getUser($user)
        ];

        echo json_encode($response);
    }

    public function listUsers()
    {
        $users = $this->modelUser->getAllUsers();
        $result = [];
        foreach ($users as $user) {
            $result[] = [
                MAIL => $user->getMail(),
            ];
        }
        echo json_encode($result);
    }

    public function forgetedPassword()
    {
        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode([
                'code' => 0,
                'message' => 'Erreur de méthode',
            ]);
            return;
        }
        if (!isset($data[MAIL])) {
            echo json_encode([
                'code' => 0,
                'message' => 'Paramètre manquant (mail)',
            ]);
            return;
        }
        $mail = $data[MAIL];
        $userId = $this->modelUser->checkMail($mail);
        if ($userId) {
            $verificationToken = hash('sha256', $data[MAIL] . CRON_KEY);
            $this->modelUser->setNewToken($userId, $verificationToken);

            $this->controllerMail->sendMailToForgetedPassword($mail, $verificationToken);

            echo json_encode([
                'code' => 1,
                'message' => 'Un e-mail de réinitialisation du mot de passe a été envoyé.',
            ]);
            return;
        } else {
            echo json_encode([
                'code' => 0,
                'message' => 'Aucun utilisateur trouvé avec cette adresse e-mail.',
            ]);
            return;
        }
    }

    public function resetPasswordLink($token = null)
    {
        if (!$token) {
            echo json_encode([
                'code' => 0,
                'message' => 'Token manquant',
            ]);
            header('Location: ' . URI_FRONT . 'echec-reinitialisation-du-mot-de-passe?raison=token_manquant');
            return;
        }

        $userId = $this->modelUser->verifyEmail($token);
        if (!$userId) {
            header('Location: ' . URI_FRONT . 'echec?raison=token_invalide');
            echo json_encode([
                'code' => 0,
                'message' => 'Token invalide ou expiré',
            ]);
            return;
        }

        if ($userId) {
            $_SESSION[ID_USER] = $userId;
            header('Location: ' . URI_FRONT . 'reset-password');
        }
    }
}
