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

| Module / Fonctionnalité                          | Responsable | Statut       |
|---------------------------------------------------|-------------|--------------|
| Socle commun (architecture, BDD, layout, auth)     | Rary + Mika | ✅ Terminé   |
| Gestion des clients                                | Rary | ⏳ À faire |
| Gestion des comptes clients                        | Rary | ⏳ À faire |
| Opérations (dépôt, retrait, transfert)             | Rary | ⏳ À faire |
| Notifications (simulation SMS)                     | Rary | ⏳ À faire |
| Gestion des agents                                  | Mika | ⏳ À faire |
| Grille tarifaire                                    | Mika | ⏳ À faire |
| Paramètres système                                  | Mika | ⏳ À faire |
| Utilisateurs internes & rôles                       | Mika | ⏳ À faire |
| Rapports & tableau de bord                          | Mika | ⏳ À faire |
| Journal d'audit (logs)                              | Mika | ⏳ À faire |

_Légende : ⏳ À faire · 🔄 En cours · ✅ Terminé · 🐞 Bug détecté_

---

## 2. Suivi des livraisons

| Livraison | Date prévue | Date réelle | Contenu principal                                   | Statut |
|-----------|-------------|-------------|-------------------------------------------------------|--------|
| L0 — Socle commun | 2026-07-22 | 2026-07-20 | Architecture, base.sql, Models/Services/Filters communs, layout, installation CI4 propre | ✅ Terminé |
| L1 — Auth + Dashboard | _(à définir)_ | 2026-07-20 | Authentification, tableau de bord (coquille) | ✅ Terminé |
| L2 — Fonctionnalités métier v1 | _(à définir)_ | | Clients/Comptes/Transactions (Rary) + Agents/Tarifs (Mika) | ⏳ |
| L3 — Fonctionnalités métier v2 | _(à définir)_ | | Paramètres, Utilisateurs, Rapports, Logs (Mika) | ⏳ |
| L4 — Tests & fusion finale | _(à définir)_ | | Tests croisés, fusion vers `main` | ⏳ |
| L5 — Livraison finale | _(à définir)_ | | Démo / soutenance | ⏳ |

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
| — | Aucun bug répertorié pour l'instant | — | — | — | — |

---

## 6. Historique des versions Git

| Branche/PR | Description |
|-------------|--------------|
| `dev_1` → PR#1 | `codeigniter_4` — première installation (à remplacer) |
| `dev_1` → PR#2 | Ajout des Services métier et des Filters d'authentification |
| `dev_2` → PR#3 | `commun` — base.sql |
| `dev_3` (en cours) | Nettoyage installation CI4 + `Taches.md` rempli |
