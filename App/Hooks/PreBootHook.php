<?php
namespace App\Hooks;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

define('DEBUG', true);

class PreBootHook
{
	public function execute()
	{
        // Set error reporting.
        if (defined('DEBUG') && DEBUG === true) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);

            $whoops = new Run();
            $whoops->pushHandler(new PrettyPageHandler());
            $whoops->register();
        } else {
            ini_set('display_errors', 'Off');
            error_reporting(0);
        }

        date_default_timezone_set('Europe/Belgrade');
	}
}