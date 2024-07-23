# Projet Immobilier

## Installation

- Installation des dépendances php

```
composer install
```

- Compilation des assets :

```
npm install && npm run dev
```

- Création de la base de données :

```
php bin/console doctrine:database:create
```

- Migration de la base de données :

```
php bin/console doctrine:migrations:migrate
```

- Chargement des données tests (écrase la BDD avec les données) :

```
php bin/console doctrine:fixtures:load
```

- [maildev] (https://grafikart.fr/tutoriels/maildev-tester-emails-595), puis :

```
maildev --hide-extensions STARTTLS
```

## Accès

(_LOGIN / MDP_)

- **Front :** http://localhost:8000/


- **Admin :** 
    - **Login :** http://localhost:8000/login
    - **Identifiants :** john@doe.com / demo
    - **Biens  :** http://localhost:8000/admin/property
    - **Options :** http://localhost:8000/admin/option   
    
