<?php
		
	/**
	 * Read the Constants
	 */
	require_once ROOT_PATH . 'app' . DS . 'core' . DS . 'engine' . DS . 'Constants.php';
	

	use \Phalcon\Loader;
	use Phalcon\DI\FactoryDefault;
	
	
	class Application extends \Phalcon\Mvc\Application {
		
		private static $appConfig;
		private static $store_code;
		private static $_di;
		private static $_registry = array();
		
		
		public function __construct($store_code = 'default') {
	
			/**
			 * Setting the Sub-Domain
			 */
			self::$store_code = $store_code;
			
			/**
			 * Read the configuration
			 */
			self::$appConfig = require_once APP_CONFIG_PATH . "config.php";
			
			/**
			 * Setting the Application Environment
			 */
			
			if (!defined('PHALCON_MODE')) {
				$mode = getenv('PHALCON_MODE');
				$config = self::$appConfig;
				$mode = $mode ? $mode : $config->application->environment;
				define('PHALCON_MODE', $mode);
			}

			switch (PHALCON_MODE) {
				case MODE_PRODUCTION:
				case MODE_STAGING:
					error_reporting(0);
					define('PHALCON_DEBUG', FALSE);
					break;

				case MODE_TESTING:
				case MODE_DEVELOPMENT:
					error_reporting(E_ALL);
					define('PHALCON_DEBUG', TRUE);
					break;
			}
			
			/**
			 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
			 */
			self::$_di = new FactoryDefault();
			
		}
		
		public function run() {
		
			try {
			
				/**
				 * Setting the Sub-Domain
				 */
				$store_code = self::getAppStoreCode();
				
				/**
				 * Read the configuration
				 */
				$config = self::getAppConfig();
				
				/**
				 * Read the Dependency Injector
				 */
				$di = self::getAppDI();

				/**
				 * Initiate auto-loader
				 */
				$this->initAppLoader();

				/**
				 * Initialize Phalcon PHP Services
				 */
				$services = new ServiceBootstrap($this);
				$services->initServices();
				
				/**
				 * Set the Dependency Injector to the Application
				 */
				$this->setDI($di);
				
				/**
				 * Set the Custom Error Handler Function
				 */
				$old_error_handler = set_error_handler("customErrorLogger", E_ALL);/*  */
				
				/**
				 * Set the Custom Exception Handler Function
				 */
				/* set_exception_handler("customExceptionLogger"); */
				
				/**
				 * Display the Processed Application
				 */
				echo $this->handle()->getContent();

			} catch(\Exception $e) {
				
				\ExceptionLogger::logException($e);
				
			}
			
		}
		
		public static function getAppStoreCode() {
		
			return self::$store_code;
			
		}
		
		public static function getAppConfig() {
		
			return self::$appConfig;
			
		}
		
		public static function getAppDI() {
		
			return self::$_di;
			
		}
		
		/**
		 * We're a registering a set of directories for Autoloading the Classes
		 */
		private function initAppLoader() {

			$loader = new Loader();

			$targerDirs = array(
				CORE_ENGINE_SERVICE_PATH,
				CORE_ENGINE_CLASS_PATH,
				CORE_LIBRARY_PATH,
				CORE_BASE_MODEL_PATH,
				CORE_BASE_CONTROLLER_PATH,
				CORE_PLUGIN_PATH
			);
			
			$loader->registerDirs($targerDirs);
			
			$loader->register();
			
		}
		
		/**
		 * Register a new variable
		 */
		public static function registerVariable($key, $value, $strict = false) {
			
			$registryArray = self::$_registry;
			if(($key == '') || ($value == null) || ($key == '') || ($value == null)) {
				if ($strict === true) {
					self::throwException('Phalcon App registry key/value entry is empty/null value!');
				}
			} else {
				if (array_key_exists($key, $registryArray)) {
					if ($strict === true) {
						self::throwException('Phalcon App registry key "'.$key.'" already exists');
					} else {
						$registryArray[$key] = $value;
					}
				} else {
					$registryArray[$key] = $value;
				}
				self::$_registry = $registryArray;
			}
		}

		/**
		 * Unregister a variable from register by key
		 */
		public static function unregisterVariable($key) {
			$registryArray = self::$_registry;
			if (array_key_exists($key, $registryArray)) {
				if (is_object($registryArray[$key]) && (method_exists($registryArray[$key], '__destruct'))) {
					$registryArray[$key]->__destruct();
				}
				unset($registryArray[$key]);
			}
			self::$_registry = $registryArray;
			
		}

		/**
		 * Retrieve a value from registry by a key
		 */
		public static function inlineRegistry($key = '') {

			$registryArray = self::$_registry;
			if($key == '') {
				return $registryArray;
			} else {
				if (isset($registryArray[$key])) {
					return $registryArray[$key];
				}
				return null;
			}
		}
		
	}
	
		
	function customErrorLogger($errno, $errstr, $errfile, $errline, $errcontext) {
	
		if(!$errno) {
			// This error code is not included in error_reporting
			return;
		}
		
		$exceptionReportArray = array(1, 16, 64, 256, 4096);
		
		if(in_array($errno, $exceptionReportArray)) {
			throw new \Phalcon\Exception($errstr, 0, $errno, $errfile, $errline);
		} else {
			\ExceptionLogger::logError($errno, $errstr, $errfile, $errline);
		}
		
		// throw new ErrorException($errstr, $errno, 8, $errfile, $errline);
		
		// return true;
	}
	
		
	/* function customExceptionLogger(\Exception $ex) {
	
		\ExceptionLogger::logException($ex);
		
	} */
	
?>