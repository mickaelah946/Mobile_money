<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table         = 'transactions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'reference', 'type_transaction_id', 'compte_source_id', 'compte_destination_id',
        'montant', 'frais', 'montant_total', 'statut', 'motif_echec',
        'initiateur_utilisateur_id', 'initiateur_agent_id', 'description',
        'numero_destination_externe', 'date_transaction', 'date_traitement',
    ];

    protected $validationRules = [
        'reference'           => 'required|is_unique[transactions.reference,id,{id}]',
        'type_transaction_id' => 'required|is_natural_no_zero',
        'montant'             => 'required|decimal|greater_than[0]',
        'statut'              => 'permit_empty|in_list[EN_ATTENTE,REUSSIE,ECHOUEE,ANNULEE]',
    ];

    public function historiqueCompte(int $compteId, int $limit = 50)
    {
        return $this->groupStart()
            ->where('compte_source_id', $compteId)
            ->orWhere('compte_destination_id', $compteId)
            ->groupEnd()
            ->orderBy('date_transaction', 'DESC')
            ->findAll($limit);
    }
}
