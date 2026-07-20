<?php

namespace App\Models;

use CodeIgniter\Model;

class UtilisateurModel extends Model
{
    protected $table         = 'utilisateurs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'date_creation';
    protected $updatedField  = 'date_maj';

    protected $allowedFields = [
        'matricule', 'nom', 'prenom', 'email', 'telephone',
        'mot_de_passe', 'role_id', 'statut', 'derniere_connexion',
    ];

    protected $validationRules = [
        'matricule' => 'required|is_unique[utilisateurs.matricule,id,{id}]',
        'nom'       => 'required|min_length[2]',
        'prenom'    => 'required|min_length[2]',
        'email'     => 'required|valid_email|is_unique[utilisateurs.email,id,{id}]',
        'telephone' => 'required|min_length[8]|is_unique[utilisateurs.telephone,id,{id}]',
        'role_id'   => 'required|is_natural_no_zero',
        'statut'    => 'permit_empty|in_list[ACTIF,INACTIF,SUSPENDU]',
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['mot_de_passe'])) {
            $data['data']['mot_de_passe'] = password_hash($data['data']['mot_de_passe'], PASSWORD_DEFAULT);
        }

        return $data;
    }
}
