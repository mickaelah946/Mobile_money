PRAGMA foreign_keys = ON;

-- =====================================================================
-- 1. ROLES
--    Roles internes du personnel operateur (back-office).
-- =====================================================================
DROP TABLE IF EXISTS roles;
CREATE TABLE roles (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    code            TEXT NOT NULL UNIQUE,                 -- SUPER_ADMIN, ADMIN, CAISSIER, SUPERVISEUR
    libelle         TEXT NOT NULL,
    description     TEXT,
    date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================================
-- 2. UTILISATEURS
--    Personnel operateur (back-office) : administrateurs, caissiers, superviseurs.
-- =====================================================================
DROP TABLE IF EXISTS utilisateurs;
CREATE TABLE utilisateurs (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    matricule           TEXT NOT NULL UNIQUE,
    nom                 TEXT NOT NULL,
    prenom              TEXT NOT NULL,
    email               TEXT NOT NULL UNIQUE,
    telephone           TEXT NOT NULL UNIQUE,
    mot_de_passe        TEXT NOT NULL,                    -- hash password_hash()
    role_id             INTEGER NOT NULL,
    statut              TEXT NOT NULL DEFAULT 'ACTIF' CHECK (statut IN ('ACTIF','INACTIF','SUSPENDU')),
    derniere_connexion  DATETIME,
    date_creation       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_maj            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);
CREATE INDEX idx_utilisateurs_role ON utilisateurs(role_id);

-- =====================================================================
-- 3. CLIENTS
--    Clients finaux (abonnes Mobile Money) enregistres par l'operateur.
-- =====================================================================
DROP TABLE IF EXISTS clients;
CREATE TABLE clients (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_client    TEXT NOT NULL UNIQUE,                -- ex: CL-000001
    nom              TEXT NOT NULL,
    prenom           TEXT NOT NULL,
    telephone        TEXT NOT NULL UNIQUE,
    type_piece       TEXT CHECK (type_piece IN ('CNI','PASSEPORT','PERMIS')),
    numero_piece     TEXT UNIQUE,
    date_naissance   DATE,
    adresse          TEXT,
    statut           TEXT NOT NULL DEFAULT 'ACTIF' CHECK (statut IN ('ACTIF','SUSPENDU','BLOQUE')),
    cree_par         INTEGER,
    date_creation    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_maj         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cree_par) REFERENCES utilisateurs(id) ON DELETE SET NULL
);
CREATE INDEX idx_clients_statut ON clients(statut);

-- =====================================================================
-- 4. AGENTS
--    Points de service (agents distributeurs) effectuant depots/retraits.
-- =====================================================================
DROP TABLE IF EXISTS agents;
CREATE TABLE agents (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    code_agent       TEXT NOT NULL UNIQUE,                -- ex: AG-000001
    nom              TEXT NOT NULL,
    prenom           TEXT NOT NULL,
    telephone        TEXT NOT NULL UNIQUE,
    zone             TEXT,
    adresse          TEXT,
    statut           TEXT NOT NULL DEFAULT 'ACTIF' CHECK (statut IN ('ACTIF','SUSPENDU','BLOQUE')),
    cree_par         INTEGER,
    date_creation    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_maj         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cree_par) REFERENCES utilisateurs(id) ON DELETE SET NULL
);
CREATE INDEX idx_agents_statut ON agents(statut);
CREATE INDEX idx_agents_zone ON agents(zone);

-- =====================================================================
-- 5. COMPTES
--    Portefeuille (wallet) unique pour CLIENT, AGENT (flotte) ou SYSTEME
--    (compte operateur qui collecte les frais). Un compte appartient a
--    un seul type de proprietaire.
-- =====================================================================
DROP TABLE IF EXISTS comptes;
CREATE TABLE comptes (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_compte    TEXT NOT NULL UNIQUE,                -- ex: CPT-0000001
    type_compte      TEXT NOT NULL CHECK (type_compte IN ('CLIENT','AGENT','SYSTEME')),
    client_id        INTEGER UNIQUE,
    agent_id         INTEGER UNIQUE,
    solde            REAL NOT NULL DEFAULT 0 CHECK (solde >= 0),
    plafond          REAL NOT NULL DEFAULT 0,
    statut           TEXT NOT NULL DEFAULT 'ACTIF' CHECK (statut IN ('ACTIF','SUSPENDU','BLOQUE')),
    date_creation    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_maj         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE CASCADE,
    CHECK (
        (type_compte = 'CLIENT'  AND client_id IS NOT NULL AND agent_id IS NULL) OR
        (type_compte = 'AGENT'   AND agent_id  IS NOT NULL AND client_id IS NULL) OR
        (type_compte = 'SYSTEME' AND client_id IS NULL AND agent_id IS NULL)
    )
);
CREATE INDEX idx_comptes_type ON comptes(type_compte);

