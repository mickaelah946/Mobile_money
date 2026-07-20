<?php

namespace App\Controllers\Logs;

use App\Controllers\BaseController;
use App\Models\LogActiviteModel;

class LogController extends BaseController
{
    public function index()
    {
        $logModel = new LogActiviteModel();

        $logs = $logModel
            ->select('logs_activites.*, utilisateurs.nom as utilisateur_nom, utilisateurs.prenom as utilisateur_prenom')
            ->join('utilisateurs', 'utilisateurs.id = logs_activites.utilisateur_id', 'left')
            ->orderBy('logs_activites.date_action', 'DESC')
            ->paginate(30);

        return view('logs/index', [
            'logs'  => $logs,
            'pager' => $logModel->pager,
        ]);
    }
}
