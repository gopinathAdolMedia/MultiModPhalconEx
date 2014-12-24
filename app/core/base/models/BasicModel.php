<?php

	use \Phalcon\Acl\Role as AclRole;
	use \Phalcon\Db\RawValue;
	use \Phalcon\Mvc\Model\Validator\Uniqueness;
	

	class BasicModel extends \Phalcon\Mvc\Model {
		
		public function initialize() {

		}
		
		public function initializeModel($tableName = '', $dbName = 'db') {
		
			// Custom DB Connection
			$this->setConnectionService($dbName);
			
			if($tableName != '') {
			
				//Custom tablename; default tablename is same as model name
				$this->setSource($tableName);
				
			}

		}
		
		protected function getDatabaseName() {
			
			$di     = $this->getDI();
			$config = $di->get('config');
			$connectionService = $this->getWriteConnectionService();
			$connectionString  = ($connectionService == 'db') ? 'default' : $connectionService;
			$targetDb          = $config->database->$connectionString->dbname;
			return $targetDb;
			
		}
		
		protected function getTableName() {
			
			$targetTable = $this->getSource();
			return $targetTable;
			
		}
		
		protected function logDbActivities($message) {
			
			$di    = $this->getDI();
			$dbLog = $di->get('dbLog');
			$targetDb    = ucfirst($this->getDatabaseName());
			$targetTable = ucfirst($this->getTableName());
			$dbLog->info("($targetDb . $targetTable) $message");
			
		}

	}

?>