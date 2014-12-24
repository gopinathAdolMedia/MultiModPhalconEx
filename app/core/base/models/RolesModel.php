<?php

	use \Phalcon\Acl\Role as AclRole;
	use \Phalcon\Db\RawValue;
	use \Phalcon\Mvc\Model\Validator\Uniqueness;
	

	class RolesModel extends \BasicModel {
		
		public function initialize() {
		
			parent::initializeModel("roles");
			
			$this->hasMany("role_id", "UsersModel", "role_id");
			
			// $this->setup(array("notNullValidations" => FALSE));
			
			$this->default_role         = new RawValue('default');
			$this->clearance_level    	= new RawValue('default');
			$this->can_delete           = new RawValue('default');
			$this->login_destination 	= new RawValue('default');
			$this->deleted          	= new RawValue('default');
			
		}
		
		public function validation() {
		
			$this->validate(new Uniqueness(
				array(
					"field" => "role_name",
					"message" => "The Name of the Role must be UNIQUE"
				)
			));
			
			return $this->validationHasFailed() != true;
			
		}
		
		public function beforeCreate() {
		
			if(!$this->default_role) {
				$this->default_role = new RawValue('default');
			}
			
			if(!$this->clearance_level) {
				$this->clearance_level = new RawValue('default');
			}
			
			if(!$this->can_delete) {
				$this->can_delete = new RawValue('default');
			}
			
			if(!$this->login_destination) {
				$this->login_destination = new RawValue('default');
			}
			
			if(!$this->deleted) {
				$this->deleted = new RawValue('default');
			}
			
		}
		
		public function afterCreate() {
		
			$this->logDbActivities("A new Role '" . $this->role_name . "' with Clearance Level '" . $this->clearance_level . "' has been created.");
			
			$newRoleToACL = new AclRole($this->role_name, $this->description);
			$this->aclSetter->addRole($newRoleToACL);
			
		}
		
		public function afterUpdate() {
		
			$this->logDbActivities("The Role '" . $this->role_name . "' has been updated.");
			
		}

	}

?>