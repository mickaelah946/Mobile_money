# Version 2 — Conception & répartition des tâches

> Décision actée : le « côté client » de la V2 (frais inclus, envoi
> multiple) est **intégré à l'interface opérateur existante** — pas de
> portail client séparé. Le caissier/agent effectue ces opérations pour
> le compte du client, comme pour un dépôt/retrait/transfert classique.

## 1. Ce que la V2 ajoute au modèle de données (prérequis commun)

Le point nouveau de la V2 est la notion de **transfert vers un autre
opérateur mobile money** (ex : un client Telma envoie de l'argent vers un
numéro Orange/Airtel). Le modèle actuel ne gère que des transferts entre
deux clients internes (les deux comptes existent dans notre base). Il
faut donc étendre légèrement le schéma **avant** de répartir le travail,
pour que Rary et Mika partent de la même base.

### Nouvelle table `prefixes_operateurs`

```sql
CREATE TABLE prefixes_operateurs (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe         TEXT NOT NULL UNIQUE,      -- ex: '032', '038'
    operateur_nom   TEXT NOT NULL,             -- ex: 'Orange Money'
    actif           INTEGER NOT NULL DEFAULT 1,
    date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

Un numéro dont le préfixe n'apparaît pas dans cette table est considéré
comme appartenant à **notre propre réseau**. Configurable, donc pas de
préfixe codé en dur dans le code.

### Nouveau type de transaction

```sql
INSERT INTO types_transaction (code, libelle, description) VALUES
('TRANSFERT_EXTERNE', 'Transfert vers un autre opérateur',
 'Transfert envoye vers un numero n''appartenant pas a notre reseau');
```

### Colonne ajoutée à `transactions`

```sql
ALTER TABLE transactions ADD COLUMN numero_destination_externe TEXT;
```

Utilisée uniquement quand `compte_destination_id IS NULL` et que le type
est `TRANSFERT_EXTERNE` (le bénéficiaire n'a pas de compte chez nous).

### Nouveau paramètre système

```sql
INSERT INTO parametres_systeme (cle, valeur, description) VALUES
('COMMISSION_INTEROPERATEUR_POURCENTAGE', '1.0',
 'Pourcentage de commission additionnelle appliqué en plus du tarif de transfert normal, pour les transferts vers un autre opérateur');
