<?php

class ControllerError
{

    public function index($handler, $method, $uri)
    {
        echo "error";
        var_dump($handler);
        var_dump($method);
        var_dump($uri);
    }

    public function validateData($data, string $errorMessage, int $httpCode = 400)
    {
        if (empty($data)) {
            JsonResponse::error(
                $errorMessage,
                $httpCode
            );
        }
        return true;
    }

    public function unauthorizedResponse(string $message = 'Unauthorized')
    {
        JsonResponse::error(
            $message,
            401
        );
    }

    public function serverErrorResponse(string $message = 'Erreur serveur')
    {
        JsonResponse::error(
            $message,
            500
        );
    }
}
