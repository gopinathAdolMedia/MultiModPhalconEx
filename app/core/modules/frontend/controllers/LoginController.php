<?php

	namespace MultiMod\Frontend\Controllers;
	

	// class LoginController extends \BaseController {
	class LoginController extends \LoginController {

		public function initialize() {
		
			// $scope = "login";
			
			// $this->initializeSettings($scope);
			$this->initializeSettings();
			
		}
		
		public function indexAction() {
		
			$temp_remoteIP = $this->request->getClientAddress();
			
			$authorizationResult = $this->auth->authorize($temp_remoteIP);
			
			if($authorizationResult) {
			
				$this->response->redirect('');
				$this->view->disable();
				
			} else {
			
				$sendingVariables = array(
					"pageName" => "Logon to Website"
				);
				$this->view->setVars($sendingVariables);
				
			}
			
		}

		public function validateAction() {

			if($this->request->isPost()) {
			
				if($this->security->checkToken()) {
				
					$temp_userName = $this->request->getPost('username', 'string');
					$temp_passWord = $this->request->getPost('password', 'string');
					$temp_remoteIP = $this->request->getClientAddress();
					
					$authenticationResult = $this->auth->authenticate($temp_userName, $temp_passWord, $temp_remoteIP);
					
					if($authenticationResult) {
					
						$this->response->redirect('');
						
					} else {
					
						$this->response->redirect('login');
						
					}
					
				} else {
				
					$this->flashSession->error("A possible Attempt of CSRF Attack!");
					$this->response->redirect('login');
					
				}
				
				$this->view->disable();
				
			}
			
		}

		public function logoutAction() {
		
			$user_id = $this->session->get("user_id");
			$signoutResult = $this->auth->signout($user_id);
			
			if($signoutResult) {
				$this->response->redirect('login');
				$this->view->disable();

			}
			
		}

	}

?>