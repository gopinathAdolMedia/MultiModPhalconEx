<?php

	namespace MultiMod\Backend\Controllers;

	
	// class RolesController extends \BaseController {
	class RolesController extends \BackendController {

		public function initialize() {
		
			// $scope = "backend";
			
			$clearanceLevel = 10;
			
			$strict = false;
			
			// $this->initializeSettings($scope, $clearanceLevel, $strict);
			$this->initializeSettings($clearanceLevel, $strict);

		}
		
		public function indexAction() {
		
			$this->auth->checkPermission();
			
			$rolesData = new \RolesModel();
			$rolesDetails = $rolesData->find();
			
			$sendingVariables = array(
				"pageName" => "User Roles",
				"roleList" => $rolesDetails,
			);
			$this->view->setVars($sendingVariables);
			
		}
		
		public function addNewAction() {
		
			$this->auth->checkPermission();
			
			$sendingVariables = array(
				"pageName" => "Add New User Role"
			);
			$this->view->setVars($sendingVariables);
			$this->tag->setDefault("role_can_delete", "1");
			$this->tag->setDefault("role_clearance_level", "1");
			
		}
		
		public function insertRoleAction() {
		
			$this->auth->checkPermission();
			
			if($this->request->isPost()) {
				if($this->security->checkToken()) {
					
					$temp_roleName         = trim($this->request->getPost('role_name', 'string'));
					$temp_description      = trim($this->request->getPost('role_description', 'string'));
					$temp_can_delete       = trim($this->request->getPost('role_can_delete', 'int'));
					$role_clearance_level  = trim($this->request->getPost('role_clearance_level', 'int'));
					
					if(($temp_roleName == '') || ($temp_can_delete == '') || ($role_clearance_level == '')) {
						$returnData['status']  = 'Failure';
						$returnData['details'] = 'Empty Values in the POST data.';
					} elseif (($temp_can_delete != '0') && ($temp_can_delete != '1')) {
						$returnData['status']  = 'Failure';
						$returnData['details'] = "Invalid Entry  in the POST data ('can_delete').";
					} elseif (($role_clearance_level < 0) || ($role_clearance_level > 10)) {
						$returnData['status']  = 'Failure';
						$returnData['details'] = "Invalid Entry  in the POST data ('role_clearance_level').";
					} else {
					
						$rolesData = new \RolesModel();
						
						$insertData = array(
							'role_name'        => $temp_roleName,
							'description'      => $temp_description,
							'can_delete'       => $temp_can_delete,
							'clearance_level'  => $role_clearance_level
						);
						
						$createStatus = $rolesData->create($insertData);
						
						 if ($createStatus) {
							$returnData['status']  = 'Success';
							$returnData['details'] = 'Role added Successfully.';
						} else {
							$errMsg = "Sorry, the following problems were generated: <br/>";
							foreach ($rolesData->getMessages() as $message) {
								$errMsg .= $message->getMessage() . "<br/>";
							}
							$returnData['status']  = 'Failure';
							$returnData['details'] = $errMsg;
						}
					}
				} else {
					$returnData['status']  = 'Failure';
					$returnData['details'] = 'A possible Attempt of CSRF Attack!';
				}
			} else {
				$returnData['status']  = 'Failure';
				$returnData['details'] = 'There is no POST data.';
			}
			echo json_encode($returnData);
			$this->view->disable();
			
		}

	}

?>