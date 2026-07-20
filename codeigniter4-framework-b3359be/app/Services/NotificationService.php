<?php

namespace App\Services;

use App\Models\NotificationModel;

class NotificationService
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function envoyer(array $data): array
    {
        // Simulation d'envoi (pas de passerelle SMS réelle) : on enregistre
        // simplement la notification avec le statut ENVOYE.
        $payload = array_merge([
            'client_id'      => null,
            'agent_id'       => null,
            'transaction_id' => null,
            'canal'          => 'SMS',
            'statut'         => 'ENVOYE',
        ], $data);

        $id = $this->notificationModel->insert($payload, true);

        return $this->notificationModel->find($id);
    }
}
