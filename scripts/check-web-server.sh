#!/bin/bash

# Script pour vérifier quel serveur web est actif
# Usage: sudo ./check-web-server.sh

echo "=== Vérification des serveurs web ==="
echo ""

# Vérifier Nginx
echo "1. Statut Nginx:"
if systemctl is-active --quiet nginx; then
    echo "   ✓ Nginx est ACTIF"
    systemctl status nginx --no-pager | head -3
else
    echo "   ✗ Nginx est INACTIF"
fi
echo ""

# Vérifier Apache
echo "2. Statut Apache:"
if systemctl is-active --quiet apache2; then
    echo "   ✓ Apache est ACTIF"
    systemctl status apache2 --no-pager | head -3
elif systemctl is-active --quiet httpd; then
    echo "   ✓ Apache (httpd) est ACTIF"
    systemctl status httpd --no-pager | head -3
else
    echo "   ✗ Apache est INACTIF"
fi
echo ""

# Vérifier LiteSpeed
echo "3. Statut LiteSpeed:"
if systemctl is-active --quiet lsws; then
    echo "   ✓ LiteSpeed est ACTIF"
    systemctl status lsws --no-pager | head -3
elif ps aux | grep -q "[l]shttpd"; then
    echo "   ✓ LiteSpeed est ACTIF (processus détecté)"
    ps aux | grep "[l]shttpd" | head -2
else
    echo "   ✗ LiteSpeed est INACTIF"
fi
echo ""

# Vérifier les ports en écoute
echo "4. Ports en écoute (80, 443):"
netstat -tlnp | grep -E ':(80|443)' || ss -tlnp | grep -E ':(80|443)'
echo ""

# Vérifier la configuration Nginx
echo "5. Configuration Nginx pour mokilievent.com:"
if [ -f "/etc/nginx/sites-available/mokilievent.com.conf" ]; then
    echo "   ✓ Fichier de configuration existe"
    echo "   Document root:"
    grep "root" /etc/nginx/sites-available/mokilievent.com.conf | head -1
else
    echo "   ✗ Fichier de configuration n'existe pas"
fi
echo ""

# Test de connexion
echo "6. Test de connexion:"
echo "   Test local:"
curl -I http://localhost 2>&1 | head -5
echo ""
echo "   Test avec domaine:"
curl -I http://mokilievent.com 2>&1 | head -5

