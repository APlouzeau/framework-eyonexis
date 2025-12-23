<?php

trait JsonRequestHandler
{
    private function getJsonRequestData(): array
    {
        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            JsonResponse::error(
                'Requête JSON invalide',
                400
            );
        }
        return $data;
    }

    private function ensurePostMethod(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            JsonResponse::error(
                'Erreur de méthode',
                405
            );
        }
    }

    public function ensureDataReady(): array
    {
        $this->ensurePostMethod();
        $data = $this->getJsonRequestData();
        return $data;
    }

    public function ensureRequiredFields(array $data, array $requiredFields): void
    {
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            JsonResponse::error(
                'Paramètres requis manquants (' . implode(', ', $missingFields) . ')',
                400
            );
        }
    }
}
