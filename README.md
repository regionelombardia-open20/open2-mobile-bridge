Amos Mobile Bridge
-----------------------

Mobile Compatibility Layer for Amos 4

Installation
------------

1. The preferred way to install this extension is through [composer](http://getcomposer.org/download/).
    
    Either run
    
    ```bash
    composer require open20/amos-mobile-bridge
    ```
    
    or add
    
    ```
    "open20/amos-mobile-bridge": "~1.0"
    ```
    
    to the require section of your `composer.json` file.
    
2.  Add module to your main config in common:
        
    ```php
    <?php
    'modules' => [
        'mobilebridge' => [
            'class' => 'open20\amos\mobile\bridge\Module'
        ],
    ],
    ```
    
3. Apply migrations
    
    ```bash
    php yii migrate/up --migrationPath=@vendor/open20/amos-mobile-bridge/src/migrations
    ```