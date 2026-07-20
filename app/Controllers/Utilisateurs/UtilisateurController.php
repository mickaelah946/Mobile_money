<?php

namespace App\Controllers\Utilisateurs;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\UtilisateurModel;
use App\Services\AuditService;

class UtilisateurController extends BaseController
{
    protected UtilisateurModel $utilisateurModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->utilisateurModel = new UtilisateurModel();
        $this->roleModel        = new RoleModel();
    }

    public function index()
    {
        $utilisateurs = $this->utilisateurModel
            ->select('utilisateurs.*, roles.libelle as role_libelle')
            ->join('roles', 'roles.id = utilisateurs.role_id')
            ->orderBy('utilisateurs.date_creation', 'DESC')
            ->findAll();

        return view('utilisateurs/index', ['utilisateurs' => $utilisateurs]);
    }

    public function create()
    {
        return view('utilisateurs/create', ['roles' => $this->roleModel->findAll()]);
    }

    public function store()
    {
        $rules = [
            'nom'          => 'required|min_length[2]',
            'prenom'       => 'required|min_length[2]',
            'email'        => 'required|valid_email|is_unique[utilisateurs.email]',
            'telephone'    => 'required|min_length[8]|is_unique[utilisateurs.telephone]',
            'mot_de_passe' => 'required|min_length[6]',
            'role_id'      => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $matricule = 'TMP-' . time() . '-' . random_int(100, 999);

        $id = $this->utilisateurModel->insert([
            'matricule'    => $matricule,
            'nom'          => $this->request->getPost('nom'),
            'prenom'       => $this->request->getPost('prenom'),
            'email'        => $this->request->getPost('email'),
            'telephone'    => $this->request->getPost('telephone'),
            'mot_de_passe' => $this->request->getPost('mot_de_passe'),
            'role_id'      => $this->request->getPost('role_id'),
            'statut'       => 'ACTIF',
        ], true);

        $this->utilisateurModel->update($id, ['matricule' => 'UTI-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT)]);

        (new AuditService())->log('CREATION_UTILISATEUR', 'utilisateurs', $id);

        return redirect()->to('/utilisateurs')->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(int $id)
    {
        $utilisateur = $this->utilisateurModel->find($id);

        if (! $utilisateur) {
            return redirect()->to('/utilisateurs')->with('error', 'Utilisateur introuvable.');
        }

        return view('utilisateurs/edit', ['utilisateur' => $utilisateur, 'roles' => $this->roleModel->findAll()]);
    }

    public function update(int $id)
    {
        $utilisateur = $this->utilisateurModel->find($id);

        if (! $utilisateur) {
            return redirect()->to('/utilisateurs')->with('error', 'Utilisateur introuvable.');
        }

        $rules = [
            'nom'       => 'required|min_length[2]',
            'prenom'    => 'required|min_length[2]',
            'email'     => "required|valid_email|is_unique[utilisateurs.email,id,{$id}]",
            'telephone' => "required|min_length[8]|is_unique[utilisateurs.telephone,id,{$id}]",
            'role_id'   => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nom'       => $this->request->getPost('nom'),
            'prenom'    => $this->request->getPost('prenom'),
            'email'     => $this->request->getPost('email'),
            'telephone' => $this->request->getPost('telephone'),
            'role_id'   => $this->request->getPost('role_id'),
        ];

        if ($this->request->getPost('mot_de_passe')) {
            $data['mot_de_passe'] = $this->request->getPost('mot_de_passe');
        }

        $this->utilisateurModel->update($id, $data);

        (new AuditService())->log('MODIFICATION_UTILISATEUR', 'utilisateurs', $id);

        return redirect()->to('/utilisateurs')->with('success', 'Utilisateur mis à jour.');
    }

    public function changerStatut(int $id)
    {
        $utilisateur = $this->utilisateurModel->find($id);

        if (! $utilisateur) {
            return redirect()->to('/utilisateurs')->with('error', 'Utilisateur introuvable.');
        }

        $nouveauStatut = $this->request->getPost('statut');

        if (! in_array($nouveauStatut, ['ACTIF', 'INACTIF', 'SUSPENDU'], true)) {
            return redirect()->back()->with('error', 'Statut invalide.');
        }

        $this->utilisateurModel->update($id, ['statut' => $nouveauStatut]);

        (new AuditService())->log('CHANGEMENT_STATUT_UTILISATEUR', 'utilisateurs', $id, "Nouveau statut : {$nouveauStatut}");

        return redirect()->to('/utilisateurs')->with('success', 'Statut mis à jour.');
    }
}
