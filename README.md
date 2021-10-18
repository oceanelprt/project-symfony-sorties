# project-symfony-sorties

TP Symfony "Sorties" ENI 

### Environnement de développement

#### Pré-requis

* \>=PHP 7.2
* Composer
* Symfony CLI

#### Installer l'environnement de développement
```bash
créer fichier ".env.local"
BDD -> MySQL
```
```bash
composer install
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

#### Lancer l'environnement de développement
```bash
symfony server:start
```