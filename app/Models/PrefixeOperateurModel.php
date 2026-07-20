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
     * Determine si un numero appartient a notre propre operateur (on-net),
     * selon le parametre PREFIXE_NOTRE_OPERATEUR (liste blanche : un seul
     * prefixe = nous, tout le reste = un autre operateur).
     */
    public function estInterne(string $numero): bool
    {
        $prefixeInterne = (new \App\Models\ParametreSystemeModel())->getValeur('PREFIXE_NOTRE_OPERATEUR', '');

        return $prefixeInterne !== '' && str_starts_with($numero, $prefixeInterne);
    }

    /**
     * Retrouve le nom de l'operateur externe correspondant a un numero
     * off-net, a partir des prefixes enregistres (actifs uniquement).
     * Sert uniquement a NOMMER l'operateur pour les rapports/notifications ;
     * n'est pas utilise pour decider si un numero est interne ou externe
     * (voir estInterne()) : un numero off-net reste valide meme si son
     * prefixe exact n'a pas ete configure ici.
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