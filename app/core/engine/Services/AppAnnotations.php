<?php

	// Setting up the Data Annotations
	
	
	use Phalcon\Annotations\Adapter\Memory as AnnotationsMemory;
	
	
	class AppAnnotations {
	
		public function __construct() {
		
		}
		
		public function initAnnotations($di, $config) {
		
			$di->set('annotations', function () use ($config) {
			
				if (!PHALCON_DEBUG) {
					$annotationsAdapter = '\Phalcon\Annotations\Adapter\\' . $config->annotations->adapter;
					$adapter = new $annotationsAdapter($config->annotations->toArray());
				} else {
					$adapter = new AnnotationsMemory();
				}

				return $adapter;
				
			}, true);

		}
	
	}
?>