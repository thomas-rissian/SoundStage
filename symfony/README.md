# SoundStage

## Prérequis

Assurez-vous d'avoir **PHP**, **Symfony** et **Composer** préinstallés.

## Configuration de l'environnement

Créez un fichier `.env.local` en dupliquant le fichier `.env` existant, puis configurez la base de données.  
Supprimez les lignes relatives à la base de données et ajoutez la ligne suivante :

```ini
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

## Installation du projet

### Installer les dépendances

```shell
  composer install
```

Si elles sont déjà installées, mettez-les à jour :

```shell
  composer update
```

### Création et migration de la base de données

Attention, veillez à avoir un dossier `/migrations` à la racine.

```shell
    mkdir -p migrations
```

```shell
  symfony console doctrine:database:create
```
```shell
  symfony console make:migration
```
```shell
  symfony console doctrine:migrations:migrate
```
## Lancer le serveur

Veillez à créer les dossiers `/public/uploads/images`, s'ils n'existent pas pour le stockage des images.

```shell
    mkdir -p public/uploads/images
```

### Mode développement

```shell
  symfony serve
```

### Mode production

```shell
  APP_ENV=prod symfony server:start
```

## Résolution des problèmes

En cas de problème, videz le cache :

```shell
php bin/console cache:clear --env=prod

```

## Test 

### Création et migration de la base de données

```shell
  php bin/console doctrine:database:create --env=test
```
```shell
  php bin/console doctrine:migrations:migrate --env=test
```
```shell
  php bin/console doctrine:schema:update --force --env=test
```

### Couverture de code 

#### Shell 
```shell
    php bin/phpunit --coverage-text
```
#### HTML 
```shell
    php bin/phpunit --coverage-html var/coverage
```
