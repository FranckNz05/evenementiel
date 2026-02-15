# ✅ Exécution des migrations Laravel

## Situation
✅ Nginx fonctionne  
✅ PHP fonctionne  
✅ Laravel fonctionne  
❌ Tables de base de données manquantes

## Solution : Exécuter les migrations

### Option 1 : Migrations Laravel (si vous avez les fichiers de migration)

```bash
cd /var/www/mokilievent/evenementiel

# Vérifier les migrations disponibles
sudo -u www-data php artisan migrate:status

# Exécuter les migrations
sudo -u www-data php artisan migrate --force

# Vérifier que les tables sont créées
sudo -u www-data php artisan migrate:status
```

### Option 2 : Importer le fichier SQL (si vous l'avez)

```bash
cd /var/www/mokilievent/evenementiel

# Si vous avez le fichier SQL dans le projet
mysql -u root -p mokilievent < mokilievent_db\ \(1\).sql

# Ou avec le chemin complet
mysql -u root -p mokilievent < /chemin/complet/vers/mokilievent_db\ \(1\).sql
```

### Option 3 : Vérifier la connexion à la base de données d'abord

```bash
# Vérifier la configuration
grep DB_ /var/www/mokilievent/evenementiel/.env

# Tester la connexion
cd /var/www/mokilievent/evenementiel
sudo -u www-data php artisan db:show
```

## Commandes complètes

```bash
cd /var/www/mokilievent/evenementiel

# 1. Vérifier la connexion
sudo -u www-data php artisan db:show

# 2. Voir les migrations en attente
sudo -u www-data php artisan migrate:status

# 3. Exécuter les migrations
sudo -u www-data php artisan migrate --force

# 4. Vérifier que ça a fonctionné
mysql -u root -p -e "USE mokilievent; SHOW TABLES LIKE 'tickets';"
```

## Si les migrations échouent

### Vérifier les permissions de la base de données

```bash
# Se connecter à MySQL
mysql -u root -p

# Dans MySQL, vérifier les permissions
SHOW GRANTS FOR 'votre_utilisateur'@'localhost';

# Si nécessaire, donner les permissions
GRANT ALL PRIVILEGES ON mokilievent.* TO 'votre_utilisateur'@'localhost';
FLUSH PRIVILEGES;
```

