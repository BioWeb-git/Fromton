# Guide de dépannage Contao Manager

Ce guide explique comment résoudre les erreurs courantes de permissions et d'authentification lors d'une mise à jour de Contao via le Manager.

## 1. Conflits de permissions (Pouet vs WWW-Data)

**Problème :** Impossible de supprimer ou modifier des fichiers dans `vendor/` ou `assets/` car ils appartiennent alternativement à l'utilisateur local et au serveur web.

**Solution (Nuclear Fix) :**
Exécuter ces commandes pour réconcilier les droits et forcer l'héritage des permissions (ACL) :

```bash
sudo chown -R pouet:www-data vendor assets var && \
sudo chmod -R 775 vendor assets var && \
sudo setfacl -R -d -m u:www-data:rwx vendor assets var && \
sudo setfacl -R -d -m u:pouet:rwx vendor assets var
```

## 2. Authentification GitHub (Bundles privés)

**Problème :** Le Contao Manager (Web) ne peut pas accéder aux dépôts privés via SSH.

**Solution :**
1. Créer un **Personal Access Token (classic)** sur GitHub avec le scope `repo`.
2. Créer un fichier `auth.json` à la racine :
```json
{
    "github-oauth": {
        "github.com": "votre_token_ghp_..."
    }
}
```
3. Donner les droits de lecture au serveur web sur ce fichier :
```bash
sudo setfacl -m u:www-data:r /home/pouet/contao5/auth.json
```

## 3. Erreur autoloader manquant ("Chicken & Egg")

**Problème :** `require(vendor/autoload.php): Failed to open stream` car le dossier `vendor` est vide mais les plugins cherchent à le charger.

**Solution :** Forcer une installation minimale sans plugins via CLI pour restaurer l'autoloader.

```bash
php8.3 public/contao-manager.phar.php composer install --no-plugins --no-scripts --no-dev
php8.3 public/contao-manager.phar.php composer install --no-dev --optimize-autoloader
```

## 4. Blocage Composer Cloud

**Problème :** Le service Cloud de Contao n'a pas accès à vos dépôts privés.

**Solution :** Désactiver le Cloud dans les **Settings** du Contao Manager pour forcer la résolution locale (puisque votre serveur est déjà configuré avec `auth.json`).
