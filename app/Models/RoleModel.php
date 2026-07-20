<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'date_creation';
    protected $updatedField  = '';

    protected $allowedFields = ['code', 'libelle', 'description'];

    protected $validationRules = [
        'code'    => 'required|alpha_dash|is_unique[roles.code,id,{id}]',
        'libelle' => 'required|min_length[2]',
    ];
}
