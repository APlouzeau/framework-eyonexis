# 🚀 Guide des Environnements

Ce projet dispose de 3 environnements distincts :

## 📦 Environnements disponibles

### 1. DEV (Développement Local)

**Usage :** Développement quotidien sur ta machine locale
**Commande :** `make dev`
**URLs :**

-   Frontend: http://localhost:3000
-   Backend: http://localhost:8000
-   phpMyAdmin: http://localhost:8081
-   Database: localhost:3307

**Caractéristiques :**

-   ✅ Hot reload activé
-   ✅ Base de données locale
-   ✅ Pas de HTTPS
-   ✅ Accès direct aux ports

---

### 2. PREPROD (Validation finale)

**Usage :** Valider les changements avant la mise en production
**Commande :** `make preprod`
**URLs :**

-   Frontend: https://YOUR_APP_NAME.YOUR_DOMAIN.fr
-   Backend: https://api.YOUR_APP_NAME.YOUR_DOMAIN.fr

**Caractéristiques :**

-   ❌ Pas de hot reload (code buildé et optimisé)
-   ✅ HTTPS via Traefik
-   ✅ Identique à la production
-   ✅ Performances optimales

---

## 🔄 Workflow recommandé

### Pour développer une nouvelle feature

1. **Développement local**

    ```bash
    git checkout -b feat/ma-nouvelle-feature
    make dev
    # Code, test, repeat...
    ```

2. **Validation en preprod**
    ```bash
    git checkout preprod
    git merge feat/ma-nouvelle-feature
    git push
    # Sur le VPS
    make preprod
    ```

---

## 📝 Commandes utiles

### Démarrer un environnement

```bash
make dev       # Local
make preprod   # Preprod (production-like)
```

### Arrêter un environnement

```bash
make down          # Dev
make down-preprod  # Preprod
```

### Redémarrer un environnement

```bash
make restart         # Dev
make restart-preprod # Preprod
```

### Voir les logs

```bash
make logs              # Tous les services
make logs-frontend     # Frontend uniquement
make logs-backend      # Backend uniquement
```

### Rebuild les images

```bash
make build           # Dev
make build-preprod   # Preprod
```

---

## ⚠️ Points d'attention

**Preprod = Production-like**
Toujours valider sur preprod avant de merger dans `main`.

## 🎯 Résumé visuel

```
┌─────────────────┬──────────────┬──────────────┐
│                 │   DEV        │  PREPROD     │
├─────────────────┼──────────────┼──────────────┤
│ Localisation    │ Local        │ VPS          │
│ Hot Reload      │ ✅          │ ❌           │
│ HTTPS           │ ❌          │ ✅           │
│ Performance     │ Normale      │ Optimale     │
│ Usage           │ Dev daily    │ Validation   │
│ Base de données │ Locale       │ Production   │
└─────────────────┴──────────────┴──────────────┘
```
