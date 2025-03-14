# SoundStage

créer env.local et faite la configuration nécessaire avec la bdd : 

```
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

Pour utiliser le projet : 

```shell
  composer install
```

```shell
  composer update
```

Création / migration bdd :

```shell
  symfony console doctrine:database:create
```
```shell
  symfony console make:migration
```
```shell
  symfony console doctrine:migrations:migrate
```


Création utilisateur bdd : 

```
id : generated
email : email@test.f
roles : ["ROLE_USER"] / ["ROLE_ADMIN"]
password : hash
```

```shell
  php bin/console security:hash-password
```

Démarrer serveur : 

```shell
  symfony serve
```