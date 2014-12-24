<?php

	/**
	 * The Constants that are used in the Application
	 */
	 

	/**
	 * The Application Environment
	 */	
	define('MODE_PRODUCTION',  'production');
	define('MODE_STAGING',     'staging');
	define('MODE_TESTING',     'testing');
	define('MODE_DEVELOPMENT', 'development');
	 

	/**
	 * The Application Directories
	 */	
	define('APP_PATH',        ROOT_PATH . 'app' . DS);
	define('APP_VAR_PATH',    ROOT_PATH . 'var' . DS);
	
	define('APP_ASSETS_PATH', PUBLIC_PATH . 'assets' . DS);
	define('APP_MEDIA_PATH',  PUBLIC_PATH . 'media' . DS);
	
	define('APP_CONFIG_PATH', APP_PATH . 'config' . DS);
	define('APP_MODULE_PATH', APP_PATH . 'modules' . DS);
	define('APP_DESIGN_PATH', APP_PATH . 'design' . DS);
	define('CORE_APP_PATH',   APP_PATH . 'core' . DS);
	
	define('CORE_BASE_PATH',    CORE_APP_PATH . 'base' . DS);
	define('CORE_PLUGIN_PATH',  CORE_APP_PATH . 'plugins' . DS);
	define('CORE_LANG_PATH',    CORE_APP_PATH . 'languages' . DS);
	define('CORE_MODULE_PATH',  CORE_APP_PATH . 'modules' . DS);
	define('CORE_LIBRARY_PATH', CORE_APP_PATH . 'libraries' . DS);
	define('CORE_ENGINE_PATH',  CORE_APP_PATH . 'engine' . DS);
	
	define('CORE_ENGINE_CLASS_PATH',   CORE_ENGINE_PATH . 'Classes' . DS);
	define('CORE_ENGINE_SERVICE_PATH', CORE_ENGINE_PATH . 'Services' . DS);
	
	define('CORE_BASE_MODEL_PATH',      CORE_BASE_PATH . 'models' . DS);
	define('CORE_BASE_CONTROLLER_PATH', CORE_BASE_PATH . 'controllers' . DS);
	
	define('APP_LOG_PATH',   APP_VAR_PATH . 'logs' . DS);
	define('APP_CACHE_PATH', APP_VAR_PATH . 'cache' . DS);
	
	define('APP_UPLOAD_PATH', APP_MEDIA_PATH . 'uploads' . DS);
	
	
	

?>