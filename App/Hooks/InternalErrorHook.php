<?php
namespace App\Hooks;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Core\Container\ContainerAware;

/**
 * Class InternalErrorHook
 */
class InternalErrorHook extends ContainerAware
{
	/**
	 * Set whoops to handle application errors
	 */
	public function execute()
	{
        $this->app['whoops']->handleException($this->app['exception']);
	}
}