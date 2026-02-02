# 🎭 Guide Final - Design de Billet MokiliEvent

## 🚀 Prévisualisation

Pour travailler sur le design du billet :
```
http://127.0.0.1:8000/ticket/design/preview?payment_id=232
```

## 📝 Fichier à Modifier

```
resources/views/tickets/template.blade.php
```

## 🎨 Design Actuel

### **Titre de l'Événement (3 affichages)** :
1. **Premier mot** : Haut gauche, contour blanc, vintage 22px
2. **Titre complet** : Centre, blanc plein, vintage 42px, GRAS
3. **Dernier mot** : Bas droite, contour blanc, vintage 22px

### **Partie Gauche** :
- Logo MokiliEvent : 35x35px
- Jour : 52px blanc (SANS cadre, police Impact)
- Mois/Année/Heure : Agrandis à côté du jour
- Adresse : Justifiée sous la date
- Nom billet : Extrême gauche, petit cadre ├─╮
- 5 Logos sponsors : Centrés, SANS fond, SANS contours

### **Partie Droite (Noire)** :
- Fond : Image foule (opacité 0.12)
- Nom billet : Cadre doré ╭─╮ au-dessus du QR
- QR Code : 80x80px centré
- Prix : 36px doré SOUS le QR
- Référence : Tout en bas

## 🛠️ Commandes Utiles

```powershell
# Vider le cache
php artisan view:clear

# Vérifier les données
php artisan ticket:test-data 232

# Déboguer les images
http://127.0.0.1:8000/debug/event-images
```

## 📧 Configuration Email

- MAIL_HOST: smtp.gmail.com
- MAIL_FROM: francknz05@gmail.com
- Les billets sont envoyés automatiquement après paiement

## ✅ Tout Fonctionne

- ✅ Génération PDF avec template.blade.php
- ✅ Envoi email avec Gmail
- ✅ Images converties en base64
- ✅ Logos sponsors et organisateur
- ✅ Design spectaculaire vintage

Bon design ! 🎉

