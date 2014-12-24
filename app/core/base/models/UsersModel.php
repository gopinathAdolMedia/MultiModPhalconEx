<?php

	use \Phalcon\Db\RawValue;
	use \Phalcon\Mvc\Model\Validator\Uniqueness;
	
	
	class UsersModel extends \BasicModel {
		
		public function initialize() {
		
			parent::initializeModel("users");
			
			$this->belongsTo("role_id", "RolesModel", "role_id", array( "foreignKey" => true));
			
			$this->role_id     = new RawValue('default');
			$this->logged_in   = new RawValue('default');
			$this->last_login  = new RawValue('default');
			$this->last_ip     = new RawValue('default');
			$this->created_at  = new RawValue('default');
			$this->deleted     = new RawValue('default');
			$this->banned      = new RawValue('default');
			$this->language    = new RawValue('default');
			$this->active      = new RawValue('default');
			
		}
		
		public function validation() {
		
			$this->validate(new Uniqueness(
				array(
					"field" => "username",
					"message" => "The Name of the User must be UNIQUE"
				)
			));
			
			$this->validate(new Uniqueness(
				array(
					"field" => "email",
					"message" => "The Email of the Role must be UNIQUE"
				)
			));
			
			return $this->validationHasFailed() != true;
			
		}
		
		public function beforeCreate() {
		
			if(!$this->role_id) {
				$this->role_id = new RawValue('default');
			}
			
			if(!$this->logged_in) {
				$this->logged_in = new RawValue('default');
			}
			
			if(!$this->last_login) {
				$this->last_login = new RawValue('default');
			}
			
			if(!$this->last_ip) {
				$this->last_ip = new RawValue('default');
			}
			
			if(!$this->created_at) {
				$this->created_at = new RawValue('default');
			}
			
			if(!$this->deleted) {
				$this->deleted = new RawValue('default');
			}
			
			if(!$this->banned) {
				$this->banned = new RawValue('default');
			}
			
			if(!$this->language) {
				$this->language = new RawValue('default');
			}
			
			if(!$this->active) {
				$this->active = new RawValue('default');
			}
			
		}
		
		public function afterCreate() {
		
			$this->logDbActivities("A new User '" . $this->username . "' as '" . $roleDetails->role_name . "' has been created.");
			
		}
		
		public function afterUpdate() {
		
			$this->logDbActivities("The User '" . $this->username . "' has been updated.");
			
		}

	}

?>