# 🔍 Commandes de diagnostic à exécuter sur le VPS

Exécutez ces commandes une par une et partagez les résultats.

## 1. Vérifier si n8n est actif

```bash
sudo systemctl status n8n
```

## 2. Vérifier les processus qui écoutent sur les ports 80 et 443

```bash
sudo ss -tlnp | grep -E ':(80|443)'
```

## 3. Chercher des références à n8n dans nginx

```bash
sudo grep -r "n8n" /etc/nginx/
```

## 4. Chercher des références à signin dans nginx

```bash
sudo grep -r "signin" /etc/nginx/
```

## 5. Vérifier les configurations nginx actives

```bash
ls -la /etc/nginx/sites-enabled/
```

## 6. Voir la configuration pour mokilievent.com

```bash
sudo cat /etc/nginx/sites-available/mokilievent.com.conf
```

## 7. Vérifier la configuration par défaut (si elle existe)

```bash
sudo cat /etc/nginx/sites-available/default 2>/dev/null || echo "Pas de configuration par défaut"
```

## 8. Tester la réponse locale

```bash
curl -I http://localhost
```

## 9. Vérifier les processus n8n en cours d'exécution

```bash
ps aux | grep -i n8n | grep -v grep
```

## 10. Vérifier les logs nginx récents

```bash
sudo tail -20 /var/log/nginx/mokilievent.com.error.log 2>/dev/null || echo "Fichier de log non trouvé"
```

---

**Commande rapide pour tout vérifier d'un coup :**

```bash
echo "=== Status n8n ===" && sudo systemctl status n8n --no-pager | head -5
echo ""
echo "=== Ports 80/443 ===" && sudo ss -tlnp | grep -E ':(80|443)'
echo ""
echo "=== Recherche n8n dans nginx ===" && sudo grep -r "n8n" /etc/nginx/ 2>/dev/null || echo "Aucune référence"
echo ""
echo "=== Configurations actives ===" && ls -la /etc/nginx/sites-enabled/
echo ""
echo "=== Test localhost ===" && curl -I http://localhost 2>&1 | head -10
```

