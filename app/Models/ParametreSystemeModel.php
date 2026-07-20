<?php

namespace App\Models;

use CodeIgniter\Model;

class ParametreSystemeModel extends Model
{
    protected $table         = 'parametres_systeme';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'date_maj';

    protected $allowedFields = ['cle', 'valeur', 'description'];

    protected $validationRules = [
        'cle'    => 'required|is_unique[parametres_systeme.cle,id,{id}]',
        'valeur' => 'required',
    ];

    public function getValeur(string $cle, $defaut = null)
    {
        $param = $this->where('cle', $cle)->first();

        return $param['valeur'] ?? $defaut;
    }
}
