<?php


class ControllerUserManagement extends ControllerBaseUser
{
    private $modelUserManagement;
    public function __construct()
    {
        $this->modelUserManagement = new ModelUserManagement();
    }
    public function listUsers()
    {
        $this->requireAdmin(); // Vérifie connexion + role admin
        $users = $this->modelUserManagement->getAllUsers();
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
        $this->modelUserManagement->deleteUser($user);

        JsonResponse::success('Utilisateur supprimé', ['idUser' => $idUser]);
    }
}
