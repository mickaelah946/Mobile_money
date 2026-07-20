# Diagnostic de `main` et plan d'action

> État vérifié le 2026-07-20, branche `main` (à jour avec `dev_3`, qui vient
> d'être créée depuis `main` sans nouveau commit). Historique actuel :
> `Init-projet` → PR#1 `codeigniter_4` (dev_1) → PR#2 `Ajout des services
> métier et des filtres d'authentification` (dev_1) → PR#3 `commun` (dev_2).

## 1. Problème principal : CodeIgniter n'est pas réellement installé

Tout le code est actuellement dans un sous-dossier
`codeigniter4-framework-b3359be/` au lieu de la racine du dépôt. Ce
dossier est le dépôt brut `codeigniter4/framework` téléchargé en ZIP
(même symptôme que dans mon dossier de travail au tout début) :

- **Pas de `vendor/`** → Composer n'a jamais été exécuté. L'application
  ne peut donc **pas démarrer** en l'état (autoload manquant).
- Tout le framework (`system/`, ~400 fichiers) est committé directement
  dans le dépôt Git, ce qui n'est pas la bonne pratique (le framework
  doit venir de `vendor/` via Composer, pas être versionné à la main).
- Le nom du dossier (`-b3359be`, hash de commit GitHub) montre que c'est
  un "Download ZIP" et non une installation via `composer create-project`.

**Bonne nouvelle :** tout le code métier déjà écrit (Config, Models,
Services, Filters, Views) a été correctement recopié à l'intérieur de ce
dossier et correspond exactement à ce qu'on a construit. Rien de ce
travail n'est perdu, il faut juste le sortir de ce sous-dossier vers une
vraie installation Composer à la racine.

## 2. À SUPPRIMER

| Élément | Raison |
|---|---|
| `codeigniter4-framework-b3359be/` (tout le dossier, après en avoir extrait le code utile) | Framework brut mal installé, doit être remplacé par une vraie installation Composer |
| `app/Controllers/Home.php` | Contrôleur par défaut de CodeIgniter, inutilisé (nos routes utilisent `AuthController`) |
| `app/Views/welcome_message.php` | Vue par défaut de CodeIgniter, inutilisée |
| `app/Helpers/auth_helper copy.php` | Copie accidentelle en double de `auth_helper.php` |

## 3. À CRÉER / CORRIGER

| Élément | Action |
|---|---|
| Installation CodeIgniter 4 | `composer create-project codeigniter4/appstarter .` à la racine du dépôt |
| `app/Helpers/format_helper.php` | Manquant (seul `auth_helper.php` a été recopié) — à recréer |
| `.gitignore` | Absent du dépôt — à ajouter (`vendor/`, `.env`, `writable/logs/*`, `writable/cache/*`, `writable/session/*`, `writable/debugbar/*`, `writable/database.db`) |
| `Taches.md` | Vide — à remplir avec le journal de développement (modèle déjà prêt dans mon dossier de travail) |
| `.env` | À créer à partir de `env`, configuré en SQLite3 (comme dans mon dossier de travail) |
| `writable/database.db` | À régénérer depuis `base.sql` (déjà correct à la racine du dépôt) |

## 4. Fichiers à RAPATRIER depuis `codeigniter4-framework-b3359be/` vers la racine (une fois Composer réinstallé)

- `app/Config/Database.php`, `Filters.php`, `Routes.php` (déjà corrects, juste à déplacer)
- `app/Filters/AuthFilter.php`, `RoleFilter.php`
- `app/Models/*` (12 fichiers, déjà complets)
- `app/Services/*` (5 fichiers, déjà complets)
- `app/Libraries/ReferenceGenerator.php`
- `app/Helpers/auth_helper.php`
- `app/Controllers/BaseController.php`, `Auth/AuthController.php`, `DashboardController.php`
- `app/Views/layouts/`, `partials/`, `auth/`, `dashboard/`
- `public/assets/css/app.css`, `public/assets/js/app.js`

## 5. Dossiers qu'on va toucher ensuite pour les tâches de Rary (Comptes & Opérations)

Une fois le nettoyage ci-dessus fait, le prochain travail se fera
uniquement dans ces emplacements (aucun impact sur le travail de Mika) :

- `app/Controllers/Clients/ClientController.php` *(nouveau)*
- `app/Controllers/Comptes/CompteController.php` *(nouveau)*
- `app/Controllers/Transactions/DepotController.php`, `RetraitController.php`, `TransfertController.php`, `TransactionController.php` *(nouveaux)*
- `app/Views/clients/`, `app/Views/comptes/`, `app/Views/transactions/` *(nouveaux dossiers)*
- `app/Config/Routes.php` (déjà scaffoldé, section "Rary" à activer — pas de conflit avec la section "Mika")
- Lecture seule sur : `ClientModel`, `CompteModel`, `TransactionModel`, `MouvementModel`, `NotificationModel`, `AgentModel`, `GrilleTarifaireModel`, et les Services (`TransactionService`, `CompteService`, `TarifService`, `NotificationService`, `AuditService`) — déjà en place, aucune modification nécessaire

## 6. Ordre d'exécution proposé

1. Sauvegarder/vérifier qu'il n'y a rien d'autre d'important dans `codeigniter4-framework-b3359be/` en dehors de ce qui est listé en §4.
2. Réinstaller CodeIgniter 4 proprement à la racine (Composer).
3. Rapatrier les fichiers du §4.
4. Supprimer les éléments du §2.
5. Ajouter les éléments manquants du §3.
6. Vérifier que l'application démarre (`php spark serve`) et que le login fonctionne.
7. Commit sur `dev_3` (branche déjà créée, propre) avec un message clair, puis PR vers `main`.
8. Démarrer les Controllers/Views de Rary (§5).
