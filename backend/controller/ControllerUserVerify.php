<?php


class ControllerUserVerify
{
    protected $modelUser;
    protected $controllerMail;
    use JsonRequestHandler;

    private $userNotConnected = [
        'code' => 0,
        'message' => USER_NOT_CONNECTED
    ];

    public function __construct()
    {
        $this->modelUser = new ModelUser();
        $this->controllerMail = new ControllerMail();
    }

    public function verifyConnectBack()
    {

        if (isset($_SESSION[ID_USER]) && !empty($_SESSION[ID_USER])) {
            return true;
        } else {
            http_response_code(401);
            $response = $this->userNotConnected;
            echo json_encode($response);
            exit();
        }
    }

    public function verifyConnectAdmin()
    {
        if (isset($_SESSION[ID_USER]) && !empty($_SESSION[ID_USER]) && isset($_SESSION[ROLE]) && $_SESSION[ROLE] === ADMIN) {
            return true;
        } else {
            http_response_code(401);
            $response = [
                'code' => 0,
                'message' => UNAUTHORIZED_USER
            ];
            echo json_encode($response);
            exit();
        }
    }
    public function verifyEmail($token = null)
    {
        if (!$token) {
            $response = [
                'code' => 0,
                'message' => 'Token manquant',
            ];
            header('Location: ' . URI_FRONT . 'echec?raison=token_manquant');
        } else {
            $user = $this->modelUser->verifyEmail($token);
            if ($user) {
                $response = [
                    'code' => 1,
                    'message' => 'Adresse e-mail vérifiée avec succès',
                ];
                header('Location: ' . URI_FRONT . 'success');
            } else {
                $response = [
                    'code' => 0,
                    'message' => 'Erreur lors de la vérification de l\'adresse e-mail',
                ];
                header('Location: ' . URI_FRONT . 'echec?raison=token_invalide');
            }
        }
        echo json_encode($response);
    }
    public function controlUserInformations(array $data, bool $requireNickname = true)
    {
        // 1. Validation de l'adresse e-mail (filter_var est la meilleure méthode)
        if (!filter_var($data[MAIL], FILTER_VALIDATE_EMAIL)) {
            return [
                'code' => 0,
                'message' => 'Adresse e-mail invalide',
            ];
        }

        // 2. Validation du pseudo
        if ($requireNickname) {
            if (strlen($data[NICK_NAME]) < 2 || strlen($data[NICK_NAME]) > 25) {
                return [
                    'code' => 0,
                    'message' => 'Le pseudo doit contenir entre 2 et 25 caractères',
                ];
            }
            // Regex pour autoriser lettres, chiffres et underscore pour le pseudo
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $data[NICK_NAME])) {
                return [
                    'code' => 0,
                    'message' => 'Le pseudo ne peut contenir que des lettres (sans accent), des chiffres et des underscores (_).',
                ];
            }
        }

        // 3. Validation du prénom
        if (strlen($data['firstName']) < 2 || strlen($data['firstName']) > 25) {
            return [
                'code' => 0,
                'message' => 'Le prénom doit contenir entre 2 et 25 caractères',
            ];
        }

        // 4. Validation du nom
        if (strlen($data['lastName']) < 2 || strlen($data['lastName']) > 25) {
            return [
                'code' => 0,
                'message' => 'Le nom doit contenir entre 2 et 25 caractères',
            ];
        }

        // Si toutes les vérifications ci-dessus sont passées, les informations sont valides.
        return [
            'code' => 1,
            'message' => 'Informations valides',
        ];
    }

    public function controlUserPasswordFormat(array $data)
    {
        if ($data[PASSWORD] != $data['passwordConfirm']) {
            return [
                'code' => 0,
                'message' => 'Les mots de passe ne correspondent pas',
            ];
        }

        $regex = "/((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,60})/";
        if (!preg_match($regex, $data[PASSWORD])) {
            return [
                'code' => 0,
                'message' => 'Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.',
            ];
        }

        return [
            'code' => 1,
            'message' => 'Mot de passe valide',
        ];
    }
}
