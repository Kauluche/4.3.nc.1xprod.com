# Instructions de déploiement NaturaCorp

## Prérequis
- PHP 8.2 ou supérieur
- Composer
- Node.js et npm
- MySQL 5.7 ou supérieur

## Étapes de déploiement

1. **Configuration de l'environnement**
   - Copier le fichier `.env.example` vers `.env`
   - Générer une nouvelle clé d'application : `php artisan key:generate`
   - Configurer les variables d'environnement dans le fichier `.env`

2. **Installation des dépendances**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

3. **Configuration de la base de données**
   - Créer une base de données MySQL
   - Configurer les informations de connexion dans le fichier `.env`
   - Exécuter les migrations : `php artisan migrate --force`

4. **Configuration des permissions**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

5. **Optimisation de Laravel**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Configuration du serveur web**
   - Le document root doit pointer vers le dossier `public`
   - Assurez-vous que le module mod_rewrite est activé
   - Le fichier `.htaccess` est déjà configuré dans le dossier public

## Structure des dossiers
```
naturacorp/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          # Document root du serveur web
├── resources/
├── routes/
├── storage/         # Doit être accessible en écriture
├── tests/
├── vendor/
├── .env            # Fichier de configuration
├── .htaccess       # Configuration Apache
└── artisan         # CLI Laravel
```

## Notes importantes
- Assurez-vous que le dossier `storage` et `bootstrap/cache` sont accessibles en écriture
- Le fichier `.env` ne doit pas être accessible publiquement
- Activez HTTPS pour la sécurité
- Configurez les sauvegardes de la base de données
- Surveillez les logs dans `storage/logs` 