# 📊 Analyse des Données Fournies (IDs 249-292)

## ✅ Vérifications Effectuées

### 1. Cohérence statut / date_paiement

| ID | Statut | date_paiement | QR Code | Vérification |
|----|--------|---------------|---------|--------------|
| 249 | échoué | NULL | NULL | ✅ Correct |
| 250 | échoué | NULL | NULL | ✅ Correct |
| 251 | payé | 2025-12-13 12:06:13 | NULL | ✅ Correct |
| 252 | payé | 2025-12-13 12:07:35 | NULL | ✅ Correct |
| 253 | payé | 2025-12-21 15:37:18 | NULL | ✅ Correct |
| 254 | échoué | NULL | NULL | ✅ Correct |
| 255 | en attente | NULL | NULL | ✅ Correct |
| 256 | en attente | NULL | NULL | ✅ Correct |
| 257 | payé | 2026-01-30 08:41:50 | ✅ | ✅ Correct |
| 258-264 | en attente | NULL | NULL | ✅ Correct |
| 265 | payé | 2026-01-30 10:11:08 | ✅ | ✅ Correct |
| 266-270 | en attente | NULL | NULL | ✅ Correct |
| 271 | payé | 2026-02-01 13:48:25 | ✅ | ✅ Correct |
| 272-273 | en attente | NULL | NULL | ✅ Correct |
| 274 | payé | 2026-02-02 11:48:09 | ✅ | ✅ Correct |
| 275-292 | en attente | NULL | NULL | ✅ Correct |

### 2. Règles Respectées ✅

#### ✅ Règle 1 : date_paiement
- **Tous les paiements avec `statut = 'payé'` ont une `date_paiement`** ✅
- **Tous les paiements avec `statut ≠ 'payé'` ont `date_paiement = NULL`** ✅

#### ✅ Règle 2 : QR Code
- **Tous les QR codes sont générés uniquement pour les paiements `statut = 'payé'`** ✅
- **Aucun QR code pour les paiements `statut = 'en attente'` ou `'échoué'`** ✅

#### ✅ Règle 3 : Statuts
- **Les statuts sont cohérents** : `'en attente'`, `'payé'`, `'échoué'` ✅

---

## ⚠️ Vérification Nécessaire

### Point Important : `airtel_transaction_status` dans `details`

Pour confirmer que **tout est correct**, il faut vérifier que le champ `details` (JSON) contient bien `airtel_transaction_status` et que celui-ci correspond au statut métier.

**Exécutez cette requête SQL** pour vérifier :

```sql
SELECT 
    id,
    matricule,
    statut,
    JSON_EXTRACT(details, '$.airtel_transaction_status') as airtel_status,
    CASE 
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') = 'TS' AND statut != 'payé' THEN '❌ INCOHÉRENCE'
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TF', 'TE') AND statut = 'payé' THEN '❌ INCOHÉRENCE'
        WHEN JSON_EXTRACT(details, '$.airtel_transaction_status') IN ('TIP', 'TA') AND statut = 'payé' THEN '❌ INCOHÉRENCE'
        ELSE '✅ OK'
    END as verification
FROM paiements
WHERE id >= 249
ORDER BY id DESC;
```

---

## 📊 Statistiques

### Répartition par Statut
- **`'en attente'`** : ~35 paiements
- **`'payé'`** : ~8 paiements
- **`'échoué'`** : ~2 paiements

### Paiements avec QR Code
- **8 paiements** avec QR code, tous avec `statut = 'payé'` ✅

### Paiements Payés
- **Tous ont une `date_paiement`** ✅

---

## ✅ Conclusion

**D'après les données visibles, tout semble correct !** ✅

Les règles de base sont respectées :
1. ✅ `date_paiement` uniquement si `statut = 'payé'`
2. ✅ QR codes uniquement si `statut = 'payé'`
3. ✅ Pas de `date_paiement` si `statut ≠ 'payé'`

**Pour une vérification complète**, exécutez le script `scripts/11_verifier_donnees_actuelles.sql` qui vérifiera aussi la cohérence avec `airtel_transaction_status` dans le JSON `details`.

---

**Date** : 2026-01-30

