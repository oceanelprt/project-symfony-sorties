# project-symfony-sorties

TP Symfony "Sorties" ENI 

### Environnement de développement

#### Pré-requis

* \>=PHP 7.4
* Composer
* Symfony CLI
* nodejs
* npm

#### Installer l'environnement de développement

```bash
composer install
npm install
npm run build
```

#### Mise en place de la BDD

```bash
# Environnement "classique"
créer fichier ".env.local"
BDD -> MySQL
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Environnement "Docker"
docker-compose up -d
symfony console make:migration
symfony console doctrine:migrations:migrate
```

#### Ajouter des données en BDD
```bash
symfony console doctrine:fixtures:load
```

#### Lancer l'environnement de développement
```bash
symfony serve
```
#### Commandes
```bash
# archivage des anciennes sorties (+1mois)
symfony console app:archiving:command
```
