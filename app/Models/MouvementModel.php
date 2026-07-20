<?php

namespace App\Models;

use CodeIgniter\Model;

class MouvementModel extends Model
{
    protected $table         = 'mouvements';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'date_mouvement';
    protected $updatedField  = '';

    protected $allowedFields = [
        'transaction_id', 'compte_id', 'sens', 'montant', 'solde_avant', 'solde_apres',
    ];

    protected $validationRules = [
        'transaction_id' => 'required|is_natural_no_zero',
        'compte_id'      => 'required|is_natural_no_zero',
        'sens'           => 'required|in_list[DEBIT,CREDIT]',
        'montant'        => 'required|decimal|greater_than[0]',
    ];

    public function historiqueCompte(int $compteId, int $limit = 50)
    {
        return $this->where('compte_id', $compteId)
            ->orderBy('date_mouvement', 'DESC')
            ->findAll($limit);
    }
}
