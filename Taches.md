# Journal de développement — Mobile Money (Côté Opérateur)

> Ce fichier est le journal de bord officiel du projet. Il doit être mis à
> jour **à chaque session de travail significative** et **à chaque
> livraison**. Ne pas réécrire l'historique : on ajoute, on ne supprime pas
> (sauf erreur de saisie).

## Informations du projet

| Champ                | Valeur                                              |
|-----------------------|-----------------------------------------------------|
| Nom du projet          | Simulation Mobile Money — Module Opérateur          |
| Framework              | PHP CodeIgniter 4                                   |
| Base de données        | SQLite 3                                            |
| Équipe                 | Rary (Comptes & Opérations) · Mika (Réseau & Administration) |
| Dépôt Git               | github.com/mickaelah946/Mobile_money                |
| Date de démarrage       | 2026-07-20                                           |

---

## 1. Répartition générale des tâches

### Version 1 (côté opérateur)

| Module / Fonctionnalité                          | Responsable | Statut       |
|---------------------------------------------------|-------------|--------------|
| Socle commun (architecture, BDD, layout, auth)     | Rary + Mika | ✅ Terminé   |
| Gestion des clients                                | Rary | ✅ Terminé |
| Gestion des comptes clients                        | Rary | ✅ Terminé |
| Opérations (dépôt, retrait, transfert)             | Rary | ✅ Terminé |
| Notifications (simulation SMS)                     | Rary | ✅ Terminé |
| Gestion des agents                                  | Mika | ✅ Terminé |
| Grille tarifaire                                    | Mika | ✅ Terminé |
| Paramètres système                                  | Mika | ✅ Terminé |
| Utilisateurs internes & rôles                       | Mika | ✅ Terminé |
| Rapports & tableau de bord                          | Mika | ✅ Terminé |
| Journal d'audit (logs)                              | Mika | ✅ Terminé |

### Version 2 (préfixes opérateurs, commissions, envoi multiple, espace client)