-- =====================================================================
-- 6. TYPES_TRANSACTION
--    Referentiel des natures d'operations possibles.
-- =====================================================================
DROP TABLE IF EXISTS types_transaction;
CREATE TABLE types_transaction (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    code           TEXT NOT NULL UNIQUE,                  -- DEPOT, RETRAIT, TRANSFERT, PAIEMENT, RECHARGE_AGENT
    libelle        TEXT NOT NULL,
    description    TEXT
);

-- =====================================================================
-- 7. GRILLE_TARIFAIRE
--    Bareme des frais applicable par type d'operation et tranche de montant.
-- =====================================================================
DROP TABLE IF EXISTS grille_tarifaire;
CREATE TABLE grille_tarifaire (
    id                     INTEGER PRIMARY KEY AUTOINCREMENT,
    type_transaction_id    INTEGER NOT NULL,
    montant_min            REAL NOT NULL,
    montant_max            REAL NOT NULL,
    frais_fixe             REAL NOT NULL DEFAULT 0,
    frais_pourcentage      REAL NOT NULL DEFAULT 0,       -- ex: 1.5 = 1.5 %
    actif                  INTEGER NOT NULL DEFAULT 1,     -- 0/1
    date_debut             DATE NOT NULL DEFAULT (date('now')),
    date_fin               DATE,
    date_creation          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_transaction_id) REFERENCES types_transaction(id) ON DELETE RESTRICT,
    CHECK (montant_max >= montant_min)
);
CREATE INDEX idx_tarifs_type ON grille_tarifaire(type_transaction_id);

