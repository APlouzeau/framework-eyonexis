<?php


class ControllerUserManagement extends ControllerBaseUser
{
    public function listUsers()
    {
        $this->requireAdmin(); // Vérifie connexion + role admin
        $users = $this->modelUser->getAllUsers();
        $result = [];
        foreach ($users as $user) {
            $result[] = [
                MAIL => $user->getMail(),
            ];
        }
        JsonResponse::success(
            'Liste des utilisateurs récupérée avec succès',
            $result
        );
    }

    public function deleteUser()
    {
        $this->requireAdmin(); // Vérifie connexion + role admin
        $data = $this->ensureDataReady();
        $this->ensureRequiredFields($data, ['idUser']);

        $idUser = $data['idUser'];
        $user = new EntitieUser(['idUser' => $idUser]);
        $this->modelUser->deleteUser($user);

        JsonResponse::success('Utilisateur supprimé', ['idUser' => $idUser]);
    }
}
