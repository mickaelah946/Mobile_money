<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeOperateurModel extends Model
{
    protected $table         = 'prefixes_operateurs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'date_creation';
    protected $updatedField  = '';

    protected $allowedFields = ['prefixe', 'operateur_nom', 'actif'];

    protected $validationRules = [
        'prefixe'       => 'required|is_unique[prefixes_operateurs.prefixe,id,{id}]',
        'operateur_nom' => 'required|min_length[2]',
    ];

    /**
     * Retourne l'operateur externe correspondant au numero donne, ou null
     * si le numero appartient a notre propre reseau (aucun prefixe externe
     * ne correspond).
     */
    public function trouverOperateurParNumero(string $telephone): ?array
    {
        $telephone = preg_replace('/\s+/', '', $telephone);

        foreach ($this->where('actif', 1)->findAll() as $prefixeOperateur) {
            if (str_starts_with($telephone, $prefixeOperateur['prefixe'])) {
                return $prefixeOperateur;
            }
        }

        return null;
    }
}
