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
