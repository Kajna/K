<?php 
namespace Core\Core;
use \Core\Database\Database as Database;
use \Core\Util\Container as Container;
use \Core\Session\Session as Session;

/**
* Core class of Core. This class is a container for all objects
* of application, it is implemented as singleton, also main run method
* with routing, database connection etc. is defined here.
*
* @author Milos Kajnaco <miloskajnaco@gmail.com>
* @see Dependecy Injection Container
* @see Singleton
*/
class Core extends Container
{
    /**
    * Core version.
    * @const string
    */
    const VERSION = '0.97';

    /**
    * Singleton instance of Core.
    * @var object
    */
    private static $instance = null;

    /**
	* Class constructor.
    * Loads all needed classes closures into container.
	**/
   	public function __construct()
    {
        // Call container parent constructor
        parent::__construct();

        // Load configuration
        $this['config'] = require APP.'Config/Config.php';

        // Create request
        $this['request'] = function () {
            return new Request();
        };

        // Create input class.
        $this['input'] = function () {
            return new Input();
        };

        // Create response class.
        $this['response'] = function () {
            return new Response();
        }; 

		// Create router class.
		$this['router'] = function () { 
            return new Router();
        };	

        // Create database class.
        $this['database'] = function() {
            return new Database(require 'App/Config/Database.php');
        };  

        // Create session class.
        $this['session'] = function ($c) {
            return new Session($c['config']['sessionAndCookies']);
        }; 

        // Load modules if enabled in configuration
        if($this['config']['modules']) {
            $this->loadModules();
        }
    }
    
    /**
    * Main executive function of Core class.
    * Function will start session, connect to database, apply hooks,
    * route requests, execute controllers and display response.
    */        
    public function run()
    {
        // Load and start session if enabled in configuration
        if($this['config']['sessionAndCookies']['start']) {
            $this['session'];
        }

        // Collect routes list from file.
        require ROUTES;

        // Pre routing/controller language hook (must be enabled in configuration)
        if($this['config']['languages']) {
            \Util::extractLanguage($this['config']['languages'], $this['request']->getUri());
        }

        // Route requests
        $this['router']->run($this['request']);

        // Post routing/controller hook

        // Write log if enabled in config
        if($this['config']['logWrite']) {
            Error::writeLog();
        }

        // Append log to display if enabled
        if($this['config']['logDisplay']) {
            $this['response']->appendBody(Error::displayLog());
        }

        // Display final response
        $this['response']->display();

        // Display benchmark time if enabled
        if($this['config']['benchmark']) {
            print \PHP_Timer::resourceUsage();
        }
    }  

    /**
    * Get singleton instance of Core class.
    * @return object
    */
    public static function getInstance()
    {
        if (null===self::$instance) {
            self::$instance = new Core();
        }
        return self::$instance;
    }

    /**
    * Load modules into application container.
    */
    private function loadModules()
    {
        $modules = require 'App/Config/Modules.php';
        foreach ($modules as $module => $value) {
            if($value) {
                $class = '\\Core\Modules\\'.$module.'\\'.$module;
                $this['tmp'] = $class;
                $this[$module] = function ($c) {
                    return new $c['tmp']();
                };
            } 
        }
    }
}