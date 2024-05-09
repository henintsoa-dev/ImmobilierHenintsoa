# Projet ImmobilierHenintsoa

## Installation

- Installation des dépendances php

```
composer install
```

- Compilation des assets :

```
npm install && npm run dev
```

- Migration de la base de données :

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

## Accès

(_LOGIN / MDP_)

- **Front :** http://localhost:8000/


- **Admin :** http://localhost:8000/admin/property
    - **Admin :** admin@admin.com / azerty

- Mailhog : http://localhost:8095/
