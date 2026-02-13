# Configuration GitHub Actions pour Déploiement Automatique

## Secrets à configurer dans GitHub

Allez dans votre repository GitHub : **Settings → Secrets and variables → Actions → New repository secret**

### 1. VPS_HOST
```
votre-serveur.com
```
ou
```
123.456.789.012
```
**Description** : L'adresse IP ou le domaine de votre VPS

---

### 2. VPS_USER
```
cursor
```
**Description** : L'utilisateur système sur le VPS (celui qui a accès au projet)

---

### 3. VPS_SSH_KEY
```
-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAABlwAAAAdzc2gtcn
... (votre clé privée SSH complète)
-----END OPENSSH PRIVATE KEY-----
```
**Description** : Votre clé privée SSH pour se connecter au VPS

**⚠️ Important** : 
- Utilisez une clé SSH dédiée (pas votre clé personnelle)
- Ne partagez JAMAIS cette clé publiquement
- Cette clé doit avoir accès au VPS sans mot de passe

---

### 4. VPS_DEPLOY_PATH (optionnel si vous utilisez le workflow fourni)
```
/var/www/mokilievent/evenementiel
```
**Description** : Le chemin complet du projet Laravel sur le VPS

**Note** : Ce secret n'est pas utilisé dans le workflow `deploy.yml` fourni car le script `deploy.sh` gère déjà les chemins. Mais vous pouvez l'utiliser si vous créez un workflow personnalisé.

---

## Génération d'une clé SSH pour GitHub Actions

### Sur votre machine locale ou le VPS

```bash
# Générer une nouvelle clé SSH dédiée
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/github_actions_deploy

# Ne pas mettre de passphrase (ou GitHub Actions ne pourra pas l'utiliser)
# Appuyez juste sur Entrée quand demandé
```

### Copier la clé publique sur le VPS

```bash
# Afficher la clé publique
cat ~/.ssh/github_actions_deploy.pub

# Sur le VPS, ajouter à authorized_keys
ssh cursor@votre-serveur
mkdir -p ~/.ssh
echo "VOTRE_CLE_PUBLIQUE_ICI" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

### Copier la clé privée dans GitHub Secrets

```bash
# Afficher la clé privée (à copier dans VPS_SSH_KEY)
cat ~/.ssh/github_actions_deploy
```

**Copiez TOUT le contenu** (y compris `-----BEGIN` et `-----END`) dans le secret `VPS_SSH_KEY` sur GitHub.

---

## Test de connexion SSH

Avant de configurer GitHub Actions, testez la connexion :

```bash
# Depuis votre machine locale
ssh -i ~/.ssh/github_actions_deploy cursor@votre-serveur

# Si ça fonctionne, vous pouvez configurer GitHub Actions
```

---

## Workflow GitHub Actions

Le fichier `.github/workflows/deploy.yml` est déjà créé. Il :

1. ✅ Se déclenche automatiquement à chaque push sur `main`
2. ✅ Peut être déclenché manuellement depuis l'onglet "Actions" de GitHub
3. ✅ Se connecte au VPS via SSH
4. ✅ Exécute le script `deploy.sh`

---

## Vérification après configuration

1. **Pousser le workflow** :
```bash
git add .github/workflows/deploy.yml
git commit -m "Add GitHub Actions deployment workflow"
git push origin main
```

2. **Vérifier sur GitHub** :
   - Allez dans l'onglet **Actions**
   - Vous devriez voir le workflow se déclencher
   - Cliquez dessus pour voir les logs

3. **Test manuel** :
   - Allez dans **Actions → Deploy to Production → Run workflow**
   - Cliquez sur "Run workflow"

---

## Dépannage

### Erreur: "Permission denied (publickey)"

- Vérifiez que la clé publique est bien dans `~/.ssh/authorized_keys` sur le VPS
- Vérifiez les permissions : `chmod 600 ~/.ssh/authorized_keys`
- Vérifiez que le secret `VPS_SSH_KEY` contient bien la clé privée complète

### Erreur: "Host key verification failed"

Ajoutez cette étape dans le workflow avant le déploiement :
```yaml
- name: Add VPS to known hosts
  run: |
    ssh-keyscan -H ${{ secrets.VPS_HOST }} >> ~/.ssh/known_hosts
```

### Erreur: "deploy.sh: command not found"

- Vérifiez que `deploy.sh` existe dans `/var/www/mokilievent/`
- Vérifiez qu'il est exécutable : `chmod +x /var/www/mokilievent/deploy.sh`

### Erreur: "The script must be executed from /var/www/mokilievent"

Le workflow utilise déjà le bon chemin. Vérifiez que le script `deploy.sh` est bien présent.

---

## Sécurité

- ✅ Utilisez une clé SSH dédiée (pas votre clé personnelle)
- ✅ Limitez les permissions de la clé SSH si possible
- ✅ Ne commitez JAMAIS les secrets dans le code
- ✅ Utilisez les GitHub Secrets pour toutes les informations sensibles
- ✅ Activez les notifications GitHub pour être alerté en cas d'échec

---

## Alternative : Déploiement avec mot de passe (non recommandé)

Si vous ne pouvez pas utiliser de clé SSH, vous pouvez utiliser `appleboy/ssh-action` avec un mot de passe :

```yaml
password: ${{ secrets.VPS_PASSWORD }}
```

**⚠️ Moins sécurisé** : Le mot de passe est stocké en clair dans les secrets GitHub.

