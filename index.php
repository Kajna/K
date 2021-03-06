<?php
/*
| K
|--------------------------------------------------------------------------
| The MIT License (MIT)
|
| Copyright (c) 2016 Milos Kajnaco <milos@caenazzo.com>
| 
| Permission is hereby granted, free of charge, to any person obtaining a copy
| of this software and associated documentation files (the "Software"), to deal
| in the Software without restriction, including without limitation the rights
| to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
| copies of the Software, and to permit persons to whom the Software is
| furnished to do so, subject to the following conditions:
|
| The above copyright notice and this permission notice shall be included in
| all copies or substantial portions of the Software.
|
| THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
| IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
| FITNESS FOR A PARTICULAR PURPOSE AND NONINFINGEMENT. IN NO EVENT SHALL THE
| AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
| LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
| OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
| THE SOFTWARE.
|
| @author Milos Kajnaco <milos@caenazzo.com>
| @link http://k-phpframework.com
*/
use Core\Core\Core;
use Core\Container\Container;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
/*
|--------------------------------------------------------------------------
| App mode
|--------------------------------------------------------------------------
*/
define('APP_MODE', 'debug');
/*
|--------------------------------------------------------------------------
| Register the composer auto loader
|--------------------------------------------------------------------------
*/
require __DIR__ . '/vendor/autoload.php';
/*
|--------------------------------------------------------------------------
| Create core application class instance
|--------------------------------------------------------------------------
*/
$container = new Container(__DIR__ . '/App');
/*
|--------------------------------------------------------------------------
| Register error handler
|--------------------------------------------------------------------------
*/
switch (APP_MODE) {
	case 'debug':
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		break;
	case 'production':
	default:
		ini_set('display_errors', 'Off');
		error_reporting(0);
		break;
}
/*
|--------------------------------------------------------------------------
| Create core application class instance
|--------------------------------------------------------------------------
*/
$app = Core::getInstance($container);
/*
|--------------------------------------------------------------------------
| Controllers base prefix
|--------------------------------------------------------------------------
*/
\Core\Routing\Router::$CONTROLLERS_ROOT = '';
/*
|--------------------------------------------------------------------------
| Hooks
|--------------------------------------------------------------------------
*/
$app->setHook('internal.error', (new App\Hooks\InternalErrorHook())->setContainer($container));
/*
|--------------------------------------------------------------------------
| Middleware
|--------------------------------------------------------------------------
*/
$app->addMiddleware(new App\Middleware\SessionMiddleware($container))
	->addMiddleware(new App\Middleware\RegistryMiddleware($container));
/*
|--------------------------------------------------------------------------
| Execute application
|-------------------------------------------------------------------------- 
*/
$app->execute();
