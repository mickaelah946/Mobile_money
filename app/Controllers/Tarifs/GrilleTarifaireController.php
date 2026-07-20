<?php

namespace App\Controllers\Tarifs;

use App\Controllers\BaseController;
use App\Models\GrilleTarifaireModel;
use App\Models\TypeTransactionModel;
use App\Services\AuditService;

class GrilleTarifaireController extends BaseController
{
    protected GrilleTarifaireModel $grilleModel;
    protected TypeTransactionModel $typeModel;

    public function __construct()
    {
        $this->grilleModel = new GrilleTarifaireModel();
        $this->typeModel   = new TypeTransactionModel();
    }

    public function index()
    {
        $tarifs = $this->grilleModel
            ->select('grille_tarifaire.*, types_transaction.libelle as type_libelle')
            ->join('types_transaction', 'types_transaction.id = grille_tarifaire.type_transaction_id')
            ->orderBy('types_transaction.libelle')
            ->orderBy('grille_tarifaire.montant_min')
            ->findAll();

        return view('tarifs/index', ['tarifs' => $tarifs]);
    }

    public function create()
    {
        return view('tarifs/create', ['types' => $this->typeModel->findAll()]);
    }

    public function store()
    {
        $rules = [
            'type_transaction_id' => 'required|is_natural_no_zero',
            'montant_min'         => 'required|decimal',
            'montant_max'         => 'required|decimal',
            'frais_fixe'          => 'permit_empty|decimal',
            'frais_pourcentage'   => 'permit_empty|decimal',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = $this->grilleModel->insert([
            'type_transaction_id' => $this->request->getPost('type_transaction_id'),
            'montant_min'         => $this->request->getPost('montant_min'),
            'montant_max'         => $this->request->getPost('montant_max'),
            'frais_fixe'          => $this->request->getPost('frais_fixe') ?: 0,
            'frais_pourcentage'   => $this->request->getPost('frais_pourcentage') ?: 0,
            'actif'               => 1,
        ], true);

        (new AuditService())->log('CREATION_TARIF', 'grille_tarifaire', $id);

        return redirect()->to('/tarifs')->with('success', 'Tarif créé avec succès.');
    }

    public function edit(int $id)
    {
        $tarif = $this->grilleModel->find($id);

        if (! $tarif) {
            return redirect()->to('/tarifs')->with('error', 'Tarif introuvable.');
        }

        return view('tarifs/edit', ['tarif' => $tarif, 'types' => $this->typeModel->findAll()]);
    }

    public function update(int $id)
    {
        $tarif = $this->grilleModel->find($id);

        if (! $tarif) {
            return redirect()->to('/tarifs')->with('error', 'Tarif introuvable.');
        }

        $rules = [
            'type_transaction_id' => 'required|is_natural_no_zero',
            'montant_min'         => 'required|decimal',
            'montant_max'         => 'required|decimal',
            'frais_fixe'          => 'permit_empty|decimal',
            'frais_pourcentage'   => 'permit_empty|decimal',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->grilleModel->update($id, [
            'type_transaction_id' => $this->request->getPost('type_transaction_id'),
            'montant_min'         => $this->request->getPost('montant_min'),
            'montant_max'         => $this->request->getPost('montant_max'),
            'frais_fixe'          => $this->request->getPost('frais_fixe') ?: 0,
            'frais_pourcentage'   => $this->request->getPost('frais_pourcentage') ?: 0,
        ]);

        (new AuditService())->log('MODIFICATION_TARIF', 'grille_tarifaire', $id);

        return redirect()->to('/tarifs')->with('success', 'Tarif mis à jour.');
    }

    public function changerStatut(int $id)
    {
        $tarif = $this->grilleModel->find($id);

        if (! $tarif) {
            return redirect()->to('/tarifs')->with('error', 'Tarif introuvable.');
        }

        $nouveauStatut = (int) $tarif['actif'] === 1 ? 0 : 1;
        $this->grilleModel->update($id, ['actif' => $nouveauStatut]);

        (new AuditService())->log('CHANGEMENT_STATUT_TARIF', 'grille_tarifaire', $id, "actif = {$nouveauStatut}");

        return redirect()->to('/tarifs')->with('success', 'Statut du tarif mis à jour.');
    }
}
