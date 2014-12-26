Getting Started With GOS Query Builder
======================================

by default, Doctrine2 create new empty instance of query builder, that force you to repeat the operation in each methods. Keep your repository clean, healthy, avoid to repeat yourself, it's why this component exists.

Prerequisites
=============

This component requires Doctrine2.1 at least.

Installation
============

Installation is very quick :)

1. Download GosDoctrineQueryBuilder
2. Configure doctrine2 (optional)

Step 1 : Download GosDoctrineQueryBuilder use [composer](https://getcomposer.org/)
==================================================================================

```bash
php composer.phar require gos/doctrine-query-builder "~1.0"
```

then `composer update`

Step 2 : Configure Doctrine2 (optional)
=======================================

By default all doctrine2 repository extend of [EntityRepository](http://www.doctrine-project.org/api/orm/2.2/class-Doctrine.ORM.EntityRepository.html). This will replace this extend by [GosEntityRepository](https://github.com/GeniusesOfSymfony/DoctrineQueryBuilder/blob/master/GosEntityRepository.php) wich provide `loadQueryBuilder` method, to retrieve your prepolated query builder. We let you free to use it, or implement your own to retrieve your QueryBuilder. See [usage.md](usage.md) to load QueryBuilder wihout our repository.

```php
<?php

use Doctrine\ORM\Configuration;

$config = new Configuration;
$config->setDefaultRepositoryClassName('Gos\\Component\\DoctrineQueryBuilder\\GosEntityRepository');

$em = EntityManager::create($connectionOptions, $config);
```

### Symfony2 integration

```yml
doctrine:
    orm:
        default_repository_class: Gos\Component\DoctrineQueryBuilder\GosEntityRepository
```

Next step
=========

* [Use GosQueryBuilder](usage.md)







