<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeTransactionModel extends Model
{
    protected $table         = 'types_transaction';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = ['code', 'libelle', 'description'];

    protected $validationRules = [
        'code'    => 'required|alpha_dash|is_unique[types_transaction.code,id,{id}]',
        'libelle' => 'required|min_length[2]',
    ];

    public function findByCode(string $code): ?array
    {
        return $this->where('code', $code)->first();
    }
}
