<?php

class ControllerBaseUser extends ControllerBase
{
    protected function controlUserPasswordFormat(array $data)
    {
        if ($data[PASSWORD] != $data['passwordConfirm']) {
            return [
                'code' => 0,
                'message' => 'Les mots de passe ne correspondent pas',
            ];
        }

        $regex = "/((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W]).{8,60})/";
        if (!preg_match($regex, $data[PASSWORD])) {
            JsonResponse::error(
                'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.',
                400
            );
        }

        return [
            'code' => 1,
            'message' => 'Mot de passe valide',
        ];
    }

    protected function controlUserInformations(array $data, bool $requireNickname = true)
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
}
