<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeOperateurModel extends Model
{
    protected $table         = 'prefixes_operateurs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $createdField  = 'date_creation';

    protected $allowedFields = [
        'prefixe', 'operateur_nom', 'actif',
    ];

    protected $validationRules = [
        'prefixe'       => 'required|min_length[2]|max_length[10]|is_unique[prefixes_operateurs.prefixe,id,{id}]',
        'operateur_nom' => 'required|min_length[2]',
        'actif'         => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'prefixe' => [
            'is_unique' => 'Ce préfixe est déjà configuré pour un autre opérateur.',
        ],
    ];

    /**
     * Retrouve l'opérateur externe correspondant à un numéro de téléphone,
     * en comparant son préfixe à ceux enregistrés (opérateurs actifs
     * uniquement). Retourne null si le numéro appartient à notre propre
     * réseau (aucun préfixe externe ne correspond).
     */
    public function trouverOperateurParNumero(string $numero): ?array
    {
        $prefixes = $this->where('actif', 1)->findAll();

        foreach ($prefixes as $prefixeOperateur) {
            if (str_starts_with($numero, $prefixeOperateur['prefixe'])) {
                return $prefixeOperateur;
            }
        }

        return null;
    }
}