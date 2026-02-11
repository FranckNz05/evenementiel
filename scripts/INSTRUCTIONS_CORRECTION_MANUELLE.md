# 📋 Instructions pour la Correction Manuelle des Incohérences

## ⚠️ IMPORTANT : Avant de commencer

1. **FAITES UNE SAUVEGARDE** de votre base de données :
```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

2. **Testez d'abord sur un environnement de développement** si possible

3. **Exécutez l'analyse** pour voir ce qui sera modifié :
```bash
mysql -u username -p database_name < scripts/analyze_payment_inconsistencies.sql
```

## 📁 Ordre d'Exécution des Scripts

Exécutez les scripts dans l'ordre suivant dans votre base de données (MySQL/phpMyAdmin) :

### Étape 1 : Créer la table de log
**Fichier** : `01_create_log_table.sql`
- Crée la table pour enregistrer les incohérences détectées
- ⏱️ Temps estimé : < 1 seconde

### Étape 2 : Créer les triggers de validation
**Fichier** : `02_create_triggers.sql`
- Crée les triggers pour valider automatiquement les futurs paiements
- ⏱️ Temps estimé : < 1 seconde

### Étape 3 : Correction 1 - TS → payé
**Fichier** : `03_correction_1_ts_to_paid.sql`
- Corrige les paiements confirmés par Airtel (TS) mais non marqués comme payés
- ⏱️ Temps estimé : Variable selon le nombre de paiements

### Étape 4 : Correction 2 - TF/TE → échoué
**Fichier** : `04_correction_2_tf_te_to_failed.sql`
- Corrige les paiements échoués (TF/TE) mais marqués comme payés
- ⏱️ Temps estimé : Variable selon le nombre de paiements

### Étape 5 : Correction 3 - TIP/TA → en attente
**Fichier** : `05_correction_3_tip_ta_to_pending.sql`
- Corrige les paiements en attente (TIP/TA) mais marqués comme payés
- ⏱️ Temps estimé : Variable selon le nombre de paiements

### Étape 6 : Correction 4 - Nettoyer les dates invalides
**Fichier** : `06_correction_4_clean_payment_dates.sql`
- Supprime les dates de paiement pour les paiements non payés
- ⏱️ Temps estimé : Variable selon le nombre de paiements

### Étape 7 : Correction 5 - Ajouter les dates manquantes
**Fichier** : `07_correction_5_add_payment_dates.sql`
- Ajoute les dates de paiement pour les paiements payés sans date
- ⏱️ Temps estimé : Variable selon le nombre de paiements

### Étape 8 : Correction 6 - Synchroniser avec details.status
**Fichier** : `08_correction_6_sync_with_api_status.sql`
- Utilise `details.status` si `airtel_transaction_status` est absent
- ⏱️ Temps estimé : Variable selon le nombre de paiements

### Étape 9 : Vérification finale
**Fichier** : `09_verification_finale.sql`
- Vérifie que toutes les corrections ont été appliquées
- Affiche les statistiques et les incohérences restantes
- ⏱️ Temps estimé : < 5 secondes

## 🚀 Comment Exécuter

### Option 1 : Via phpMyAdmin
1. Connectez-vous à phpMyAdmin
2. Sélectionnez votre base de données
3. Cliquez sur l'onglet "SQL"
4. Copiez-collez le contenu du script
5. Cliquez sur "Exécuter"

### Option 2 : Via MySQL en ligne de commande
```bash
mysql -u username -p database_name < scripts/01_create_log_table.sql
mysql -u username -p database_name < scripts/02_create_triggers.sql
mysql -u username -p database_name < scripts/03_correction_1_ts_to_paid.sql
# ... etc
```

### Option 3 : Via MySQL Workbench / DBeaver
1. Ouvrez le fichier SQL
2. Connectez-vous à votre base de données
3. Exécutez le script (F5 ou bouton Exécuter)

## 📊 Vérification Après Chaque Étape

Chaque script de correction affiche automatiquement :
- Le nombre de paiements corrigés
- Une description de la correction effectuée

**Exemple de résultat attendu** :
```
paiements_corriges | description
-------------------|-------------------
15                 | CORRECTION 1: TS → payé
```

## ⚠️ Points d'Attention

### Correction 1 (TS → payé)
- **Impact** : Peut affecter beaucoup de paiements
- **Risque** : Faible (correction légitime)
- **Vérification** : Vérifiez que les dates de paiement sont correctes

### Correction 2 (TF/TE → échoué)
- **Impact** : CRITIQUE - Peut marquer des paiements comme échoués
- **Risque** : Élevé si des paiements sont réellement payés
- **Vérification** : Examinez manuellement quelques cas avant d'exécuter

### Correction 3 (TIP/TA → en attente)
- **Impact** : Peut remettre des paiements en attente
- **Risque** : Moyen
- **Vérification** : Vérifiez que ces paiements sont vraiment en attente

### Correction 4 (Nettoyage dates)
- **Impact** : Supprime des dates de paiement
- **Risque** : Faible (dates invalides de toute façon)
- **Vérification** : Vérifiez le nombre de dates supprimées

### Correction 5 (Ajout dates)
- **Impact** : Ajoute des dates de paiement
- **Risque** : Faible
- **Vérification** : Vérifiez que les dates sont cohérentes

### Correction 6 (Synchronisation API)
- **Impact** : Variable
- **Risque** : Moyen (utilise une source secondaire)
- **Vérification** : Vérifiez que `details.status` est fiable

## 🔍 Vérification Finale

Après avoir exécuté tous les scripts, exécutez `09_verification_finale.sql` pour :
- Voir les statistiques par statut
- Détecter les incohérences restantes (devrait être 0)
- Vérifier que les triggers sont actifs

## 🐛 En Cas de Problème

### Erreur : "Trigger already exists"
```sql
DROP TRIGGER IF EXISTS validate_payment_status_before_update;
DROP TRIGGER IF EXISTS validate_payment_status_before_insert;
```
Puis réexécutez `02_create_triggers.sql`

### Erreur : "Table doesn't exist"
Vérifiez que la table `paiements` existe :
```sql
SHOW TABLES LIKE 'paiements';
```

### Erreur : "Access denied"
Vérifiez vos permissions :
```sql
SHOW GRANTS;
```

### Restaurer la sauvegarde
```bash
mysql -u username -p database_name < backup_YYYYMMDD_HHMMSS.sql
```

## 📝 Notes Importantes

1. **Les corrections sont irréversibles** : Assurez-vous d'avoir une sauvegarde
2. **Exécutez dans l'ordre** : Les scripts sont conçus pour être exécutés séquentiellement
3. **Vérifiez après chaque étape** : Chaque script affiche le nombre de corrections
4. **Les triggers sont automatiques** : Une fois créés, ils valident tous les futurs paiements

## ✅ Checklist de Vérification

- [ ] Sauvegarde effectuée
- [ ] Analyse exécutée pour voir les incohérences
- [ ] Script 01 exécuté (table de log)
- [ ] Script 02 exécuté (triggers)
- [ ] Scripts 03-08 exécutés (corrections)
- [ ] Script 09 exécuté (vérification)
- [ ] Aucune incohérence restante (vérification finale)
- [ ] Triggers actifs (vérification finale)

---

**Version** : 1.0  
**Date** : 2026-01-30

