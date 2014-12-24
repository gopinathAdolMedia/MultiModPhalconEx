<?php

	// ACL Initialization
	
	
	use Phalcon\Acl\Adapter\Memory as AclAdapter;
	use Phalcon\Acl\Role as AclRole;
	
	
	class AppAcl {
	
		public function __construct() {
		
		}
		
		public function initAcl($di, $config) {
		
			$di->set('aclSetter', function () use ($config) {
			
				//Create the ACL
				$acl = new AclAdapter();
				
				//The default action is ALLOW access
				$acl->setDefaultAction(Phalcon\Acl::ALLOW);
				
				$roleData = new \RolesModel();
				$rolesList = $roleData->find();
				$rolesArray = $rolesList->toArray();

				foreach ($rolesArray as $roleDetails) {
					$roleAssign[$roleDetails["role_name"]] = new AclRole($roleDetails["role_name"], $roleDetails["description"]);
				}
				
				foreach ($roleAssign as $role) {
					$acl->addRole($role);
				}
				
				return $acl;
				
			}, true);
			
		}
	
	}

?>