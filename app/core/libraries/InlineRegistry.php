<?php

	class InlineRegistry {

		
		// private $inlineRegister;
		
		
		public function __construct() {
			
		}
		
		/**
		 * Register a new variable
		 */
		public function registerVariable($key, $value, $strict = false) {
		
			\Application::registerVariable($key, $value, $strict);
			
		}

		/**
		 * Unregister a variable from register by key
		 */
		public function unregisterVariable($key) {
		
			\Application::unregisterVariable($key);
			
		}

		/**
		 * Retrieve a value from registry by a key
		 */
		public function getValue($key = '') {
			
			$registryArray = \Application::inlineRegistry($key);
			
			return $registryArray;
			
		}

	}

?>