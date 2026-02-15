# 🔧 Solution : Hostinger intercepte les requêtes

## Problème
Hostinger a un proxy/load balancer (LiteSpeed) qui intercepte les requêtes avant qu'elles n'arrivent à votre serveur. Cela empêche Certbot d'accéder à `/.well-known/acme-challenge/`.

## Solutions

### Solution 1 : Utiliser le DNS challenge (Recommandé)

```bash
sudo certbot certonly --manual --preferred-challenges dns -d mokilievent.com -d www.mokilievent.com
```

Puis ajoutez les enregistrements TXT dans Hostinger (pas Cloudflare).

### Solution 2 : Configurer Hostinger pour désactiver le proxy

Dans Hostinger :
1. Allez dans la gestion de votre domaine
2. Cherchez les options de proxy/CDN
3. Désactivez le proxy si possible
4. Ou configurez pour laisser passer les requêtes vers votre serveur

### Solution 3 : Utiliser l'IP directe du serveur

Si vous connaissez l'IP directe de votre serveur (pas celle de Hostinger), vous pouvez :
1. Configurer le DNS pour pointer directement vers cette IP
2. Puis utiliser Certbot normalement

### Solution 4 : Installer SSL via Hostinger

Si Hostinger propose une installation SSL dans leur panel, utilisez-la.

## Vérification

Testez si vous pouvez accéder directement au serveur :

```bash
# Depuis le serveur
curl -I http://localhost

# Depuis l'extérieur (remplacez par l'IP directe du serveur si vous la connaissez)
curl -I http://IP_DIRECTE_SERVEUR
```

Si `curl localhost` fonctionne mais pas depuis l'extérieur, c'est que Hostinger intercepte.

