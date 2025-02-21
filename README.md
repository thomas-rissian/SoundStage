# SoundStage

créer env.local et faite la configuration nécessaire avec la bdd : 

``
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
``

Pour utiliser le projet : 

``
composer update
``

Création utilisateur bdd : 

``
id : generated
email : email@test.f
roles : ["ROLE_USER"] / ["ROLE_ADMIN"]
password : hash
``