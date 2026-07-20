<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// --- Routes publiques ---
$routes->get('/', 'Auth\AuthController::showLogin');
$routes->get('login', 'Auth\AuthController::showLogin');
$routes->post('login', 'Auth\AuthController::login');
$routes->get('logout', 'Auth\AuthController::logout');

// --- Routes protégées (filtre 'auth') ---
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {

    $routes->get('dashboard', 'DashboardController::index');

    // ================================================================
    // Développeur A — Comptes & Opérations
    // ================================================================
    $routes->group('clients', static function (RouteCollection $routes) {
        $routes->get('/', 'Clients\ClientController::index');
        $routes->get('create', 'Clients\ClientController::create');
        $routes->post('/', 'Clients\ClientController::store');
        $routes->get('(:num)', 'Clients\ClientController::show/$1');
        $routes->get('(:num)/edit', 'Clients\ClientController::edit/$1');
        $routes->post('(:num)', 'Clients\ClientController::update/$1');
        $routes->post('(:num)/statut', 'Clients\ClientController::changerStatut/$1');
    });

    $routes->group('comptes', static function (RouteCollection $routes) {
        $routes->get('/', 'Comptes\CompteController::index');
        $routes->get('(:num)', 'Comptes\CompteController::show/$1');
        $routes->post('(:num)/statut', 'Comptes\CompteController::changerStatut/$1');
    });

    $routes->group('transactions', static function (RouteCollection $routes) {
        $routes->get('/', 'Transactions\TransactionController::index');
        $routes->get('(:num)', 'Transactions\TransactionController::show/$1');
        $routes->get('depot', 'Transactions\DepotController::index');
        $routes->post('depot', 'Transactions\DepotController::store');
        $routes->get('retrait', 'Transactions\RetraitController::index');
        $routes->post('retrait', 'Transactions\RetraitController::store');
        $routes->get('transfert', 'Transactions\TransfertController::index');
        $routes->post('transfert', 'Transactions\TransfertController::store');
    });

    // ================================================================
    // Développeur B — Réseau, Tarification & Administration
    // ================================================================
    $routes->group('agents', static function (RouteCollection $routes) {
        $routes->get('/', 'Agents\AgentController::index');
        $routes->get('create', 'Agents\AgentController::create');
        $routes->post('/', 'Agents\AgentController::store');
        $routes->get('(:num)', 'Agents\AgentController::show/$1');
        $routes->get('(:num)/edit', 'Agents\AgentController::edit/$1');
        $routes->post('(:num)', 'Agents\AgentController::update/$1');
        $routes->post('(:num)/statut', 'Agents\AgentController::changerStatut/$1');
        $routes->get('(:num)/recharge', 'Agents\RechargeAgentController::show/$1');
        $routes->post('(:num)/recharge', 'Agents\RechargeAgentController::store/$1');
    });

    $routes->group('tarifs', static function (RouteCollection $routes) {
        $routes->get('/', 'Tarifs\GrilleTarifaireController::index');
        $routes->get('create', 'Tarifs\GrilleTarifaireController::create');
        $routes->post('/', 'Tarifs\GrilleTarifaireController::store');
        $routes->get('(:num)/edit', 'Tarifs\GrilleTarifaireController::edit/$1');
        $routes->post('(:num)', 'Tarifs\GrilleTarifaireController::update/$1');
        $routes->post('(:num)/statut', 'Tarifs\GrilleTarifaireController::changerStatut/$1');
    });

    $routes->group('parametres', ['filter' => 'role:ADMIN,SUPER_ADMIN'], static function (RouteCollection $routes) {
        $routes->get('/', 'Parametres\ParametreController::index');
        $routes->post('(:num)', 'Parametres\ParametreController::update/$1');
    });

    $routes->group('utilisateurs', ['filter' => 'role:SUPER_ADMIN'], static function (RouteCollection $routes) {
        $routes->get('/', 'Utilisateurs\UtilisateurController::index');
        $routes->get('create', 'Utilisateurs\UtilisateurController::create');
        $routes->post('/', 'Utilisateurs\UtilisateurController::store');
        $routes->get('(:num)/edit', 'Utilisateurs\UtilisateurController::edit/$1');
        $routes->post('(:num)', 'Utilisateurs\UtilisateurController::update/$1');
        $routes->post('(:num)/statut', 'Utilisateurs\UtilisateurController::changerStatut/$1');
    });

    $routes->get('rapports', 'Rapports\RapportController::index');

    $routes->get('logs', 'Logs\LogController::index');
});
