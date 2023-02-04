# PHP X-Sendfile
Serve large files using web server with support for laravel

## Some features for X-Sendfile:
- Support **Nginx**, **Apache**, **LiteSpeed**, **Lighttpd**
- Automatic Server Type Detector
- Configurable
- Support Cache
- Set Extra Headers
- Compatible with Laravel

----
I develop in a open-source journey 🚀, I wish I lived in an environment where financial situation was fine and I could only focus on the path, but as you may know, life isn't perfect. <br>So if you end up using my packages, please consider making a donation, any amount would go along way and is much appreciated. 🍺

[![Donate](https://mostafaznv.github.io/donate/donate.svg)](https://mostafaznv.github.io/donate)

----

## Requirements:
- PHP >=7.0.1
- FileInfo Extension

## Installation
Install using composer:
```shell
composer require mostafaznv/php-x-sendfile
```


## Laravel (Optional) 
1. ##### Register Provider and Facade in config/app.php:
> Don't need for Laravel 5.5+

```php
'providers' => [
  Mostafaznv\PhpXsendfile\PhpXsendfileServiceProvider::class,
],

'aliases' => [
  'Recaptcha' => Mostafaznv\PhpXsendfile\Facades\PhpXsendfile::class,
]
```

2. ##### Publish config file:
```shell
php artisan vendor:publish --provider="Mostafaznv\PhpXsendfile\PhpXsendfileServiceProvider"
```

## Usage
```php
<?php

namespace App\Controllers;

use Mostafaznv\PhpXsendfile\PhpXsendfile;

class DownloadController
{
    public function quickExample()
    {
        $path = '/files/Sample.mp4';

        $xSendFile = new PhpXsendfile();
        $xSendFile->download($path);
    }

    public function completeExample()
    {
        $path = '/files/Sample.mp4';
        // or full path
        $path = public_path('files/Sample.mp4');

        // optional
        $config = [
            'server' => null,
            
            'cache'                 => true,
            'cache-control-max-age' => 2592000
        ];

        // set extra headers (optional)
        $headers = [
            'Header-Name' => 'Header-Value' // header('Header-Name: Header-Value')
        ];

        // set downloaded filename (optional, nullable)
        $fileName = 'LargeVideoFile.mp4';

        $xSendFile = new PhpXsendfile($config);
        $xSendFile->setHeader($headers)->download($path, $fileName);
    }
}

```

## Laravel Usage
```php
<?php

namespace App\Http\Controllers;

use Mostafaznv\PhpXsendfile\Facades\PhpXsendfile; // or use PhpXsendfile;

class DownloadController extends Controller
{
    public function quickExample()
    {
        $path = public_path('files/zip.zip');

        PhpXsendfile::download($path);

        // or
        
        app('x-sendfile')->download($path);
    }

    public function completeExample()
    {
        $path = public_path('files/zip.zip');

        // set extra headers (optional)
        $headers = [
            'Header-Name' => 'Header-Value' // header('Header-Name: Header-Value')
        ];

        // set downloaded filename (optional, nullable)
        $fileName = 'LargeVideoFile.mp4';

        PhpXsendfile::setHeader($headers)->download($path, $fileName);
    }
}
```
> Note: to change configuration in laravel, open config/x-sendfile.php and set your own configurations.  


## Config:
| Key                   | Default                    | Type    | Description                                                                                                                       |
|-----------------------|----------------------------|---------|-----------------------------------------------------------------------------------------------------------------------------------|
| server                | null                       | string  | with null value, package will detect server type automatically <br> supported: **Nginx**, **Apache**, **LiteSpeed**, **Lighttpd** |
| base-path             | $_SERVER['DOCUMENT_ROOT']  | string  | defines base path of your project.                                                                                                |
| cache                 | true                       | boolean | enable/disable for caching response                                                                                               |
| cache-control-max-age | 2592000                    | integer | set maximum age of cache                                                                                                          |



## Methods

#### Download

| Argument Index | Argument Name | Default | Type   | Description                                                         |
|----------------|---------------|---------|--------|---------------------------------------------------------------------|
| 0              | file          |         | string | relative (related to project index.php file) or absolute file path  |
| 1              | fileName      | null    | string | user defined file name                                              |

#### setHeader
| Argument Index | Argument Name | Default | Example                            | Type  | Description                                                                 |
|----------------|---------------|---------|------------------------------------|-------|-----------------------------------------------------------------------------|
| 0              | headers       |         | ['Header-Name' => 'Header-Value')] | array | Key-Value array. <br> **key** is header name <br> **value** is header value |

----
I develop in a open-source journey 🚀, I wish I lived in an environment where financial situation was fine and I could only focus on the path, but as you may know, life isn't perfect. <br>So if you end up using my packages, please consider making a donation, any amount would go along way and is much appreciated. 🍺

[![Donate](https://mostafaznv.github.io/donate/donate.svg)](https://mostafaznv.github.io/donate)

----

#### Credit and Thanks
this package inspired by [songlipeng2003's x-sendfile](https://github.com/songlipeng2003/php-x-sendfile).

## Changelog
Refer to the [Changelog](CHANGELOG.md) for a full history of the project.

## License
This software released under [Apache License Version 2.0](LICENSE).

(c) 2020 Mostafaznv, All rights reserved.


-----
### Sponsors

[![JetBrains Logo (Main) logo](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://jb.gg/OpenSourceSupport)
