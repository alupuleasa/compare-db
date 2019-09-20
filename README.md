# Compare DB
Php Database Comparison Tool


Use example:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use CompareDB\CompareDB as CompareDB;

$DB1 = new stdClass(); 
$DB1->host   = "*******"; 
$DB1->dbname = "*******"; 
$DB1->user   = "*******";
$DB1->passw  = "*******";

$DB2 = new stdClass(); 
$DB2->host   = "*******"; 
$DB2->dbname = "*******";
$DB2->user   = "*******";
$DB2->passw  = "*******"; 

$compareDb = new CompareDB($DB1,$DB2);
$compareDb->showDiferences();
//$compareDb->showStructures();
```
Import with Composer

Load from packagist
```composer log
{
    "minimum-stability": "dev",
    "require": {
        "alupuleasa/compare-db":"1.0"
    },
    "autoload": {
        "psr-4": {
            "CompareDB\\":"src"
        }
    }
}
```

Load from github
```composer log
{
    "repositories": {
        "alupuleasa/compare-db":{
            "url": "git@github.com:alupuleasa/compare-db.git",
            "type" : "git"
        }
    },
    "autoload": {
        "psr-4": {
            "CompareDB\\":"src"
        }
    }
}
```
Load from fork
```composer log
{
    "repositories": [  
        {  
            "type": "vcs",  
            "url": "https://xxxxxxxxxxxxx.com"  
        }  
    ],
    "autoload": {
        "psr-4": {
            "CompareDB\\":"src"
        }
    }
}
```
