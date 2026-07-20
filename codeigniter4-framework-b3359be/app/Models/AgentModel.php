<?php

namespace App\Models;

use CodeIgniter\Model;

class AgentModel extends Model
{
    protected $table         = 'agents';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'date_creation';
    protected $updatedField  = 'date_maj';

    protected $allowedFields = [
        'code_agent', 'nom', 'prenom', 'telephone', 'zone',
        'adresse', 'statut', 'cree_par',
    ];

    protected $validationRules = [
        'code_agent' => 'permit_empty|is_unique[agents.code_agent,id,{id}]',
        'nom'        => 'required|min_length[2]',
        'prenom'     => 'required|min_length[2]',
        'telephone'  => 'required|min_length[8]|is_unique[agents.telephone,id,{id}]',
        'statut'     => 'permit_empty|in_list[ACTIF,SUSPENDU,BLOQUE]',
    ];
}
