<?php

	use \Phalcon\Events\Event;
	use \Phalcon\Mvc\Dispatcher;
	

	class AclOpns extends \Phalcon\Mvc\User\Component {

	
		protected $_module;

		
		public function __construct($module) {
		
			$this->_module = $module;
		
		}

		public function moduleResourceAllocation($clearance_level, $resourceName, $aclResourceAction) {

			$di = $this->getDI();
			$aclControl = $di->getShared("aclSetter");
			$aclControl->addResource(new Phalcon\Acl\Resource($resourceName), $aclResourceAction);
			$aclRoles = $aclControl->getRoles();
			foreach ($aclRoles as $aclRole) {
				$searchOptions = array(
					"role_name = :role_name:",
					"bind" => array(
						"role_name" => $aclRole->getName()
					)
				);
				$roleDetails = RolesModel::findFirst($searchOptions);
				if(($roleDetails->clearance_level) >= $clearance_level) {
					$aclControl->allow($aclRole->getName(), $resourceName, $aclResourceAction);
				} else {
					$aclControl->deny($aclRole->getName(), $resourceName, $aclResourceAction);
				}
			}
			
		}

		public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher) {
			
			$resourceModule = $this->_module;
			$resourceController = $dispatcher->getControllerName();
			$resourceAction = $dispatcher->getActionName();
			
		}

	}
	
?>