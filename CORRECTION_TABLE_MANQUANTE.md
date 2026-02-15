# 🔧 Correction : Table 'tickets' n'existe pas

## Erreur
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'mokilievent.tickets' doesn't exist
```

## Solution : Exécuter les migrations Laravel

### 1. Vérifier la connexion à la base de données

```bash
# Vérifier que la base de données est configurée
grep DB_ /var/www/mokilievent/evenementiel/.env
```

### 2. Exécuter les migrations

```bash
cd /var/www/mokilievent/evenementiel

# Exécuter les migrations
sudo -u www-data php artisan migrate

# Si des erreurs, forcer (attention, peut écraser des données)
sudo -u www-data php artisan migrate --force
```

### 3. Vérifier que les tables existent

```bash
# Se connecter à MySQL
mysql -u root -p

# Dans MySQL
USE mokilievent;
SHOW TABLES;
```

### 4. Si les migrations échouent

```bash
# Vérifier le statut des migrations
sudo -u www-data php artisan migrate:status

# Voir les migrations en attente
sudo -u www-data php artisan migrate --pretend
```

### 5. Si la base de données est vide

Si vous avez un fichier SQL de sauvegarde :

```bash
# Importer la base de données
mysql -u root -p mokilievent < /chemin/vers/votre/fichier.sql
```

Ou si vous avez le fichier dans le projet :

```bash
mysql -u root -p mokilievent < mokilievent_db\ \(1\).sql
```

## Commandes complètes

```bash
cd /var/www/mokilievent/evenementiel

# Vérifier la connexion
sudo -u www-data php artisan db:show

# Exécuter les migrations
sudo -u www-data php artisan migrate --force

# Vérifier le statut
sudo -u www-data php artisan migrate:status
```

