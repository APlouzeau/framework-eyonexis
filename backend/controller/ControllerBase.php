<?php

class ControllerBase
{
    use JsonRequestHandler;

    protected $modelUser;

    public function __construct()
    {
        $this->modelUser = new ModelUser();
    }

    /**
     * Vérifie que l'utilisateur est connecté
     */
    protected function requireAuth(): void
    {
        if (!isset($_SESSION[ID_USER]) || empty($_SESSION[ID_USER])) {
            JsonResponse::error(USER_NOT_CONNECTED, 401);
        }
    }

    /**
     * Vérifie que l'utilisateur est admin
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();

        if (!isset($_SESSION[ROLE]) || $_SESSION[ROLE] !== ADMIN) {
            JsonResponse::error(UNAUTHORIZED_USER, 403);
        }
    }
}
