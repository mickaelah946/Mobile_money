<?php

namespace App\Services;

use App\Models\LogActiviteModel;

class AuditService
{
    protected LogActiviteModel $logModel;

    public function __construct()
    {
        $this->logModel = new LogActiviteModel();
    }

    public function log(string $action, ?string $cibleTable = null, ?int $cibleId = null, ?string $details = null): void
    {
        $this->logModel->insert([
            'utilisateur_id' => session()->get('utilisateurId'),
            'action'         => $action,
            'cible_table'    => $cibleTable,
            'cible_id'       => $cibleId,
            'details'        => $details,
            'adresse_ip'     => service('request')->getIPAddress(),
        ]);
    }
}
