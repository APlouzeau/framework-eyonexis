<?php

class JsonResponse
{
    /**
     * Envoie une réponse JSON de succès
     */
    public static function success(string $message, array $data = [], int $httpCode = 200): void
    {
        http_response_code($httpCode);
        echo json_encode([
            'code' => 1,
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }

    /**
     * Envoie une réponse JSON d'erreur
     */
    public static function error(string $message, int $httpCode = 400, array $data = []): void
    {
        http_response_code($httpCode);
        echo json_encode([
            'code' => 0,
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }

    /**
     * Envoie une réponse JSON personnalisée
     */
    public static function send(int $code, string $message, array $data = [], int $httpCode = 200): void
    {
        http_response_code($httpCode);
        echo json_encode([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }
}
