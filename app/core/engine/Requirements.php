<?php 

	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	
	/**
	 * Versions.
	 */
	define('PHP_VERSION_REQUIRED', '5.4.0');
	define('PHALCON_VERSION_REQUIRED', '1.3.0');
	define('PHP_VERSION_CURRENT', PHP_VERSION);
	define('PHALCON_VERSION_CURRENT', Phalcon\Version::get());
	
	

	/**
	 * Check PHP installation.
	 */
	if (version_compare(PHP_VERSION_CURRENT, PHP_VERSION_REQUIRED) < 0) {
		printf('Install PHP at least of version %s', PHP_VERSION_REQUIRED);
		exit(1);
	}

	/**
	 * Check Phalcon PHP Framework installation.
	 */
	if (!extension_loaded('phalcon') || (version_compare(PHALCON_VERSION_CURRENT, PHALCON_VERSION_REQUIRED) < 0)) {
		printf('Install Phalcon PHP Framework v%s', PHALCON_VERSION_REQUIRED);
		exit(1);
	}
	
?>