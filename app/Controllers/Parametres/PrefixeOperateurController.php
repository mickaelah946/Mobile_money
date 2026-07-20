<?php

namespace App\Controllers\Parametres;

use App\Controllers\BaseController;
use App\Models\PrefixeOperateurModel;
use App\Services\AuditService;

class PrefixeOperateurController extends BaseController
{
    protected PrefixeOperateurModel $prefixeModel;

    public function __construct()
    {
        $this->prefixeModel = new PrefixeOperateurModel();
    }

    public function index()
    {
        $prefixes = $this->prefixeModel->orderBy('prefixe')->findAll();

        return view('parametres/prefixes/index', ['prefixes' => $prefixes]);
    }

    public function create()
    {
        return view('parametres/prefixes/create');
    }

    public function store()
    {
        $rules = [
            'prefixe'       => 'required|min_length[2]|max_length[10]|is_unique[prefixes_operateurs.prefixe]',
            'operateur_nom' => 'required|min_length[2]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = $this->prefixeModel->insert([
            'prefixe'       => $this->request->getPost('prefixe'),
            'operateur_nom' => $this->request->getPost('operateur_nom'),
            'actif'         => 1,
        ], true);

        (new AuditService())->log('CREATION_PREFIXE_OPERATEUR', 'prefixes_operateurs', $id);

        return redirect()->to('/parametres/prefixes')->with('success', 'Préfixe opérateur créé avec succès.');
    }

    public function edit(int $id)
    {
        $prefixe = $this->prefixeModel->find($id);

        if (! $prefixe) {
            return redirect()->to('/parametres/prefixes')->with('error', 'Préfixe introuvable.');
        }

        return view('parametres/prefixes/edit', ['prefixe' => $prefixe]);
    }

    public function update(int $id)
    {
        $prefixe = $this->prefixeModel->find($id);

        if (! $prefixe) {
            return redirect()->to('/parametres/prefixes')->with('error', 'Préfixe introuvable.');
        }

        $rules = [
            'prefixe'       => "required|min_length[2]|max_length[10]|is_unique[prefixes_operateurs.prefixe,id,{$id}]",
            'operateur_nom' => 'required|min_length[2]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->prefixeModel->update($id, [
            'prefixe'       => $this->request->getPost('prefixe'),
            'operateur_nom' => $this->request->getPost('operateur_nom'),
        ]);

        (new AuditService())->log('MODIFICATION_PREFIXE_OPERATEUR', 'prefixes_operateurs', $id);

        return redirect()->to('/parametres/prefixes')->with('success', 'Préfixe mis à jour.');
    }

    public function changerStatut(int $id)
    {
        $prefixe = $this->prefixeModel->find($id);

        if (! $prefixe) {
            return redirect()->to('/parametres/prefixes')->with('error', 'Préfixe introuvable.');
        }

        $nouveauStatut = (int) $prefixe['actif'] === 1 ? 0 : 1;
        $this->prefixeModel->update($id, ['actif' => $nouveauStatut]);

        (new AuditService())->log('CHANGEMENT_STATUT_PREFIXE_OPERATEUR', 'prefixes_operateurs', $id, "actif = {$nouveauStatut}");

        return redirect()->to('/parametres/prefixes')->with('success', 'Statut du préfixe mis à jour.');
    }

    public function delete(int $id)
    {
        $prefixe = $this->prefixeModel->find($id);

        if (! $prefixe) {
            return redirect()->to('/parametres/prefixes')->with('error', 'Préfixe introuvable.');
        }

        $this->prefixeModel->delete($id);

        (new AuditService())->log('SUPPRESSION_PREFIXE_OPERATEUR', 'prefixes_operateurs', $id);

        return redirect()->to('/parametres/prefixes')->with('success', 'Préfixe supprimé.');
    }
}