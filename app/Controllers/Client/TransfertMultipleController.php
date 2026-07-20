<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\CompteModel;
use App\Models\PrefixeOperateurModel;
use App\Services\NotificationService;
use App\Services\TransactionService;
use Throwable;

class TransfertMultipleController extends BaseController
{
    public function index()
    {
        $compte = (new CompteModel())->findByClientId((int) $this->session->get('clientId'));

        return view('client/transactions/transfert_multiple', ['compte' => $compte]);
    }

    public function store()
    {
        $rules = ['montant_total' => 'required|decimal|greater_than[0]'];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $numeros = array_values(array_filter(array_map('trim', (array) $this->request->getPost('numeros'))));
        $numeros = array_values(array_unique($numeros));

        if (count($numeros) < 2) {
            return redirect()->back()->withInput()->with('error', 'Saisissez au moins deux numéros bénéficiaires distincts pour un envoi multiple.');
        }

        $clientModel  = new ClientModel();
        $compteModel  = new CompteModel();
        $prefixeModel = new PrefixeOperateurModel();

        $clientId     = (int) $this->session->get('clientId');
        $clientSource = $clientModel->find($clientId);
        $compteSource = $clientSource ? $compteModel->findByClientId($clientId) : null;

        if (! $compteSource) {
            return redirect()->back()->withInput()->with('error', 'Votre compte est introuvable. Contactez votre agence.');
        }

        // Résolution des bénéficiaires : uniquement des numéros de notre opérateur (même réseau).
        $destinataires = [];
        foreach ($numeros as $numero) {
            if ($numero === $clientSource['telephone']) {
                return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas être votre propre bénéficiaire.');
            }

            if (! $prefixeModel->estInterne($numero)) {
                return redirect()->back()->withInput()->with('error', "Le numéro {$numero} appartient à un autre opérateur. L'envoi multiple est réservé aux transferts vers le même opérateur.");
            }

            $client = $clientModel->where('telephone', $numero)->first();
            if (! $client) {
                return redirect()->back()->withInput()->with('error', "Le numéro {$numero} ne correspond à aucun client de notre réseau.");
            }

            $compte = $compteModel->findByClientId($client['id']);
            if (! $compte) {
                return redirect()->back()->withInput()->with('error', "Le client {$numero} n'a pas de compte associé.");
            }

            $destinataires[] = ['client' => $client, 'compte' => $compte];
        }

        $montantTotal = (float) $this->request->getPost('montant_total');
        $nombre       = count($destinataires);
        $part         = floor(($montantTotal / $nombre) * 100) / 100;
        $reste        = round($montantTotal - ($part * $nombre), 2);
        $description  = $this->request->getPost('description');

        $db = \Config\Database::connect();
        $db->transStart();

        $transactionService  = new TransactionService();
        $notificationService = new NotificationService();

        try {
            foreach ($destinataires as $index => $destinataire) {
                $montantPart = $part + ($index === $nombre - 1 ? $reste : 0);

                $transaction = $transactionService->executerTransfert(
                    $compteSource['id'],
                    $destinataire['compte']['id'],
                    $montantPart,
                    null,
                    $description ?: 'Envoi multiple (' . $nombre . ' bénéficiaires)'
                );

                $notificationService->envoyer([
                    'client_id'      => $destinataire['client']['id'],
                    'transaction_id' => $transaction['id'],
                    'message'        => 'Vous avez reçu ' . formatMontant($montantPart) . ' via un envoi multiple depuis ' . $compteSource['numero_compte'] . '.',
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException("Une erreur est survenue pendant l'envoi multiple, aucune transaction n'a été appliquée.");
            }
        } catch (Throwable $e) {
            $db->transRollback();

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/client/dashboard')->with('success', $nombre . ' transferts effectués avec succès (montant réparti automatiquement).');
    }
}
