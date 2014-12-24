<?php

	// Database connection is created based in the parameters defined in the configuration file
	 
	 
	use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
	use Phalcon\Db\Profiler as DatabaseProfiler;
	use Phalcon\Mvc\Model\Manager as ModelManager;
	use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
	
	
	class AppDatabase {
	
		public function __construct() {
		
		}
		
		public function initDatabase($di, $config) {
		
			$this->initDbList($di, $config);
			
			$this->initModelsManager($di);
			
			$this->initModelsMetadata($di);
	
		}
		
		private function initDbList($di, $config) {
		
			$databaseList = $config->database;
			$dbDefaultFlag = TRUE;
			
			foreach($databaseList as $dbInstance => $dbDetails) {
			
				$dbProceedFlag = TRUE;
				if((trim(strtolower($dbInstance)) == 'default') || (trim(strtolower($dbInstance)) == 'db')){
					if($dbDefaultFlag){
						$setDbInstance = 'db';
						$dbDefaultFlag = FALSE;
						$dbProceedFlag = TRUE;
					} else {
						$dbProceedFlag = FALSE;
					}
				} else {
					$setDbInstance = $dbInstance;
					$dbProceedFlag = TRUE;
				}
				
				
				$profiler = $di->get('profiler');
				
				if($dbProceedFlag) {
					$di->set($setDbInstance, function () use ($dbDetails, $profiler) {
					
						$dbAdapter = new DbAdapter(array(
							'host' => $dbDetails->host,
							'username' => $dbDetails->username,
							'password' => $dbDetails->password,
							'dbname' => $dbDetails->dbname
						));
						
						$dbProfiler = new DatabaseProfiler();
						if ($dbProfiler) {
							$profiler->setDbProfiler($dbProfiler);
						}
						
						return $dbAdapter;
						
					});
				}
			}
			
		}
		
		private function initModelsManager($di) {
		
			// Managing various Models in the Application
			$di->set('modelsManager', function() {
			
				return new ModelManager();
				
			});
			
		}
		
		private function initModelsMetadata($di) {
		
			/**
			 * If the configuration specify the use of metadata adapter use it or use memory otherwise
			 */
			$di->set('modelsMetadata', function () {
			
				return new MetaDataAdapter();
				
			});
			
		}
	
	}

?>