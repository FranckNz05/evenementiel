# 📋 Guide d'Utilisation des Scripts SQL

## 📁 Fichiers Disponibles

### 1. `fix_payment_status_inconsistencies.sql` (Script Complet)
**Description** : Script complet qui fait tout en une fois
- ✅ Crée la table de log
- ✅ Crée les triggers de validation
- ✅ Corrige les données existantes
- ✅ Affiche les statistiques

**Utilisation** :
```bash
mysql -u username -p database_name < scripts/fix_payment_status_inconsistencies.sql
```

**Quand l'utiliser** : Pour une installation complète en une seule fois

---

### 2. `create_payment_validation_triggers.sql` (Triggers Seulement)
**Description** : Crée uniquement les triggers de validation
- ✅ Crée les triggers pour INSERT et UPDATE
- ✅ Valide automatiquement la cohérence statut/date_paiement

**Utilisation** :
```bash
mysql -u username -p database_name < scripts/create_payment_validation_triggers.sql
```

**Quand l'utiliser** : Si vous voulez seulement les triggers sans corriger les données existantes

---

### 3. `correct_payment_data_only.sql` (Correction Seulement)
**Description** : Corrige uniquement les données existantes
- ✅ Synchronise les statuts avec `airtel_transaction_status`
- ✅ Nettoie les dates de paiement invalides
- ✅ Ajoute les dates de paiement manquantes

**Utilisation** :
```bash
mysql -u username -p database_name < scripts/correct_payment_data_only.sql
```

**Quand l'utiliser** : Si vous voulez seulement corriger les données sans créer les triggers

---

### 4. `analyze_payment_inconsistencies.sql` (Analyse Seulement)
**Description** : Analyse les incohérences sans rien modifier
- ✅ Détecte tous les types d'incohérences
- ✅ Affiche les statistiques
- ✅ Identifie les paiements à risque

**Utilisation** :
```bash
mysql -u username -p database_name < scripts/analyze_payment_inconsistencies.sql
```

**Quand l'utiliser** : Pour analyser les incohérences avant de corriger

---

## 🚀 Ordre d'Exécution Recommandé

### Option 1 : Installation Complète (Recommandé)
```bash
# 1. Analyser d'abord pour voir ce qui sera modifié
mysql -u username -p database_name < scripts/analyze_payment_inconsistencies.sql > analysis_results.txt

# 2. Faire une sauvegarde
mysqldump -u username -p database_name > backup_before_fix.sql

# 3. Exécuter le script complet
mysql -u username -p database_name < scripts/fix_payment_status_inconsistencies.sql
```

### Option 2 : Étape par Étape
```bash
# 1. Analyser
mysql -u username -p database_name < scripts/analyze_payment_inconsistencies.sql

# 2. Sauvegarder
mysqldump -u username -p database_name > backup.sql

# 3. Corriger les données
mysql -u username -p database_name < scripts/correct_payment_data_only.sql

# 4. Créer les triggers
mysql -u username -p database_name < scripts/create_payment_validation_triggers.sql
```

---

## ⚠️ Précautions

### Avant d'exécuter les scripts

1. **Sauvegarder la base de données** :
```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

2. **Tester sur un environnement de développement** d'abord

3. **Vérifier les permissions** : Assurez-vous d'avoir les droits nécessaires (CREATE TRIGGER, UPDATE, etc.)

4. **Exécuter l'analyse d'abord** pour voir ce qui sera modifié

### Après l'exécution

1. **Vérifier les triggers** :
```sql
SHOW TRIGGERS LIKE 'paiements';
```

2. **Vérifier les corrections** :
```sql
SELECT 
    statut,
    COUNT(*) as total,
    SUM(CASE WHEN date_paiement IS NOT NULL THEN 1 ELSE 0 END) as with_date
FROM paiements
GROUP BY statut;
```

3. **Exécuter l'analyse** pour vérifier qu'il ne reste plus d'incohérences :
```bash
mysql -u username -p database_name < scripts/analyze_payment_inconsistencies.sql
```

---

## 📊 Ce que font les Corrections

### Correction 1 : TS → payé
Met à jour les paiements où Airtel confirme le succès (TS) mais le statut n'est pas "payé"

### Correction 2 : TF/TE → échoué
Met à jour les paiements où Airtel confirme l'échec (TF/TE) mais le statut est "payé"

### Correction 3 : TIP/TA → en attente
Met à jour les paiements où Airtel indique en attente (TIP/TA) mais le statut est "payé"

### Correction 4 : Nettoyage date_paiement
Supprime les dates de paiement pour les paiements non payés

### Correction 5 : Ajout date_paiement
Ajoute une date de paiement pour les paiements payés sans date

### Correction 6 : Synchronisation avec details.status
Utilise `details.status` comme source secondaire si `airtel_transaction_status` est absent

---

## 🔍 Vérification des Triggers

### Voir les triggers créés
```sql
SHOW TRIGGERS LIKE 'paiements';
```

### Voir le code d'un trigger
```sql
SHOW CREATE TRIGGER validate_payment_status_before_update;
```

### Supprimer un trigger (si nécessaire)
```sql
DROP TRIGGER IF EXISTS validate_payment_status_before_update;
DROP TRIGGER IF EXISTS validate_payment_status_before_insert;
```

---

## 🐛 Dépannage

### Erreur : "Trigger already exists"
```sql
DROP TRIGGER IF EXISTS validate_payment_status_before_update;
DROP TRIGGER IF EXISTS validate_payment_status_before_insert;
```
Puis réexécutez le script.

### Erreur : "Access denied"
Vérifiez que vous avez les permissions nécessaires :
```sql
SHOW GRANTS;
```

### Erreur : "Table doesn't exist"
Vérifiez que la table `paiements` existe :
```sql
SHOW TABLES LIKE 'paiements';
```

### Les corrections ne s'appliquent pas
Vérifiez que les données JSON sont bien formatées :
```sql
SELECT id, details FROM paiements LIMIT 1;
```

---

## 📝 Notes Importantes

1. **Les triggers sont automatiques** : Une fois créés, ils valident automatiquement toutes les insertions et mises à jour

2. **Les corrections sont irréversibles** : Assurez-vous d'avoir une sauvegarde avant d'exécuter les scripts de correction

3. **Les dates sont récupérées depuis JSON** : Le script essaie de récupérer les dates depuis `callback_received_at`, `verified_at`, ou `initiated_at`

4. **Les statuts sont synchronisés** : Le script synchronise les statuts selon la priorité :
   - Priorité 1 : `airtel_transaction_status`
   - Priorité 2 : `details.status`

---

**Version** : 1.0  
**Date** : 2026-01-30

