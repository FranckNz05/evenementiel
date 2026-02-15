#!/bin/bash

# Script de diagnostic pour identifier la cause de la redirection vers n8n
# Usage: sudo bash diagnostic_redirection_n8n.sh

echo "=========================================="
echo "🔍 Diagnostic : Redirection vers n8n"
echo "=========================================="
echo ""

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Vérifier les configurations nginx
echo -e "${YELLOW}1. Vérification des configurations Nginx${NC}"
echo "----------------------------------------"
echo ""

echo "Configurations actives dans sites-enabled:"
ls -la /etc/nginx/sites-enabled/ 2>/dev/null || echo "Aucune configuration trouvée"
echo ""

echo "Recherche de références à 'n8n' dans les configurations nginx:"
grep -r "n8n" /etc/nginx/ 2>/dev/null || echo "Aucune référence à n8n trouvée"
echo ""

echo "Recherche de références à 'signin' dans les configurations nginx:"
grep -r "signin" /etc/nginx/ 2>/dev/null || echo "Aucune référence à signin trouvée"
echo ""

echo "Configuration pour mokilievent.com:"
if [ -f /etc/nginx/sites-available/mokilievent.com.conf ]; then
    echo "✅ Fichier trouvé"
    echo "Premières lignes:"
    head -20 /etc/nginx/sites-available/mokilievent.com.conf
else
    echo -e "${RED}❌ Fichier non trouvé${NC}"
fi
echo ""

# 2. Vérifier les processus qui écoutent sur les ports 80 et 443
echo -e "${YELLOW}2. Vérification des ports 80 et 443${NC}"
echo "----------------------------------------"
echo ""

echo "Processus écoutant sur le port 80:"
if command -v ss &> /dev/null; then
    ss -tlnp | grep :80 || echo "Aucun processus trouvé"
else
    netstat -tlnp 2>/dev/null | grep :80 || echo "Aucun processus trouvé"
fi
echo ""

echo "Processus écoutant sur le port 443:"
if command -v ss &> /dev/null; then
    ss -tlnp | grep :443 || echo "Aucun processus trouvé"
else
    netstat -tlnp 2>/dev/null | grep :443 || echo "Aucun processus trouvé"
fi
echo ""

# 3. Vérifier si n8n est en cours d'exécution
echo -e "${YELLOW}3. Vérification de n8n${NC}"
echo "----------------------------------------"
echo ""

if systemctl is-active --quiet n8n 2>/dev/null; then
    echo -e "${RED}⚠️  n8n est actif${NC}"
    systemctl status n8n --no-pager | head -10
elif systemctl list-units --type=service | grep -q n8n; then
    echo "n8n est installé mais inactif"
    systemctl status n8n --no-pager | head -5
else
    echo "n8n n'est pas installé comme service systemd"
fi
echo ""

echo "Processus n8n en cours d'exécution:"
ps aux | grep -i n8n | grep -v grep || echo "Aucun processus n8n trouvé"
echo ""

# 4. Vérifier les logs nginx récents
echo -e "${YELLOW}4. Dernières entrées des logs Nginx${NC}"
echo "----------------------------------------"
echo ""

if [ -f /var/log/nginx/mokilievent.com.access.log ]; then
    echo "Dernières 10 lignes de access.log:"
    tail -10 /var/log/nginx/mokilievent.com.access.log
else
    echo "Fichier access.log non trouvé"
fi
echo ""

if [ -f /var/log/nginx/mokilievent.com.error.log ]; then
    echo "Dernières 10 lignes de error.log:"
    tail -10 /var/log/nginx/mokilievent.com.error.log
else
    echo "Fichier error.log non trouvé"
fi
echo ""

# 5. Tester la réponse du serveur local
echo -e "${YELLOW}5. Test de la réponse locale${NC}"
echo "----------------------------------------"
echo ""

echo "Test avec curl localhost:"
curl -I http://localhost 2>&1 | head -15
echo ""

echo "Test avec curl 127.0.0.1:"
curl -I http://127.0.0.1 2>&1 | head -15
echo ""

# 6. Vérifier la configuration nginx par défaut
echo -e "${YELLOW}6. Configuration nginx par défaut${NC}"
echo "----------------------------------------"
echo ""

if [ -f /etc/nginx/sites-available/default ]; then
    echo "⚠️  Configuration par défaut trouvée:"
    echo "Premières lignes:"
    head -30 /etc/nginx/sites-available/default
    echo ""
    echo "Vérifiez si cette configuration intercepte les requêtes pour mokilievent.com"
else
    echo "Aucune configuration par défaut trouvée"
fi
echo ""

# 7. Vérifier l'ordre de priorité des configurations
echo -e "${YELLOW}7. Ordre des configurations actives${NC}"
echo "----------------------------------------"
echo ""

echo "Configurations dans sites-enabled (ordre alphabétique):"
ls -1 /etc/nginx/sites-enabled/ 2>/dev/null || echo "Aucune configuration"
echo ""

# 8. Résumé et recommandations
echo -e "${YELLOW}=========================================="
echo "📋 Résumé et recommandations"
echo "==========================================${NC}"
echo ""

echo "Actions à vérifier:"
echo "1. Si n8n est actif sur le port 80/443, arrêtez-le ou configurez-le sur un autre port"
echo "2. Si une configuration nginx redirige vers n8n, modifiez-la"
echo "3. Vérifiez les règles Cloudflare (Page Rules, Transform Rules)"
echo "4. Assurez-vous que la configuration mokilievent.com.conf est active et correcte"
echo ""

echo "Pour plus de détails, consultez: DIAGNOSTIC_REDIRECTION_N8N.md"
echo ""