```

### Qui fait ce prérequis commun ?

Comme pour le socle V1, je m'en occupe maintenant (schéma + seed +
migration des `writable/database.db` existantes) avant que Rary et Mika
ne divergent, pour éviter tout conflit de schéma. Dites-moi si vous
voulez plutôt le faire vous-mêmes.

---

## 2. Répartition des fonctionnalités

### Mika — Réseau, Tarification & Administration (inchangé par rapport à la V1)

| # | Fonctionnalité | Détail |
|---|-----------------|--------|
| 1 | **Configuration des préfixes des autres opérateurs** | CRUD sur `prefixes_operateurs` : `PrefixeOperateurController` (index/create/store/edit/update/delete), vues `parametres/prefixes/*`. Accès réservé ADMIN/SUPER_ADMIN. |
| 2 | **Commission % inter-opérateur** | Simple édition du paramètre `COMMISSION_INTEROPERATEUR_POURCENTAGE` — réutilise l'écran `ParametreController` déjà existant (rien à créer, juste vérifier qu'il s'affiche bien). |
| 3 | **Rapport « Situation gain via les différents frais » séparé opérateur / autres opérateurs** | Étendre `RapportController` (déjà à Mika) : scinder la répartition des frais collectés en deux blocs — transferts internes vs `TRANSFERT_EXTERNE`, en s'appuyant sur `type_transaction.code`. |
| 4 | **Situation des montants à envoyer à chaque opérateur** | Nouveau rapport dans `RapportController` (ou nouveau `SettlementController` si plus lisible) : somme des `montant` (hors frais) des transactions `TRANSFERT_EXTERNE` réussies, groupée par préfixe/opérateur (jointure `numero_destination_externe` → `prefixes_operateurs`). C'est le montant que l'opérateur doit physiquement reverser à chaque concurrent pour compenser les transferts sortants. |

**Fichiers Mika :**
- `app/Controllers/Parametres/PrefixeOperateurController.php` *(nouveau)*
- `app/Models/PrefixeOperateurModel.php` *(nouveau — commun, voir §1)*
- `app/Views/parametres/prefixes/*.php` *(nouveau)*
- `app/Controllers/Rapports/RapportController.php` *(modifié)*
- `app/Views/rapports/index.php` *(modifié)*

### Rary — Comptes & Opérations

| # | Fonctionnalité | Détail |
|---|-----------------|--------|
| 1 | **Option « frais de retrait inclus lors de l'envoi »** | Sur le formulaire de transfert (`transactions/transfert.php`), case à cocher. Si cochée, le frais de *retrait* que le bénéficiaire paierait normalement est calculé à l'avance (`TarifService::calculerFrais` sur le type `RETRAIT`) et ajouté au montant débité à l'émetteur — le bénéficiaire reçoit alors un montant net sans frais à payer au retrait. Nécessite une petite extension de `TransactionService::executerTransfert()` (paramètre `bool $fraisRetraitInclus = false`). |
| 2 | **Envoi multiple vers plusieurs numéros** | Nouvelle vue `transactions/transfert_multiple.php` : un champ montant total + une liste dynamique de numéros bénéficiaires (JS simple pour ajouter/retirer des lignes), le montant est **divisé équitablement** entre les numéros saisis. Nouvelle méthode `TransactionService::executerTransfertMultiple(int $compteSourceId, array $numerosDestinataires, float $montantTotal, ...)` qui boucle sur `executerTransfert()`/`executerTransfertExterne()` pour chaque bénéficiaire, dans **une seule transaction SQL globale** (tout réussit ou tout échoue). |
| 3 | **Détection automatique interne / externe** | Quand un numéro de téléphone est saisi (transfert simple ou multiple) et ne correspond à aucun client existant, vérifier son préfixe contre `prefixes_operateurs` : s'il correspond à un opérateur externe → `TRANSFERT_EXTERNE` (nouvelle méthode `TransactionService::executerTransfertExterne()`) ; sinon → erreur « numéro inconnu dans notre réseau ». |

**Fichiers Rary :**
- `app/Controllers/Transactions/TransfertController.php` *(modifié — option frais inclus + détection externe)*
- `app/Controllers/Transactions/TransfertMultipleController.php` *(nouveau)*
- `app/Views/transactions/transfert.php` *(modifié)*
- `app/Views/transactions/transfert_multiple.php` *(nouveau)*
- `app/Services/TransactionService.php` *(modifié — commun, voir dépendances ci-dessous)*

---

## 3. Dépendances entre les deux

- `TransactionService` est un fichier **commun** modifié par Rary (nouvelle
  logique de transfert). Mika n'a pas besoin d'y toucher, mais doit
  connaître le nouveau type `TRANSFERT_EXTERNE` pour ses rapports.
- Rary a besoin de `PrefixeOperateurModel` (Mika) pour détecter si un
  numéro appartient à un autre opérateur — **le Model seul suffit**, pas
  besoin d'attendre que Mika ait fini son écran de configuration CRUD.
  Comme en V1, le Model fait partie du prérequis commun (§1), donc aucun
  blocage.
- Les rapports de Mika (§2 Mika, points 3 et 4) ont besoin que les
  transferts externes de Rary existent réellement pour être testés avec
  de vraies données — prévoir un test croisé une fois les deux parties
  terminées, comme en V1.

## 4. Peut être développé en parallèle ?

Oui, entièrement, une fois le prérequis commun (§1) posé : Rary et Mika
ne touchent à aucun fichier en commun (à l'exception de
`TransactionService.php`, modifié uniquement par Rary, et lu — pas
modifié — par Mika).
