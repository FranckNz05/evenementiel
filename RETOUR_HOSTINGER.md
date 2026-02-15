# 🔄 Retour à Hostinger - Configuration DNS

## Étapes pour revenir à Hostinger

### 1. Dans Cloudflare

1. Allez dans **Overview** (Aperçu) de votre domaine
2. En bas de la page, trouvez **Advanced Actions**
3. Cliquez sur **Remove Site** ou **Retirer le site**
4. Confirmez la suppression

**Note :** Cela ne supprime pas le domaine, juste la configuration Cloudflare.

### 2. Dans Hostinger

1. Allez dans la gestion de votre domaine
2. Trouvez la section **Nameservers** ou **Serveurs de noms**
3. Changez les nameservers pour utiliser ceux de Hostinger :
   - `ns1.dns-parking.com`
   - `ns2.dns-parking.com`
   - Ou les nameservers spécifiques à Hostinger (vérifiez dans votre compte)

### 3. Configurer les enregistrements DNS dans Hostinger

1. Allez dans **DNS** ou **Zone DNS** dans Hostinger
2. Ajoutez/modifiez les enregistrements suivants :

**Enregistrement A :**
- Type : `A`
- Nom : `@` ou `mokilievent.com`
- Valeur : `IP_DU_VPS` (remplacez par l'IP réelle de votre VPS)
- TTL : `3600` ou `Automatique`

**Enregistrement A pour www :**
- Type : `A`
- Nom : `www`
- Valeur : `IP_DU_VPS` (même IP)
- TTL : `3600` ou `Automatique`

### 4. Attendre la propagation DNS

- La propagation peut prendre de 1 à 48 heures
- Généralement, c'est quelques heures
- Vous pouvez vérifier avec :
  ```bash
  nslookup mokilievent.com
  ```
  Devrait retourner l'IP de votre VPS (pas les IPs Cloudflare)

### 5. Une fois la propagation terminée

Vous pourrez installer SSL avec Certbot en utilisant le challenge HTTP (plus simple) :

```bash
sudo certbot --nginx -d mokilievent.com -d www.mokilievent.com
```

## Vérification

Après la propagation, testez :
```bash
# Devrait retourner l'IP de votre VPS
nslookup mokilievent.com

# Test HTTP
curl -I http://mokilievent.com
```

