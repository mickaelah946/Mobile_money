<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\UtilisateurModel;
use App\Services\AuditService;

class AuthController extends BaseController
{
    public function showLogin()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function login()
    {
        $rules = [
            'telephone'    => 'required',
            'mot_de_passe' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez renseigner votre téléphone et votre mot de passe.');
        }

        $telephone   = $this->request->getPost('telephone');
        $motDePasse  = $this->request->getPost('mot_de_passe');

        $utilisateurModel = new UtilisateurModel();
        $utilisateur       = $utilisateurModel->where('telephone', $telephone)->first();

        if (! $utilisateur || ! password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            return redirect()->back()->withInput()->with('error', 'Identifiants incorrects.');
        }

        if ($utilisateur['statut'] !== 'ACTIF') {
            return redirect()->back()->with('error', 'Votre compte est inactif ou suspendu.');
        }

        $role = (new RoleModel())->find($utilisateur['role_id']);

        $this->session->set([
            'isLoggedIn'    => true,
            'utilisateurId' => $utilisateur['id'],
            'nom'           => $utilisateur['nom'],
            'prenom'        => $utilisateur['prenom'],
            'roleCode'      => $role['code'] ?? null,
            'roleLabel'     => $role['libelle'] ?? null,
        ]);

        $utilisateurModel->update($utilisateur['id'], ['derniere_connexion' => date('Y-m-d H:i:s')]);

        (new AuditService())->log('CONNEXION', 'utilisateurs', $utilisateur['id']);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        if ($this->session->get('isLoggedIn')) {
            (new AuditService())->log('DECONNEXION', 'utilisateurs', $this->session->get('utilisateurId'));
        }

        $this->session->destroy();

        return redirect()->to('/login');
    }
}
