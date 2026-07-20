<?php

namespace App\Models;

use CodeIgniter\Model;

class LogActiviteModel extends Model
{
    protected $table         = 'logs_activites';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'date_action';
    protected $updatedField  = '';

    protected $allowedFields = [
        'utilisateur_id', 'action', 'cible_table', 'cible_id', 'details', 'adresse_ip',
    ];

    protected $validationRules = [
        'action' => 'required',
    ];
}
