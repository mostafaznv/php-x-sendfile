<?php

namespace Mostafaznv\PhpXsendfile\Facades;

use Illuminate\Support\Facades\Facade;
use Mostafaznv\PhpXsendfile\PhpXsendfile as PhpXsendfileInstance;


/**
 * PhpXsendfile Facade
 *
 * @method static void download(string $file, string $fileName = null)
 * @method static self setHeader(array $headers)
 *
 * @package Mostafaznv\PhpXsendfile\Facades
 * @see PhpXsendfileInstance
 */
class PhpXsendfile extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PhpXsendfileInstance::class;
    }
}