| Module / Fonctionnalité                                          | Responsable | Statut       |
|---------------------------------------------------------------------|-------------|--------------|
| Prérequis commun V2 (schéma, `PrefixeOperateurModel`, param. commission) | Rary (avec l'IA) | ✅ Terminé |
| Configuration des préfixes des autres opérateurs (CRUD)              | Mika | ✅ Terminé |
| Commission % inter-opérateur                                          | Mika | ✅ Terminé |
| Rapport "gains via frais" séparé opérateur / autres opérateurs        | Mika | ✅ Terminé |
| Rapport "montants à envoyer à chaque opérateur"                        | Mika | ✅ Terminé |
| Option "frais de retrait inclus" à l'envoi                             | Rary | ✅ Terminé |
| Envoi multiple vers plusieurs numéros (même opérateur)                  | Rary | ✅ Terminé |
| Règle liste blanche (`PREFIXE_NOTRE_OPERATEUR` = seul préfixe interne)  | Rary (avec l'IA) | ✅ Terminé |
| Espace client séparé (login par numéro, solde, historique, transfert, envoi multiple) | Rary (avec l'IA) | ✅ Terminé |

_Légende : ⏳ À faire · 🔄 En cours · ✅ Terminé · 🐞 Bug détecté_

---

## 2. Suivi des livraisons

| Livraison | Date prévue | Date réelle | Contenu principal                                   | Statut |
|-----------|-------------|-------------|-------------------------------------------------------|--------|
| L0 — Socle commun | 2026-07-22 | 2026-07-20 | Architecture, base.sql, Models/Services/Filters communs, layout, installation CI4 propre | ✅ Terminé |
| L1 — Auth + Dashboard | _(à définir)_ | 2026-07-20 | Authentification, tableau de bord (coquille) | ✅ Terminé |
| L2 — Fonctionnalités métier v1 | _(à définir)_ | 2026-07-20 | Clients/Comptes/Transactions (Rary) + Agents/Tarifs (Mika) | ✅ Terminé |
| L3 — Fonctionnalités métier v1 (suite) | _(à définir)_ | 2026-07-20 | Paramètres, Utilisateurs, Rapports, Logs (Mika) | ✅ Terminé |
| L4 — Version 2 (préfixes, commissions, envoi multiple) | _(à définir)_ | 2026-07-20 | Préfixes opérateurs, commission inter-opérateur, frais inclus, envoi multiple — fusionné dans `main` (PR#9) | ✅ Terminé |
| L5 — Version 2 (ajustements + espace client) | _(à définir)_ | 2026-07-20 | Règle liste blanche du préfixe interne, rapports séparés opérateur/autres, espace client autonome | ✅ Terminé (en attente de push) |
| L6 — Tests & fusion finale | _(à définir)_ | | Tests croisés complets, fusion `dev_v2` → `main` | ⏳ |
| L7 — Livraison finale | _(à définir)_ | | Démo / soutenance | ⏳ |

---

## 3. Journal détaillé

> Une entrée par session de travail. Format : date, auteur, ce qui a été
> fait, fichiers touchés, points bloquants éventuels.

### 2026-07-20 (matin) — Init projet + installation CodeIgniter 4

**Auteur(s) :** Mika

**Travaux effectués :**
- Initialisation du dépôt (`Init-projet`).
- Installation de CodeIgniter 4 (`codeigniter_4`) — téléchargée en ZIP
  depuis GitHub, pas encore via Composer.
- Ajout des Services métier et des Filters d'authentification
  (`AuthFilter`, `RoleFilter`, `CompteService`, `TransactionService`,
  `TarifService`, `NotificationService`, `AuditService`).

**Fichiers ajoutés/modifiés :** tout le contenu initial du dossier
`codeigniter4-framework-b3359be/`.

**Points bloquants (découverts plus tard) :** l'installation n'était pas
une vraie installation Composer (pas de `vendor/`), voir entrée du soir.

---

### 2026-07-20 (après-midi) — Socle commun (architecture, base.sql, journal)

**Auteur(s) :** Rary (avec l'assistant IA)

**Travaux effectués :**
- Définition de l'architecture complète du projet CodeIgniter 4.
- Conception du modèle relationnel (12 tables) et rédaction de `base.sql`
  (schéma + contraintes + vues + données initiales).
- Définition des conventions de nommage (PHP, base de données, routes, vues).
- Définition de l'organisation Git (branches, commits, stratégie de fusion).
- Répartition des tâches entre les deux développeurs (Rary / Mika).

**Fichiers ajoutés/modifiés :**
- `base.sql`
- `Taches.md`

**Points bloquants :** Aucun.

---

### 2026-07-20 (soir) — Nettoyage de l'installation CodeIgniter 4

**Auteur(s) :** Rary (avec l'assistant IA)

**Travaux effectués :**
- Diagnostic de `main` : le dossier `codeigniter4-framework-b3359be/`
  était le dépôt brut du framework (téléchargé en ZIP), sans `vendor/`
  → l'application ne pouvait pas démarrer.
- Réinstallation propre de CodeIgniter 4 via
  `composer create-project codeigniter4/appstarter` à la racine du dépôt.
- Rapatriement de tout le code déjà écrit (Config, Models, Services,
  Filters, Libraries, Helpers, Controllers, Views) vers la nouvelle
  installation.
- Suppression du dossier `codeigniter4-framework-b3359be/`, de
  `Home.php`, `welcome_message.php` et `auth_helper copy.php` (doublon).
- Ajout de `format_helper.php` (manquant), de `.env` (config SQLite3),
  régénération de `writable/database.db` depuis `base.sql`.
- Remplissage du présent journal (`Taches.md` était vide).

**Fonctionnalités développées / terminées :**
- Authentification (login/logout/session) fonctionnelle de bout en bout.
- Tableau de bord (coquille avec widgets à compléter par Rary et Mika).

**Fichiers ajoutés/modifiés :** voir `A_FAIRE.md` (diagnostic complet)
pour la liste détaillée.

**Points bloquants :** Aucun — application testée et fonctionnelle
(`php spark serve`, login avec `0340000000` / `Admin@2026`).

**Prochaine étape :** Développement des Controllers/Views de Rary
(Clients, Comptes, Transactions).

---

### 2026-07-20 — Fonctionnalités métier V1 complètes (Rary + Mika)

**Auteur(s) :** Rary et Mika (avec l'assistant IA)

**Travaux effectués :**
- Rary : `ClientController`, `CompteController`, `TransactionController`,
  `DepotController`, `RetraitController`, `TransfertController` (V1, transfert
  simple entre deux clients internes) + toutes les vues associées. Chaque
  client créé obtient automatiquement un compte (portefeuille) lié.
- Mika : `AgentController` + `RechargeAgentController` (avec compte flotte
  auto-créé), `GrilleTarifaireController`, `ParametreController`,
  `UtilisateurController`, `RapportController` (v1 : agents actifs, flotte
  totale, alertes seuil, répartition des transactions), `LogController`.
  Widgets du tableau de bord (Rary et Mika) branchés sur les vraies données.
- Fusion du travail des deux (branches `dev_3`/`dev_4`/`dev`) après un
  incident de synchronisation (travail resté sur une branche non fusionnée)
  et nettoyage d'un marqueur de conflit Git oublié dans ce fichier.
- Nettoyage de routes dupliquées/mal placées.

**Fonctionnalités développées / terminées :** Ensemble du périmètre V1
côté opérateur (Clients, Comptes, Transactions, Agents, Tarifs, Paramètres,
Utilisateurs, Rapports, Logs).

**Fichiers ajoutés/modifiés :** voir commits `comptes` (dev_3) et
`ajout des modules tarifs,agents,parametre et admin` (dev_4).

**Points bloquants :** Aucun — testé de bout en bout (création client,
dépôt, retrait, transfert, recharge agent, comptabilité en partie double
vérifiée manuellement à chaque étape).

**Prochaine étape :** Cahier des charges Version 2.

---

### 2026-07-20 — Version 2 : conception, prérequis commun et implémentation initiale

**Auteur(s) :** Rary et Mika (avec l'assistant IA)

**Travaux effectués :**
- Conception de la V2 (`REPARTITION_V2.md`) : préfixes des autres
  opérateurs, commission inter-opérateur, rapports séparés, envoi multiple,
  frais de retrait inclus.
- Prérequis commun : nouvelle table `prefixes_operateurs`, nouveau type de
  transaction `TRANSFERT_EXTERNE`, colonne `transactions.numero_destination_externe`,
  paramètre `COMMISSION_INTEROPERATEUR_POURCENTAGE`, `PrefixeOperateurModel`.
- Rary : option "frais de retrait inclus" (`TransactionService::executerTransfert`),
  transfert vers un autre opérateur (`executerTransfertExterne`), envoi
  multiple (`TransfertMultipleController`, montant réparti, réservé au même
  opérateur, transaction SQL globale tout-ou-rien).
- Mika : `PrefixeOperateurController` (CRUD des préfixes nommés).
- **Bug corrigé :** la commission inter-opérateur remplaçait le tarif de
  transfert normal au lieu de s'y ajouter (grille tarifaire absente pour
  `TRANSFERT_EXTERNE`) — corrigé pour cumuler tarif normal + commission.
- Fusionné dans `main` via PR#9 (`dev_v2`).

**Fichiers ajoutés/modifiés :** `TransactionService.php`, `TarifService.php`,
`TransfertController.php`, `TransfertMultipleController.php`,
`PrefixeOperateurModel.php`, `PrefixeOperateurController.php`, `base.sql`,
vues `transactions/transfert*.php`, `parametres/prefixes/*.php`.

**Points bloquants :** Aucun — chaque scénario retesté avec calcul manuel
des montants (dépôt, retrait, transfert interne, transfert externe, envoi
multiple).

**Prochaine étape :** Ajustement de la règle interne/externe + rapports
manquants + espace client.

---

### 2026-07-20 — Version 2 (suite) : règle liste blanche, rapports manquants, espace client séparé

**Auteur(s) :** Rary (avec l'assistant IA)

**Travaux effectués :**
- Changement de règle interne/externe : passage d'une liste noire
  (préfixes externes nommés + existence d'un client) à une **liste
  blanche** — un seul paramètre `PREFIXE_NOTRE_OPERATEUR` (exemple `031`)
  détermine ce qui est interne, tout le reste est automatiquement externe,
  même si le préfixe exact n'est pas nommé dans `prefixes_operateurs`.
  Nouvelle méthode `PrefixeOperateurModel::estInterne()`. Validation
  ajoutée à la création/modification d'un client (le téléphone doit
  appartenir à notre préfixe).
- Ajout des deux rapports manquants de Mika dans `RapportController` :
  gains via frais séparés "notre opérateur" / "autres opérateurs", et
  "situation des montants à envoyer à chaque opérateur" (groupé par
  opérateur, à partir de `numero_destination_externe`).
- **Bug corrigé :** `numero_destination_externe` manquait dans
  `$allowedFields` de `TransactionModel` → jamais réellement sauvegardé en
  base malgré le code déjà écrit ; tous les transferts externes se
  retrouvaient fusionnés dans un seul groupe vide sur le rapport de
  règlement. Corrigé.
- Nouvel **espace client séparé** (changement de portée validé avec
  l'équipe) : authentification par numéro de téléphone uniquement (pas de
  mot de passe, simulation), session distincte de l'espace opérateur
  (`ClientAuthFilter`, `clientauth`), layout dédié. Le client peut
  consulter son solde et son historique, et effectuer lui-même un
  transfert (avec frais inclus / détection externe) ou un envoi multiple.
  Liens croisés entre les deux pages de connexion.

**Fonctionnalités développées / terminées :**
- `App\Controllers\Client\*` : `ClientAuthController`, `DashboardController`,
  `TransactionController`, `TransfertController`, `TransfertMultipleController`.
- Vues `client/auth/login.php`, `client/layouts/main.php`,
  `client/dashboard/index.php`, `client/transactions/*.php`.

**Fichiers ajoutés/modifiés :** `base.sql`, `PrefixeOperateurModel.php`,
`TransactionModel.php`, `ClientController.php`, `TransfertController.php`,
`TransfertMultipleController.php`, `RapportController.php`,
`rapports/index.php`, `Config/Routes.php`, `Config/Filters.php`,
`auth_helper.php`, dossier complet `app/Controllers/Client/` et
`app/Views/client/` (nouveaux).

**Points bloquants :** Aucun — testé de bout en bout dans une session
navigateur séparée de l'opérateur (connexion client par numéro, protection
des routes des deux espaces vérifiée indépendamment, transfert avec frais
inclus, envoi multiple, transfert externe : tous les soldes vérifiés
exacts par calcul manuel).

**Prochaine étape :** Push de `dev_v2` vers origin (en attente de
confirmation), puis tests croisés finaux avant fusion vers `main`.

---

### _(modèle à copier pour chaque nouvelle entrée)_

### AAAA-MM-JJ — Titre court de la session

**Auteur(s) :**

**Travaux effectués :**
-

**Fonctionnalités développées / terminées :**
-

**Fichiers ajoutés/modifiés :**
-

**Points bloquants :**
-

**Prochaine étape :**
-

---

## 4. Backlog (à faire / idées non planifiées)

| # | Description | Priorité | Proposé par |
|---|--------------|----------|-------------|
| 1 | _(exemple)_ Export PDF des relevés de transaction | Basse | |

---

## 5. Bugs connus

| # | Description | Module | Sévérité | Statut | Corrigé le |
|---|--------------|--------|----------|--------|------------|
| 1 | INSERT `grille_tarifaire` : 6 colonnes déclarées, 5 valeurs fournies (colonne `actif` en trop dans la liste) | `base.sql` | Moyenne | ✅ Corrigé | 2026-07-20 |
| 2 | Hash bcrypt du mot de passe admin écrit à la main, invalide | `base.sql` | Haute | ✅ Corrigé | 2026-07-20 |
| 3 | Recharge de flotte agent débitait le compte SYSTEME (frais collectés) au lieu d'un apport de trésorerie externe | `TransactionService` | Haute | ✅ Corrigé | 2026-07-20 |
| 4 | Route `/parametres/prefixes` mal montée à la racine `/prefixes` → 404 | `Config/Routes.php` | Moyenne | ✅ Corrigé | 2026-07-20 |
| 5 | Commission inter-opérateur remplaçait le tarif de transfert normal au lieu de s'y ajouter | `TransactionService::executerTransfertExterne` | Moyenne | ✅ Corrigé | 2026-07-20 |
| 6 | `numero_destination_externe` absent de `$allowedFields` → jamais sauvegardé, transferts externes fusionnés dans le rapport de règlement | `TransactionModel` | Haute | ✅ Corrigé | 2026-07-20 |
| 7 | Marqueur de conflit Git (`>>>>>>> dev`) oublié dans `Taches.md` après une fusion | `Taches.md` | Basse | ✅ Corrigé | 2026-07-20 |

---

## 6. Historique des versions Git

| Branche/PR | Description |
|-------------|--------------|
| `dev_1` → PR#1 | `codeigniter_4` — première installation (à remplacer) |
| `dev_1` → PR#2 | Ajout des Services métier et des Filters d'authentification |
| `dev_2` → PR#3 | `commun` — base.sql |
| `dev_3` → PR#4/#6 | Nettoyage installation CI4 + `Taches.md` rempli + module Clients/Comptes/Transactions (Rary) |
| `dev_4` | `ajout des modules tarifs,agents,parametre et admin` (Mika) — fusion de `dev`/`dev_3`/`dev_4` |
| `dev_6` → PR#7/#8 | `repartition_taches V2`, `Prérequis commun V2`, `realisation_taches V2` (Rary) |
| `dev_5` | `V2 - Mika : CRUD préfixes des autres opérateurs` |
| `dev_v2` → PR#9 | Fusion `dev_6` + `dev_5`, correctif commission — **mergé dans `main`** |
| `dev_v2` (en cours, non poussé) | Règle liste blanche du préfixe interne, rapports séparés/règlement, espace client séparé |