-- =====================================================================
-- 8. TRANSACTIONS
--    Operation financiere entre deux comptes (ou un seul, selon le type).
-- =====================================================================
DROP TABLE IF EXISTS transactions;
CREATE TABLE transactions (
    id                          INTEGER PRIMARY KEY AUTOINCREMENT,
    reference                   TEXT NOT NULL UNIQUE,     -- ex: TXN-20260720-000001
    type_transaction_id         INTEGER NOT NULL,
    compte_source_id            INTEGER,
    compte_destination_id       INTEGER,
    montant                     REAL NOT NULL CHECK (montant > 0),
    frais                       REAL NOT NULL DEFAULT 0,
    montant_total                REAL NOT NULL,
    statut                       TEXT NOT NULL DEFAULT 'EN_ATTENTE' CHECK (statut IN ('EN_ATTENTE','REUSSIE','ECHOUEE','ANNULEE')),
    motif_echec                  TEXT,
    initiateur_utilisateur_id    INTEGER,
    initiateur_agent_id          INTEGER,
    description                  TEXT,
    numero_destination_externe   TEXT,       -- V2 : numero du beneficiaire quand il appartient a un autre operateur (compte_destination_id est alors NULL)
    date_transaction             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_traitement               DATETIME,
    FOREIGN KEY (type_transaction_id) REFERENCES types_transaction(id) ON DELETE RESTRICT,
    FOREIGN KEY (compte_source_id) REFERENCES comptes(id) ON DELETE RESTRICT,
    FOREIGN KEY (compte_destination_id) REFERENCES comptes(id) ON DELETE RESTRICT,
    FOREIGN KEY (initiateur_utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    FOREIGN KEY (initiateur_agent_id) REFERENCES agents(id) ON DELETE SET NULL
);
CREATE INDEX idx_transactions_statut ON transactions(statut);
CREATE INDEX idx_transactions_date ON transactions(date_transaction);
CREATE INDEX idx_transactions_source ON transactions(compte_source_id);
CREATE INDEX idx_transactions_destination ON transactions(compte_destination_id);

-- =====================================================================
-- 9. MOUVEMENTS
--    Ecritures comptables (partie double) generees par chaque transaction
--    reussie : une ligne DEBIT + une ligne CREDIT. Garantit la tracabilite
--    et permet de reconstituer l'historique du solde d'un compte.
-- =====================================================================
DROP TABLE IF EXISTS mouvements;
CREATE TABLE mouvements (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    transaction_id    INTEGER NOT NULL,
    compte_id         INTEGER NOT NULL,
    sens              TEXT NOT NULL CHECK (sens IN ('DEBIT','CREDIT')),
    montant           REAL NOT NULL CHECK (montant > 0),
    solde_avant       REAL NOT NULL,
    solde_apres       REAL NOT NULL,
    date_mouvement    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (compte_id) REFERENCES comptes(id) ON DELETE RESTRICT
);
CREATE INDEX idx_mouvements_compte ON mouvements(compte_id);
CREATE INDEX idx_mouvements_transaction ON mouvements(transaction_id);

-- =====================================================================
-- 10. PARAMETRES_SYSTEME
--     Configuration cle/valeur modifiable par l'administrateur (plafonds, etc).
-- =====================================================================
DROP TABLE IF EXISTS parametres_systeme;
CREATE TABLE parametres_systeme (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    cle            TEXT NOT NULL UNIQUE,
    valeur         TEXT NOT NULL,
    description    TEXT,
    date_maj       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================================
-- 11. LOGS_ACTIVITES
--     Journal d'audit des actions sensibles effectuees par le personnel.
-- =====================================================================
DROP TABLE IF EXISTS logs_activites;
CREATE TABLE logs_activites (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    utilisateur_id    INTEGER,
    action            TEXT NOT NULL,             -- ex: CREATION_CLIENT, BLOCAGE_COMPTE, MODIF_TARIF
    cible_table       TEXT,
    cible_id          INTEGER,
    details           TEXT,
    adresse_ip        TEXT,
    date_action       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);
CREATE INDEX idx_logs_utilisateur ON logs_activites(utilisateur_id);
CREATE INDEX idx_logs_date ON logs_activites(date_action);

-- =====================================================================
-- 12. NOTIFICATIONS
--     Simulation des notifications (SMS/Email) envoyees suite a une operation.
-- =====================================================================
DROP TABLE IF EXISTS notifications;
CREATE TABLE notifications (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id         INTEGER,
    agent_id          INTEGER,
    transaction_id    INTEGER,
    canal             TEXT NOT NULL DEFAULT 'SMS' CHECK (canal IN ('SMS','EMAIL')),
    message           TEXT NOT NULL,
    statut            TEXT NOT NULL DEFAULT 'ENVOYE' CHECK (statut IN ('ENVOYE','ECHEC')),
    date_envoi        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
);

-- =====================================================================
-- 13. PREFIXES_OPERATEURS (V2)
--     Prefixes telephoniques appartenant a d'autres operateurs mobile
--     money. Un numero dont le prefixe n'apparait pas ici est considere
--     comme appartenant a notre propre reseau.
-- =====================================================================
DROP TABLE IF EXISTS prefixes_operateurs;
CREATE TABLE prefixes_operateurs (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe         TEXT NOT NULL UNIQUE,      -- ex: '032', '038'
    operateur_nom   TEXT NOT NULL,             -- ex: 'Orange Money'
    actif           INTEGER NOT NULL DEFAULT 1,
    date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================================
-- VUES SQL
-- =====================================================================

-- Vue consolidee des comptes avec le nom du proprietaire
DROP VIEW IF EXISTS vue_comptes;
CREATE VIEW vue_comptes AS
SELECT
    c.id,
    c.numero_compte,
    c.type_compte,
    CASE
        WHEN c.type_compte = 'CLIENT'  THEN cl.nom || ' ' || cl.prenom
        WHEN c.type_compte = 'AGENT'   THEN ag.nom || ' ' || ag.prenom
        ELSE 'COMPTE SYSTEME'
    END AS proprietaire,
    CASE
        WHEN c.type_compte = 'CLIENT' THEN cl.telephone
        WHEN c.type_compte = 'AGENT'  THEN ag.telephone
        ELSE NULL
    END AS telephone,
    c.solde,
    c.plafond,
    c.statut,
    c.date_creation
FROM comptes c
LEFT JOIN clients cl ON cl.id = c.client_id
LEFT JOIN agents  ag ON ag.id = c.agent_id;

-- Vue des transactions recentes avec libelles lisibles
DROP VIEW IF EXISTS vue_transactions;
CREATE VIEW vue_transactions AS
SELECT
    t.id,
    t.reference,
    tt.code            AS type_operation,
    tt.libelle         AS type_libelle,
    cs.numero_compte   AS compte_source,
    cd.numero_compte   AS compte_destination,
    t.montant,
    t.frais,
    t.montant_total,
    t.statut,
    t.date_transaction
FROM transactions t
JOIN types_transaction tt ON tt.id = t.type_transaction_id
LEFT JOIN comptes cs ON cs.id = t.compte_source_id
LEFT JOIN comptes cd ON cd.id = t.compte_destination_id;

-- =====================================================================
-- DONNEES INITIALES (SEED)
-- =====================================================================

-- Roles de base
INSERT INTO roles (code, libelle, description) VALUES
('SUPER_ADMIN', 'Super administrateur', 'Acces complet a la plateforme operateur'),
('ADMIN',       'Administrateur',       'Gestion des clients, agents, tarifs, parametres'),
('CAISSIER',    'Caissier / Guichetier','Traitement des operations quotidiennes (depot, retrait, transfert)'),
('SUPERVISEUR', 'Superviseur',          'Consultation des rapports et validation des operations sensibles');

-- Utilisateur administrateur par defaut
-- IMPORTANT : le mot de passe ci-dessous est un hash bcrypt de "Admin@2026".
-- Il doit imperativement etre regenere puis change des la premiere connexion :
--   php -r "echo password_hash('Admin@2026', PASSWORD_DEFAULT);"
INSERT INTO utilisateurs (matricule, nom, prenom, email, telephone, mot_de_passe, role_id, statut) VALUES
('ADM-0001', 'Admin', 'Systeme', 'admin@mobilemoney.local', '0340000000',
 '$2y$10$yLmfXJnxA1yKa53CYPcHKOuLyEW.lXFJq5.1QKjCGwoSADwa6lt.q', 1, 'ACTIF');

-- Types de transaction
INSERT INTO types_transaction (code, libelle, description) VALUES
('DEPOT',          'Depot',                 'Depot d''especes sur le compte d''un client via un agent'),
('RETRAIT',        'Retrait',               'Retrait d''especes depuis le compte d''un client via un agent'),
('TRANSFERT',      'Transfert',             'Transfert d''argent entre deux comptes clients'),
('PAIEMENT',       'Paiement',              'Paiement de facture ou marchand'),
('RECHARGE_AGENT', 'Recharge flotte agent', 'Approvisionnement du compte flotte d''un agent par l''operateur'),
('TRANSFERT_EXTERNE', 'Transfert vers un autre operateur', 'Transfert envoye vers un numero n''appartenant pas a notre reseau');

-- Grille tarifaire initiale (exemple de bareme)
INSERT INTO grille_tarifaire (type_transaction_id, montant_min, montant_max, frais_fixe, frais_pourcentage) VALUES
((SELECT id FROM types_transaction WHERE code = 'DEPOT'),          0,      1000000, 0,   0),
((SELECT id FROM types_transaction WHERE code = 'RETRAIT'),        0,      50000,   200, 0),
((SELECT id FROM types_transaction WHERE code = 'RETRAIT'),        50001,  1000000, 0,   1.0),
((SELECT id FROM types_transaction WHERE code = 'TRANSFERT'),      0,      50000,   100, 0),
((SELECT id FROM types_transaction WHERE code = 'TRANSFERT'),      50001,  1000000, 0,   0.5);

-- Parametres systeme par defaut
INSERT INTO parametres_systeme (cle, valeur, description) VALUES
('DEVISE',                            'MGA',     'Devise utilisee par la plateforme'),
('PLAFOND_COMPTE_CLIENT',             '2000000', 'Plafond maximal du solde d''un compte client'),
('PLAFOND_TRANSACTION_JOURNALIERE',   '1000000', 'Montant cumule maximal des transactions par jour et par client'),
('SEUIL_ALERTE_FLOTTE_AGENT',         '20000',   'Solde minimal de flotte en dessous duquel un agent est alerte'),
('COMMISSION_INTEROPERATEUR_POURCENTAGE', '1.0', 'Commission additionnelle (%) appliquee en plus du tarif de transfert normal, pour les transferts vers un autre operateur'),
('PREFIXE_NOTRE_OPERATEUR', '031', 'Prefixe telephonique (liste blanche) identifiant les numeros de notre propre operateur (on-net). Tout numero ne commencant pas par ce prefixe est considere comme appartenant a un autre operateur (off-net). Valeur d''exemple, a adapter.');

-- Prefixes de quelques operateurs concurrents (exemple, a completer par Mika)
-- Utilise uniquement pour NOMMER l'operateur externe dans les rapports, pas
-- pour determiner si un numero est interne (voir PREFIXE_NOTRE_OPERATEUR).
INSERT INTO prefixes_operateurs (prefixe, operateur_nom) VALUES
('032', 'Orange Money'),
('033', 'Telma Money'),
('034', 'Airtel Money'),
('038', 'Airtel Money');

-- Compte SYSTEME (collecte des frais operateur) - cree en dernier pour un id stable
INSERT INTO comptes (numero_compte, type_compte, client_id, agent_id, solde, plafond, statut) VALUES
('CPT-SYSTEME-001', 'SYSTEME', NULL, NULL, 0, 0, 'ACTIF');

-- =====================================================================
-- FIN base.sql
-- =====================================================================
