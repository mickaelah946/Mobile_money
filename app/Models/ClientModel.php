<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table         = 'clients';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'date_creation';
    protected $updatedField  = 'date_maj';

    protected $allowedFields = [
        'numero_client', 'nom', 'prenom', 'telephone',
        'type_piece', 'numero_piece', 'date_naissance', 'adresse',
        'statut', 'cree_par',
    ];

    protected $validationRules = [
        'numero_client' => 'permit_empty|is_unique[clients.numero_client,id,{id}]',
        'nom'           => 'required|min_length[2]',
        'prenom'        => 'required|min_length[2]',
        'telephone'     => 'required|min_length[8]|is_unique[clients.telephone,id,{id}]',
        'type_piece'    => 'permit_empty|in_list[CNI,PASSEPORT,PERMIS]',
        'statut'        => 'permit_empty|in_list[ACTIF,SUSPENDU,BLOQUE]',
    ];
}
