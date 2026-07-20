<?php

namespace App\Controllers\Parametres;

use App\Controllers\BaseController;
use App\Models\ParametreSystemeModel;
use App\Services\AuditService;

class ParametreController extends BaseController
{
    protected ParametreSystemeModel $parametreModel;

    public function __construct()
    {
        $this->parametreModel = new ParametreSystemeModel();
    }

    public function index()
    {
        $parametres = $this->parametreModel->orderBy('cle')->findAll();

        return view('parametres/index', ['parametres' => $parametres]);
    }

    public function update(int $id)
    {
        $parametre = $this->parametreModel->find($id);

        if (! $parametre) {
            return redirect()->to('/parametres')->with('error', 'Paramètre introuvable.');
        }

        $rules = ['valeur' => 'required'];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'La valeur est obligatoire.');
        }

        $this->parametreModel->update($id, ['valeur' => $this->request->getPost('valeur')]);

        (new AuditService())->log('MODIFICATION_PARAMETRE', 'parametres_systeme', $id, $parametre['cle'] . ' = ' . $this->request->getPost('valeur'));

        return redirect()->to('/parametres')->with('success', 'Paramètre mis à jour.');
    }
}
