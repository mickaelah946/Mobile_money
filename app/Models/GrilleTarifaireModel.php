<?php

namespace App\Models;

use CodeIgniter\Model;

class GrilleTarifaireModel extends Model
{
    protected $table         = 'grille_tarifaire';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'date_creation';
    protected $updatedField  = '';

    protected $allowedFields = [
        'type_transaction_id', 'montant_min', 'montant_max',
        'frais_fixe', 'frais_pourcentage', 'actif', 'date_debut', 'date_fin',
    ];

    protected $validationRules = [
        'type_transaction_id' => 'required|is_natural_no_zero',
        'montant_min'         => 'required|decimal',
        'montant_max'         => 'required|decimal',
        'frais_fixe'          => 'permit_empty|decimal',
        'frais_pourcentage'   => 'permit_empty|decimal',
    ];

    public function findApplicable(int $typeTransactionId, float $montant): ?array
    {
        return $this->where('type_transaction_id', $typeTransactionId)
            ->where('actif', 1)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();
    }
}
