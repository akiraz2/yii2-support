# Yii2 Support

Yii2 Support Ticket System

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist powerkernel/yii2-support "dev-master"
```

or add

```
"powerkernel/yii2-support": "dev-master"
```

to the require section of your `composer.json` file.

### Console Migrations

Add path to `console/config/main.php`
```
'controllerMap' => [        
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => null,
            'migrationNamespaces' => [
                //'console\migrations',                
                'powerkernel\support\migrations'
            ],
        ],
    ],
```

Then run console command    
```
php yii migrate
```


## Usage


## Translations

```
yii message/extract @vendor/powerkernel/yii2-support/messages/config.php
```

