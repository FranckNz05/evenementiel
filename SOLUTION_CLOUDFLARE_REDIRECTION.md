# ✅ Solution : Redirection vers n8n depuis Cloudflare

## Diagnostic effectué

✅ n8n n'est **PAS** installé sur le VPS  
✅ Nginx fonctionne correctement (port 80/443)  
✅ Le serveur répond correctement en local (`curl localhost` = 200 OK)

**Conclusion : Le problème vient de Cloudflare, pas du VPS.**

## 🔧 Solution : Vérifier et corriger les règles Cloudflare

### Étape 1 : Vérifier les Page Rules

1. Connectez-vous à https://dash.cloudflare.com
2. Sélectionnez votre domaine `mokilievent.com`
3. Allez dans **Rules** → **Page Rules** (ou **Redirect Rules**)
4. Cherchez une règle qui :
   - Redirige vers `/signin`
   - Redirige vers n8n
   - A une URL pattern comme `*mokilievent.com*` ou `mokilievent.com/*`

### Étape 2 : Vérifier les Transform Rules

1. Dans Cloudflare, allez dans **Rules** → **Transform Rules** → **Redirect Rules**
2. Cherchez des règles qui pourraient rediriger vers `/signin`

### Étape 3 : Vérifier les Workers (si vous en avez)

1. Allez dans **Workers & Pages**
2. Vérifiez s'il y a un Worker actif qui pourrait intercepter les requêtes

### Étape 4 : Vérifier les paramètres SSL/TLS

1. Allez dans **SSL/TLS** → **Overview**
2. Vérifiez le mode SSL (devrait être "Flexible" ou "Full")
3. Allez dans **SSL/TLS** → **Edge Certificates**
4. Vérifiez que "Always Use HTTPS" n'a pas de redirection vers `/signin`

## 🎯 Solution la plus probable

Il y a probablement une **Page Rule** ou **Redirect Rule** dans Cloudflare qui redirige toutes les requêtes vers `/signin`.

### Pour supprimer la règle problématique :

1. **Page Rules** :
   - Trouvez la règle avec le pattern `*mokilievent.com*` ou similaire
   - Cliquez sur **Delete** ou modifiez la règle pour supprimer la redirection

2. **Redirect Rules** :
   - Trouvez la règle qui redirige vers `/signin`
   - Supprimez-la ou modifiez-la

### Configuration recommandée pour mokilievent.com

Si vous avez besoin d'une Page Rule, voici une configuration correcte :

**URL Pattern** : `mokilievent.com/*`  
**Settings** :
- **Always Use HTTPS** : On
- **Cache Level** : Standard (ou selon vos besoins)
- **Browser Cache TTL** : Respect Existing Headers

**⚠️ Ne PAS ajouter de redirection vers `/signin`**

## 🔍 Vérification après correction

1. **Attendez 2-5 minutes** pour que Cloudflare propage les changements
2. **Videz le cache Cloudflare** :
   - Allez dans **Caching** → **Configuration**
   - Cliquez sur **Purge Everything**
3. **Testez** :
   ```bash
   curl -I https://mokilievent.com
   ```
   Devrait retourner `HTTP/2 200` ou `HTTP/2 301` (redirection HTTPS normale)

## 📋 Checklist

- [ ] Vérifié les Page Rules dans Cloudflare
- [ ] Vérifié les Redirect Rules dans Cloudflare
- [ ] Vérifié les Transform Rules dans Cloudflare
- [ ] Vérifié les Workers actifs
- [ ] Supprimé/modifié la règle problématique
- [ ] Purge du cache Cloudflare
- [ ] Testé l'accès au site

## 🆘 Si vous ne trouvez pas la règle

1. **Vérifiez l'historique** :
   - Cloudflare garde un historique des changements
   - Regardez dans **Analytics & Logs** → **Audit Logs**

2. **Contactez le support Cloudflare** :
   - Ils peuvent vous aider à identifier la règle problématique
   - Mentionnez que vous avez une redirection non désirée vers `/signin`

3. **Vérifiez les sous-domaines** :
   - Parfois une règle sur un sous-domaine peut affecter le domaine principal
   - Vérifiez tous les sous-domaines configurés

