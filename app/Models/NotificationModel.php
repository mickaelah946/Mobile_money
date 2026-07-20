<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'date_envoi';
    protected $updatedField  = '';

    protected $allowedFields = [
        'client_id', 'agent_id', 'transaction_id', 'canal', 'message', 'statut',
    ];

    protected $validationRules = [
        'message' => 'required',
        'canal'   => 'permit_empty|in_list[SMS,EMAIL]',
        'statut'  => 'permit_empty|in_list[ENVOYE,ECHEC]',
    ];
}
