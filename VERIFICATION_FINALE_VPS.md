# Vérifications finales sur le VPS

Exécutez ces commandes pour confirmer que tout est correct côté serveur :

```bash
# Vérifier s'il y a des processus n8n qui tournent
ps aux | grep -i n8n | grep -v grep

# Vérifier les configurations nginx actives
ls -la /etc/nginx/sites-enabled/

# Voir la configuration complète pour mokilievent.com
sudo cat /etc/nginx/sites-available/mokilievent.com.conf

# Vérifier s'il y a des références à n8n ou signin
sudo grep -r "n8n\|signin" /etc/nginx/

# Tester avec le nom de domaine depuis le serveur
curl -I http://mokilievent.com
```

Mais comme le serveur répond correctement en local, le problème est **100% côté Cloudflare**.

