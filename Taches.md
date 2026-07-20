# Taches.md — Suivi des travaux par étudiant et par livraison

Projet : Mobile Money — Simulation d'un opérateur de mobile money
Binôme : Hanitra-R (Rary) & mickaelah946 (Mika)

---

## Version 1 (v1)

### Socle commun (réalisé ensemble avant la répartition Rary / Mika)

- Mise en place des documents de conception : `README.md`, `ARCHITECTURE.md`,
  `REPARTITION_TACHES.md`, `PLAN_DEVELOPPEMENT.md`, `commun.md`
- Installation du framework CodeIgniter 4
- Configuration de la base SQLite (`app/Config/Database.php`, `.env`)
- Écriture de `base.sql` : schéma complet (tables, contraintes, vues,
  données initiales) + correction du bug sur `grille_tarifaire` et
  régénération du hash du mot de passe admin
- Création des Models communs (Role, Utilisateur, Client, Agent, Compte,
  TypeTransaction, GrilleTarifaire, Transaction, Mouvement,
  ParametreSysteme, LogActivite, Notification)
- Mise en place de l'authentification (`AuthController`, vue `login.php`)
  et de la coquille du tableau de bord (`DashboardController`,
  `dashboard/index.php` + partials `_widgets_operations.php` /
  `_widgets_reseau.php`)
- Layout principal et composants réutilisables (navbar, sidebar, footer,
  messages flash, pagination)

### Hanitra-R (Rary_004378)

- Finalisation et intégration du socle commun (PR #3 "commun")
- *(à compléter au fur et à mesure de la livraison v1 : module Clients,
  Comptes, Transactions dépôt/retrait/transfert — cf.
  REPARTITION_TACHES.md)*

### mickaelah946 (Mika_003932)

- Services métier (`CompteService`, `TarifService`, `TransactionService`,
  `NotificationService`, `AuditService`) et Library `ReferenceGenerator`
- Filtres d'authentification et de rôle (`AuthFilter`, `RoleFilter`)
- Helpers (`auth_helper`, `format_helper`) (PR #2 "Ajout des services
  métier et des filtres d'authentification")
- *(à compléter au fur et à mesure de la livraison v1 : module Agents,
  Grille tarifaire, Paramètres, Utilisateurs, Rapports, Logs — cf.
  REPARTITION_TACHES.md)*

---

## Version 2 (v2)

*(à compléter lors de la prochaine livraison)*

---

## Version 3 (v3)

*(à compléter lors de la prochaine livraison)*