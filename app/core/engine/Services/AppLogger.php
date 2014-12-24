<?php

	// The LOG component is used to log the activities and events occur in the Site
	 
	 
	use Phalcon\Logger\Adapter\File as FileLogAdapter;
	use Phalcon\Logger\Formatter\Line as LineFormatter;
	
	
	class AppLogger {
	
		public function __construct() {
		
		}
		
		public function initLogger($di, $config) {
		
			$loggerList = $config->logger;
			
			foreach($loggerList as $logInstance => $logDetails) {
			
				$di->set($logInstance, function () use ($config, $logDetails) {
				
					$logger = new FileLogAdapter(APP_LOG_PATH . $logDetails->logFile);
					
					$formatter = new LineFormatter($logDetails->logFormat);
					$logger->setFormatter($formatter);

					return $logger;
					
				}, true);
				
			}
		}
	
	}

?>