#!/bin/bash

# Configuration
REMOTE_HOST="votre-serveur.infomaniak.ch"
REMOTE_USER="votre-utilisateur"
REMOTE_PATH="/home/votre-utilisateur/www/votre-domaine.com"
LOCAL_PATH="."

# Couleurs pour les messages
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}Début du déploiement...${NC}"

# Compilation des assets
echo "Compilation des assets..."
npm run build

# Optimisation de Laravel
echo "Optimisation de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Synchronisation des fichiers
echo "Synchronisation des fichiers..."
rsync -avz --exclude='.git' \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='.env' \
    --exclude='.env.production' \
    --exclude='storage/*' \
    --exclude='bootstrap/cache/*' \
    $LOCAL_PATH/ $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/

# Commandes à exécuter sur le serveur distant
ssh $REMOTE_USER@$REMOTE_HOST "cd $REMOTE_PATH && \
    composer install --no-dev --optimize-autoloader && \
    php artisan migrate --force && \
    php artisan storage:link && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache"

echo -e "${GREEN}Déploiement terminé avec succès !${NC}" 