<?php

	namespace MultiMod\Backend\Controllers;

	
	// class UsersController extends \BaseController {
	class UsersController extends \BackendController {

		public function initialize() {
			
			// $scope = "backend";
			
			$clearanceLevel = 10;
			
			$strict = false;
			
			// $this->initializeSettings($scope, $clearanceLevel, $strict);
			$this->initializeSettings($clearanceLevel, $strict);
			
		}
		
		public function indexAction() {
		
			/* $exceptionArray = array(
				'clearance_levels' => array(),
				'roles' => array(),
				'users' => array(),
			);
			$this->auth->checkPermission($exceptionArray); */
			
			$this->auth->checkPermission();
			
			$usersData = new \UsersModel();
			$usersDetails = $usersData->find();
			
			$sendingVariables = array(
				"pageName" => "Users",
				"userList" => $usersDetails
			);
			$this->view->setVars($sendingVariables);
			
		}
		
		public function addNewAction() {
		
			$this->auth->checkPermission();
			
			$simpleStartPar = $this->router->getParams();
			$uriAssoc =$this->commonFnHandler->urlToAssocConverter($simpleStartPar);
			
			$sendingVariables = array(
				"pageName" => "Add New User"
			);
			$this->view->setVars($sendingVariables);
			
			$rolesData = new \RolesModel();
			$searchOptions = array(
				"default_role = :default_role:",
				"bind" => array(
					"default_role" => 1
				)
			);
			
			$roleDefaultSelect = $rolesData->findFirst($searchOptions);
			if($roleDefaultSelect->count() > 0) {
				$this->tag->setDefault("user_role_select", $roleDefaultSelect->role_id);
			} else {
				$roleDefaultSelect = $rolesData->findFirst();
				$this->tag->setDefault("user_role_select", $roleDefaultSelect->role_id);
			}
			
		}
		
		public function insertUserAction() {
		
			$this->auth->checkPermission();
			
			if($this->request->isPost()) {
				if($this->security->checkToken()) {
					
					$temp_userName      = trim($this->request->getPost('user_name', 'string'));
					$temp_email         = trim($this->request->getPost('user_email', 'string'));
					$temp_passw1        = trim($this->request->getPost('user_passw1', 'string'));
					$temp_passw2        = trim($this->request->getPost('user_passw2', 'string'));
					$temp_display_name  = trim($this->request->getPost('user_display_name', 'string'));
					$temp_user_role     = trim($this->request->getPost('user_role_select', 'int'));
					
					$name_verify   = strpos($temp_userName, " ");
					$email_verify  = preg_match('/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i', $temp_email);
					
					if(($temp_userName == '') || ($temp_email == '') || ($temp_passw1 == '')) {
						$returnData['status']  = 'Failure';
						$returnData['details'] = 'Empty Values in the POST data.';
					} elseif ($temp_passw1 !== $temp_passw2) {
						$returnData['status']  = 'Failure';
						$returnData['details'] = 'Password Values Mismatch.';
					} elseif ($name_verify !== FALSE) {
						$returnData['status']  = 'Failure';
						$returnData['details'] = "The USER NAME Field should have only a single value!";
					} elseif (!$email_verify) {
						$returnData['status']  = 'Failure';
						$returnData['details'] = "Invalid Entry  in the POST data ('user_email').";
					} else {
					
						$user_role = ($temp_user_role == '') ? 4 : $temp_user_role;
						$display_name = ($temp_display_name == '') ? $temp_userName : $temp_display_name;
						$encoded_password = md5($temp_passw1);
						
						$insertData = array(
							'role_id'         => $user_role,
							'email'           => $temp_email,
							'username'        => $temp_userName,
							'password'        => $encoded_password,
							'created_at'      => date('Y-m-d h:i:s e'),
							'display_name'    => $display_name
						);
						
						$usersData = new \UsersModel();
						$createStatus = $usersData->create($insertData);
						
						 if ($createStatus) {
							$returnData['status']  = 'Success';
							$returnData['details'] = 'User added Successfully.';
						} else {
							$errMsg = "Sorry, the following problems were generated: <br/>";
							foreach ($usersData->getMessages() as $message) {
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