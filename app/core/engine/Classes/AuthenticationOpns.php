<?php

	class AuthenticationOpns {

	
		private $di;
		private $config;
		private $session;
		private $aclSetter;
		private $dispatcher;
		private $router;
		private $response;
		private $flashSession;
		
		private $namespaceName;
		private $moduleName;
		private $controllerName;
		private $actionName;
		private $paramArray;
		private $clearanceLevel;
		
		private static $sessionStatic;
		
		
		public function __construct($di, $dispatcher) {
			
			$this->di = $di;
			$this->dispatcher = $dispatcher;
				
			$this->config        = $this->di->get('config');
			$this->session       = $this->di->getShared('session');
			$this->aclSetter     = $this->di->getShared('aclSetter');
			$this->router        = $this->di->get('router');
			$this->flashSession  = $this->di->get('flashSession');
			$this->response      = $this->di->get('response');
			
			self::$sessionStatic  = $this->di->getShared('session');
			
			$this->namespaceName   = $this->dispatcher->getNamespaceName();
			$this->moduleName      = $this->dispatcher->getModuleName();
			$this->controllerName  = $this->dispatcher->getControllerName();
			$this->actionName      = $this->dispatcher->getActionName();
			$this->paramArray      = $this->dispatcher->getParams();
			
		}

		public function setClearanceSettings($clearanceLevel = '') {
			
			$namespaceName   = $this->dispatcher->getNamespaceName();
			$moduleName      = $this->dispatcher->getModuleName();
			$controllerName  = $this->dispatcher->getControllerName();
			
			$max_clearance_level = \RolesModel::maximum(array("column" => "clearance_level"));
			
			$min_clearance_level = \RolesModel::minimum(array("column" => "clearance_level"));
			
			$default_level = \RolesModel::findFirst("default_role = '1'");
			$this->clearanceLevel = ($default_level) ? ($clearanceLevel == '') ? $default_level->clearance_level : $clearanceLevel : $min_clearance_level;
			
			$aclHandler = new \AclOpns($moduleName);
			
			if(($this->clearanceLevel != '') && ((gettype($this->clearanceLevel) == "integer"))) {
				if(($this->clearanceLevel >= $min_clearance_level) && ($this->clearanceLevel <= $max_clearance_level)) {
					$actionMethodList = array();
					$controllerClassName = $namespaceName . ucfirst($controllerName) . "Controller";
					$methodList = get_class_methods($controllerClassName);
					foreach($methodList as $methodName) {
						if((strlen($methodName) > 6) && (strpos($methodName, 'Action', (strlen($methodName) - 6)) !== FALSE)) {
							$actionMethodList[] = str_replace('Action', '', $methodName);
						}
					}
					
					if(count($actionMethodList) > 0) {
						$aclHandler->moduleResourceAllocation($this->clearanceLevel, $controllerName, $actionMethodList);
					}
				}
			}
			
		}

		public function checkPermission($exceptionArray = FALSE) {
			
			$config        = $this->di->get('config');
			$session       = $this->di->getShared('session');
			$aclSetter     = $this->di->getShared('aclSetter');
			$flashSession  = $this->di->get('flashSession');
			$response      = $this->di->get('response');
			$view          = $this->di->get('view');
			
			$resourceModule      = $this->dispatcher->getModuleName();
			$resourceController  = $this->dispatcher->getControllerName();
			$resourceAction      = $this->dispatcher->getActionName();
				
			$coreModules = include CORE_MODULE_PATH . "core_modules.php";
			
			$configPath = APP_MODULE_PATH;
			
			foreach($coreModules->modules as $coreModuleInst) {
				$activeChecker = include CORE_MODULE_PATH . $coreModuleInst . DS . "config" . DS . "config.php";
				if((strtolower($coreModuleInst) == strtolower($resourceModule)) && $activeChecker->module->active) {
					$configPath = CORE_MODULE_PATH;
					break;
				}
			}
			
			$moduleConfig = include $configPath . $resourceModule . DS . "config" . DS . "config.php";
			
			$resourceUrlSegment  = $moduleConfig->module->urlSegment;
			$resourceUrl = ($resourceUrlSegment == 'default') ? '' : $resourceUrlSegment;
			
			// if($session->get("is_logged") && $session->has("user_id")){
			if($session->get("is_admin_logged") && $session->has("admin_user_id")){
				
				$role_id = $session->get("admin_role_id");
				$roleData = new \RolesModel();
				$roleDetails = $roleData->findFirst("role_id = '$role_id'");
				if(($aclSetter->isRole($roleDetails->role_name) != '') && ($aclSetter->isResource($resourceController)) != '') {
				
					$allowCompare = \Phalcon\Acl::ALLOW;
					
					//Check if the Role have access to the controller (resource)
					$allowed = $aclSetter->isAllowed($roleDetails->role_name, $resourceController, $resourceAction);
					
					$exceptionResult = $this->resolveEcxeptionArray($exceptionArray, $roleDetails);
					if($exceptionResult != '') {
						$allowed = $exceptionResult;
					}
					
					if ($allowed != $allowCompare) {

						//If the Role doesn't have access
						
						$flashSession->error("Access Denied to the requested module.");
						if($resourceController == 'index') {
							$response->redirect('login');
						} else {
							$response->redirect($resourceUrl);
						}
						
						$view->disable();
						
					} else {
						
					}
				} else {
				
					$flashSession->error("Invalid Role/Resource.");
					
				}
			} else {
				
				$flashSession->error("Please Login.");
				$response->redirect('admin/login');
				$view->disable();
				
			}
		
		}
		
		private static function resolveEcxeptionArray($exceptionArray, $roleDetails) {
		
			// Processing the Exception Array
			
			$allowed = '';
			
			if($exceptionArray && is_array($exceptionArray) && (count($exceptionArray) > 0)) {
			
				// Processing the Exception Array for Clearance Levels
				if(array_key_exists('clearance_levels', $exceptionArray) && is_array($exceptionArray['clearance_levels']) && (count($exceptionArray['clearance_levels']) > 0)) {
					foreach($exceptionArray['clearance_levels'] as $clKey => $clValue) {
						$max_clearance_level = \RolesModel::maximum(array("column" => "clearance_level"));
						$min_clearance_level = \RolesModel::minimum(array("column" => "clearance_level"));
						$clIntValue = (int)$clValue;
						// if(($clValue != '') && ((gettype($clValue) == "integer"))) {
						if(($clValue != '') && (($clValue == '0') || ($clIntValue != 0))) {
							if(($clIntValue >= $min_clearance_level) && ($clIntValue <= $max_clearance_level)) {
								if($clIntValue == $roleDetails->clearance_level) {
									if($clIntValue <= $this->clearanceLevel) {
										$allowed = \Phalcon\Acl::ALLOW;
									} else {
										$allowed = \Phalcon\Acl::DENY;
									}
									break;
								}
							}
						}
					}
				}
				
				// Processing the Exception Array for Roles
				if(array_key_exists('roles', $exceptionArray) && is_array($exceptionArray['roles']) && (count($exceptionArray['roles']) > 0)) {
					foreach($exceptionArray['roles'] as $roleKey => $roleValue) {
						if($roleValue != '') {
							$roleDetailsExpId   = $roleData->findFirst("role_id = '$roleValue'");
							$roleDetailsExpName = $roleData->findFirst("role_name = '$roleValue'");
							
							if($roleDetailsExpId && (count($roleDetailsExpId) > 0)) {
								if($roleDetailsExpId->clearance_level == $roleDetails->clearance_level) {
									if($roleDetailsExpId->clearance_level <= $this->clearanceLevel) {
										$allowed = \Phalcon\Acl::ALLOW;
									} else {
										$allowed = \Phalcon\Acl::DENY;
									}
									break;
								}
							
							} elseif(isset($roleDetailsExpName) && (count($roleDetailsExpName) > 0)) {
								if($roleDetailsExpName->clearance_level == $roleDetails->clearance_level) {
									if($roleDetailsExpName->clearance_level <= $this->clearanceLevel) {
										$allowed = \Phalcon\Acl::ALLOW;
									} else {
										$allowed = \Phalcon\Acl::DENY;
									}
									break;
								}
							}
						}
					}
				}
				
				// Processing the Exception Array for Users
				if(array_key_exists('users', $exceptionArray) && is_array($exceptionArray['users']) && (count($exceptionArray['users']) > 0)) {
					foreach($exceptionArray['users'] as $userKey => $userValue) {
						if($userValue != '') {
							$usersData = new \UsersModel();
							$usersDetailsExpId   = $usersData->findFirst("id = '$userValue'");
							$usersDetailsExpMail = $usersData->findFirst("email = '$userValue'");
							$usersDetailsExpName = $usersData->findFirst("username = '$userValue'");
							
							if($usersDetailsExpId && (count($usersDetailsExpId) > 0)) {
								$idRoleDetails = $usersDetailsExpId->getRolesModel();
								if($idRoleDetails->clearance_level == $roleDetails->clearance_level) {
									if($idRoleDetails->clearance_level <= $this->clearanceLevel) {
										$allowed = \Phalcon\Acl::ALLOW;
									} else {
										$allowed = \Phalcon\Acl::DENY;
									}
									break;
								}
							} elseif($usersDetailsExpMail && (count($usersDetailsExpMail) > 0)) {
								$mailRoleDetails = $usersDetailsExpMail->getRolesModel();
								if($mailRoleDetails->clearance_level == $roleDetails->clearance_level) {
									if($mailRoleDetails->clearance_level <= $this->clearanceLevel) {
										$allowed = \Phalcon\Acl::ALLOW;
									} else {
										$allowed = \Phalcon\Acl::DENY;
									}
									break;
								}
							} elseif($usersDetailsExpName && (count($usersDetailsExpName) > 0)) {
								$nameRoleDetails = $usersDetailsExpName->getRolesModel();
								if($nameRoleDetails->clearance_level == $roleDetails->clearance_level) {
									if($nameRoleDetails->clearance_level <= $this->clearanceLevel) {
										$allowed = \Phalcon\Acl::ALLOW;
									} else {
										$allowed = \Phalcon\Acl::DENY;
									}
									break;
								}
							}
						}
					}
				}
			}
			
			return $allowed;
			
		}
		
		public function authorize($remoteIP) {
			
			$session         = $this->di->getShared('session');
			$inlineRegistry  = $this->di->getShared('inlineRegistry');
			
			$scope = $inlineRegistry->getValue('controllerScope');
			$resourceModule  = $this->dispatcher->getModuleName();
			
			$adminExt = "";
			if($scope == 'backendLogin') {
				$adminExt = "admin_";
			} elseif($scope == 'login') {
				$adminExt = "";
			}
				
			$checkUser = new \UsersModel();
			
			$searchOptions = array(
				"logged_in = :logged_in: AND last_ip = :last_ip:",
				"bind" => array(
					"logged_in" => 1,
					"last_ip" => $remoteIP
				)
			);
			
			$userResult   = $checkUser->findFirst($searchOptions);
			
			if($userResult) {
				
				$roleDetails  = $userResult->getRolesModel();
				
				if(($adminExt == "admin_") && ($roleDetails->default_role)) {
					
					$message = "Access to this Area is Denied to the Common Users";
					$authorizationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, $message);
					
				} elseif(($adminExt == "") && ($session->get("is_admin_logged") && ($session->get("admin_user_id") == $userResult->id))) {
					
					$authorizationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, false);
					
				} else {
				
					$authorizationResult = $this->setValidAuthenticationOpn($adminExt, $session, $remoteIP, $userResult, 'authorize');
					
				}
				
			} else {
			
				$authorizationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, false);
				
			}
			
			return $authorizationResult;
			
		}
		
		public function authenticate($userName, $passWord, $remoteIP) {
		
			$session         = $this->di->getShared('session');
			$inlineRegistry  = $this->di->getShared('inlineRegistry');
			
			$scope = $inlineRegistry->getValue('controllerScope');
			$resourceModule  = $this->dispatcher->getModuleName();
			
			$refinedUsername = trim($userName);
			$refinedPassword = trim($passWord);
			
			$adminExt = "";
			if($scope == 'backendLogin') {
				$adminExt = "admin_";
			} elseif($scope == 'login') {
				$adminExt = "";
			}
				
			if(($refinedUsername != '') && ($refinedPassword != '')){
			
				$checkUser = new \UsersModel();
				
				$searchOptions = array(
					"username = :user_name:",
					"bind" => array(
						"user_name" => $refinedUsername
					)
				);
				
				$userResult   = $checkUser->findFirst($searchOptions);
				
				if ($userResult) {
				
					$roleDetails  = $userResult->getRolesModel();
					
					$encryptedPassword = md5($refinedPassword);
					
					$compare_password = $userResult->password;
						
					if($encryptedPassword != $compare_password) {
					
						$message = "Invalid Username/Password.";
						$authenticationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, $message);
						
					} elseif($userResult->logged_in) {
					
						$message = $roleDetails->role_name . " '$refinedUsername' already logged in at '" . $userResult->last_ip . "'.";
						$authenticationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, $message);
						
					} elseif($userResult->deleted) {
					
						$message = $roleDetails->role_name . " '$refinedUsername' credentials deleted.";
						$authenticationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, $message);
						
					} elseif($userResult->banned) {
					
						$message = $roleDetails->role_name . " '$refinedUsername' credentials banned.";
						$authenticationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, $message);
						
					} elseif(!$userResult->active) {
					
						$message = $roleDetails->role_name . " '$refinedUsername' is not active.";
						$authenticationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, $message);
						
					} elseif(($adminExt == "admin_") && ($roleDetails->default_role)) {
					
						$message = "Access to this Area is Denied to the Common Users";
						$authenticationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, $message);
						
					} else {
					
						$authenticationResult = $this->setValidAuthenticationOpn($adminExt, $session, $remoteIP, $userResult, 'authenticate');
						
					}
					
				} else {
				
					$message = "Invalid Username/Password.";
					$authenticationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, $message);
					
				}
				
			} else {
			
				$message = "Invalid Input.";
				$authenticationResult = $this->setInvalidAuthenticationOpn($adminExt, $session, $message);
				
			}
			
			return $authenticationResult;
			
		}
		
		public function signout($user_id = '') {
			
			$session         = $this->di->getShared('session');
			$siteLog         = $this->di->getShared('siteLog');
			$inlineRegistry  = $this->di->getShared('inlineRegistry');
			
			$scope = $inlineRegistry->getValue('controllerScope');
			$resourceModule  = $this->dispatcher->getModuleName();
			
			$adminExt = "";
			if($scope == 'backendLogin') {
				$adminExt = "admin_";
			} elseif($scope == 'login') {
				$adminExt = "";
			}
			
			if($user_id == '')
				$user_id = $session->get($adminExt . "user_id");
			
			$checkUser = new \UsersModel();
			$searchOptions = array(
				"id = :user_id:",
				"bind" => array(
					"user_id" => $user_id
				)
			);
			$userResult = $checkUser->findFirst($searchOptions);
			
			if($userResult) {
			
				$roleDetails = $userResult->getRolesModel();
				
				$updateData = array(
					"logged_in"  => 0
				);
				$updateStatus = $userResult->update($updateData);
				
				$message = $roleDetails->role_name . " '" . $userResult->username . "' logged out.";
				$authorResult = $this->setInvalidAuthenticationOpn($adminExt, $session, $message);
				
				if(!$authorResult) {
				
					$site = ($adminExt == "admin_") ? "Backend" : "Frontend";
					$siteLog->log("The " . $roleDetails->role_name . " '" . $userResult->username . "' Logged out of $site Site from IP '" . $userResult->last_ip . "'.");
					$signoutResult = true;
					
				} else {
					
					$signoutResult = false;
					
				}
				
			} else {
			
				$signoutResult = false;
				
			}
			
			return $signoutResult;
			
		}
		
		private function setInvalidAuthenticationOpn($adminExt, $session, $message) {
		
			$flashSession    = $this->di->get('flashSession');
			
			$session->remove($adminExt . "user_id");
			$session->remove($adminExt . "role_id");
			$session->remove($adminExt . "clearance_level");
			$session->remove($adminExt . "logged_at");
			$session->set("is_" . $adminExt . "logged", FALSE);
			
			if($message)
				$flashSession->error($message);
				
			return false;
		
		}
		
		private function setValidAuthenticationOpn($adminExt, $session, $remoteIP, $userResult, $mode) {
		
			$siteLog      = $this->di->getShared('siteLog');
			$roleDetails  = $userResult->getRolesModel();
			
			$authid = $userResult->id;
			$roleid = $userResult->role_id;
			$roleCL = $roleDetails->clearance_level;
		
			$updateData = array(
				"logged_in"  => 1,
				"last_login" => date('Y-m-d h:i:s e'),
				"last_ip"    => $remoteIP
			);
			
			$updateStatus = $userResult->update($updateData);
			
			if($mode == 'authenticate') {
				$messageExt = "";
			} elseif($mode == 'authorize') {
				$messageExt = "(Again)";
			}
			
			$site = ($adminExt == "admin_") ? "Backend" : "Frontend";
			
			$session->set("is_" . $adminExt . "logged", TRUE);
			$session->set($adminExt . "user_id", $authid);
			$session->set($adminExt . "role_id", $roleid);
			$session->set($adminExt . "clearance_level", $roleCL);
			$session->set($adminExt . "logged_at", date('Y-m-d h:i:s'));
			
			$message = "The " . $roleDetails->role_name . " '" . $userResult->username . "' Logged in on $site Site" . $messageExt . " at IP '$remoteIP'.";
			$siteLog->log($message);
			
			return true;
		
		}
		
		public static function is_loggedIn() {
		
			$session = self::$sessionStatic;
			
			if($session->get("is_logged") && $session->has("user_id")){
				$returnData = TRUE;
			} else {
				$returnData = FALSE;
			}
			
			return $returnData;
			
		}
		
		public static function is_adminLoggedIn() {
		
			$session = self::$sessionStatic;
			$scope = \Application::inlineRegistry('controllerScope');
			
			if(($scope == 'backend') || ($scope == 'backendLogin')) {
			
				if($session->get("is_admin_logged") && $session->has("admin_user_id")){
					$returnData = TRUE;
				} else {
					$returnData = FALSE;
				}
				
			} else {
			
				$returnData = FALSE;
				
			}
			
			return $returnData;
			
		}
		
		public static function getLoggedUserDetails() {
		
			$session = self::$sessionStatic;
			
			if($session->get("is_logged") && $session->has("user_id")) {
			
				$user_id = $session->get("user_id");

				$checkUser = new \UsersModel();
				$userResult = $checkUser->findFirst("id = '$user_id'");
				$roleDetails = $userResult->getRolesModel();
				
				$userDetailsArray = $userResult->toArray();
				$roleDetailsArray = $roleDetails->toArray();
				
				$returnData['id']                    = $userDetailsArray['id'];
				$returnData['role_id']               = $userDetailsArray['role_id'];
				$returnData['email']                 = $userDetailsArray['email'];
				$returnData['username']              = $userDetailsArray['username'];
				$returnData['password']              = $userDetailsArray['password'];
				$returnData['logged_in']             = $userDetailsArray['logged_in'];
				$returnData['last_login']            = $userDetailsArray['last_login'];
				$returnData['last_ip']               = $userDetailsArray['last_ip'];
				$returnData['created_at']            = $userDetailsArray['created_at'];
				$returnData['deleted']               = $userDetailsArray['deleted'];
				$returnData['banned']                = $userDetailsArray['banned'];
				$returnData['ban_message']           = $userDetailsArray['ban_message'];
				$returnData['reset_by']              = $userDetailsArray['reset_by'];
				$returnData['display_name']          = $userDetailsArray['display_name'];
				$returnData['display_name_changed']  = $userDetailsArray['display_name_changed'];
				$returnData['language']              = $userDetailsArray['language'];
				$returnData['active']                = $userDetailsArray['active'];
				
				$roleFilter['role_id']            = $roleDetailsArray['role_id'];
				$roleFilter['role_name']          = $roleDetailsArray['role_name'];
				$roleFilter['description']        = $roleDetailsArray['description'];
				$roleFilter['default_role']       = $roleDetailsArray['default_role'];
				$roleFilter['clearance_level']    = $roleDetailsArray['clearance_level'];
				$roleFilter['can_delete']         = $roleDetailsArray['can_delete'];
				$roleFilter['login_destination']  = $roleDetailsArray['login_destination'];
				$roleFilter['deleted']            = $roleDetailsArray['deleted'];
				
				$returnData['roleDetails'] = $roleFilter;
				
			} else {
			
				$returnData = FALSE;
				
			}
			
			return $returnData;
			
		}
		
		public static function getLoggedAdminDetails() {
		
			$session = self::$sessionStatic;
			$scope = \Application::inlineRegistry('controllerScope');
			
			if(($scope == 'backend') || ($scope == 'backendLogin')) {
			
				if($session->get("is_admin_logged") && $session->has("admin_user_id")) {
				
					$user_id = $session->get("admin_user_id");

					$checkUser = new \UsersModel();
					$userResult = $checkUser->findFirst("id = '$user_id'");
					$roleDetails = $userResult->getRolesModel();
					
					$userDetailsArray = $userResult->toArray();
					$roleDetailsArray = $roleDetails->toArray();
					
					$returnData['id']                    = $userDetailsArray['id'];
					$returnData['role_id']               = $userDetailsArray['role_id'];
					$returnData['email']                 = $userDetailsArray['email'];
					$returnData['username']              = $userDetailsArray['username'];
					$returnData['password']              = $userDetailsArray['password'];
					$returnData['logged_in']             = $userDetailsArray['logged_in'];
					$returnData['last_login']            = $userDetailsArray['last_login'];
					$returnData['last_ip']               = $userDetailsArray['last_ip'];
					$returnData['created_at']            = $userDetailsArray['created_at'];
					$returnData['deleted']               = $userDetailsArray['deleted'];
					$returnData['banned']                = $userDetailsArray['banned'];
					$returnData['ban_message']           = $userDetailsArray['ban_message'];
					$returnData['reset_by']              = $userDetailsArray['reset_by'];
					$returnData['display_name']          = $userDetailsArray['display_name'];
					$returnData['display_name_changed']  = $userDetailsArray['display_name_changed'];
					$returnData['language']              = $userDetailsArray['language'];
					$returnData['active']                = $userDetailsArray['active'];
					
					$roleFilter['role_id']            = $roleDetailsArray['role_id'];
					$roleFilter['role_name']          = $roleDetailsArray['role_name'];
					$roleFilter['description']        = $roleDetailsArray['description'];
					$roleFilter['default_role']       = $roleDetailsArray['default_role'];
					$roleFilter['clearance_level']    = $roleDetailsArray['clearance_level'];
					$roleFilter['can_delete']         = $roleDetailsArray['can_delete'];
					$roleFilter['login_destination']  = $roleDetailsArray['login_destination'];
					$roleFilter['deleted']            = $roleDetailsArray['deleted'];
					
					$returnData['roleDetails'] = $roleFilter;
					
				} else {
				
					$returnData = FALSE;
					
				}
				
			} else {
			
				$returnData = FALSE;
				
			}
			
			return $returnData;
			
		}
		
	}

?>