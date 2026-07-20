<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteModel extends Model
{
    protected $table         = 'comptes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'date_creation';
    protected $updatedField  = 'date_maj';

    protected $allowedFields = [
        'numero_compte', 'type_compte', 'client_id', 'agent_id',
        'solde', 'plafond', 'statut',
    ];

    protected $validationRules = [
        'numero_compte' => 'permit_empty|is_unique[comptes.numero_compte,id,{id}]',
        'type_compte'   => 'required|in_list[CLIENT,AGENT,SYSTEME]',
        'solde'         => 'permit_empty|decimal',
        'statut'        => 'permit_empty|in_list[ACTIF,SUSPENDU,BLOQUE]',
    ];

    public function findByClientId(int $clientId): ?array
    {
        return $this->where('client_id', $clientId)->first();
    }

    public function findByAgentId(int $agentId): ?array
    {
        return $this->where('agent_id', $agentId)->first();
    }

    public function findSystemAccount(): ?array
    {
        return $this->where('type_compte', 'SYSTEME')->first();
    }
}
