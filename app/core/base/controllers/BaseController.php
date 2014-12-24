<?php

	class BaseController extends \Phalcon\Mvc\Controller {

		private $config;
		private $session;
		private $inlineRegistry;
		private $currentStore;
		private $aclSetter;
		private $router;
		private $view;
		private $commonView;
		private $response;
		private $flashSession;
		
		private $namespaceName;
		private $moduleName;
		private $controllerName;
		private $actionName;
		private $paramArray;
		
		protected $aclHandler;
		protected $commonFnHandler;
		protected $auth;
		
		private $scopeList;
		
		public function initialize() {
		
		}
		
		public function initializeSettings($scope = '', $clearanceLevel = '', $strict = FALSE) {
		
			$this->config          = $this->di->get('config');
			$this->session         = $this->di->getShared('session');
			$this->inlineRegistry  = $this->di->getShared('inlineRegistry');
			$this->currentStore    = $this->di->getShared('storeManager');
			$this->aclSetter       = $this->di->getShared('aclSetter');
			$this->router          = $this->di->get('router');
			$this->view            = $this->di->get('view');
			$this->commonView      = $this->di->get('commonView');
			$this->flashSession    = $this->di->get('flashSession');
			$this->response        = $this->di->get('response');
			
			$this->namespaceName   = $this->dispatcher->getNamespaceName();
			$this->moduleName      = $this->dispatcher->getModuleName();
			$this->controllerName  = $this->dispatcher->getControllerName();
			$this->actionName      = $this->dispatcher->getActionName();
			$this->paramArray      = $this->dispatcher->getParams();
			
			$this->aclHandler       = new \AclOpns($this->moduleName);
			$this->auth             = new \AuthenticationOpns($this->di, $this->dispatcher);
			$this->commonFnHandler  = new \CommonFns($this->di, $this->dispatcher);
			
			$this->scopeList = array(
				'default' => 'frontend',
				'backend'
			);
			
			\CommonFns::getRenderStartTime();
			
			if(strtolower($scope) === 'login') {
			
				$tempScope = 'frontend';
				
			} elseif(strtolower($scope) === 'backendlogin') {
			
				$tempScope = 'backend';
				
			} else {
			
				$tempScope = strtolower($scope);
				
			}
			
			$revisedLocation = (in_array($tempScope, $this->scopeList)) ? $tempScope : $this->scopeList['default'];
			
			if(strtolower($scope) === 'login') {
			
				$revisedScope = 'login';
				
			} elseif(strtolower($scope) === 'backendlogin') {
			
				$revisedScope = 'backendLogin';
				
			} else {
			
				$revisedScope = $revisedLocation;
				
			}
			
			$viewDir = $this->view->getViewsDir();
			
			$coreDirCheck = strpos($viewDir, 'core' . DS . 'modules');

			$dirCheckResult = (!($coreDirCheck === false)) ? "/core/" : "/";
			
			$currentTheme  = $this->currentStore->getCurrentStoreTheme();
			
			$publicPath = "assets/base/";
			
			$themePath  = "assets/$currentTheme/$revisedLocation/";
			
			$modulePath = "../app" . $dirCheckResult . "modules/" . $this->moduleName . "/assets/";
			
			$assetVariables = array(
			
				'publicCss'         => 'publicCssCollection',
				'publicJs'          => 'publicJsCollection',
				'publicAssetsPath'  => $publicPath,
				
				'themeCss'          => 'themeCssCollection',
				'themeJs'           => 'themeJsCollection',
				'themeAssetsPath'   => $themePath,
				
				'moduleCss'         => 'moduleCssCollection',
				'moduleJs'          => 'moduleJsCollection',
				'moduleAssetsPath'  => $modulePath,
				
			);
			
			$this->inlineRegistry->registerVariable('controllerScope', $revisedScope);
			
			$this->inlineRegistry->registerVariable('assets', $assetVariables);
			
			$this->view->setTemplateAfter('layout');
			
			if($revisedScope == 'backend') {
				
				$default_level = \RolesModel::findFirst("default_role = '1'");
				
				$max_clearance_level = \RolesModel::maximum(array("column" => "clearance_level"));
				
				$min_clearance_level = \RolesModel::minimum(array("column" => "clearance_level"));
				
				if(($clearanceLevel != '') && ((gettype($clearanceLevel) == "integer"))) {
					
					if(($clearanceLevel >= $min_clearance_level) && ($clearanceLevel <= $max_clearance_level)) {
					
						$finalClearanceLevel = $clearanceLevel;
						
					} elseif($clearanceLevel < $min_clearance_level) {
					
						$finalClearanceLevel = $min_clearance_level;
						
					} elseif($clearanceLevel > $max_clearance_level) {
					
						$finalClearanceLevel = $max_clearance_level;
						
					}
					
				} elseif($default_level && (count($default_level) == 1)) {
					
					$finalClearanceLevel = $default_level->clearance_level;
					
				} else {
				
					$finalClearanceLevel = $min_clearance_level;
					
				}
				
				$this->auth->setClearanceSettings($finalClearanceLevel);
				
				if($strict === TRUE) {
				
					$this->auth->checkPermission();
					
				}
				
			}
			
		}

		public function show404Action() {
		
			// echo "<h1 >404 Error</h1><br />Sorry for the Inconvenience! The requested Page Not Found.";
			// $this->commonView->pick("errorPages" . DS . "404Error");
			include $this->commonView->getViewsDir() . "errorPages" . DS . "404Error.phtml";
			
		}

		public function showInvalidUrlAction() {
		
			// echo "<h1 >Invalid URL Error</h1><br />Sorry for the Inconvenience! The requested Page has some Invalid URL Handler/Parameters.";
			// $this->commonView->pick("errorPages" . DS . "invalidURLError");
			include $this->commonView->getViewsDir() . "errorPages" . DS . "invalidURLError.phtml";
			
		}

		public function showNoDIAction() {
		
			// echo "<h1 >No DI Error</h1><br />Sorry for the Inconvenience! The Dependency Injector Container for the Services Not Found.";
			// $this->commonView->pick("errorPages" . DS . "noDIError");
			include $this->commonView->getViewsDir() . "errorPages" . DS . "noDIError.phtml";
			
		}

		public function showCyclicRoutingAction() {
		
			// echo "<h1 >Cyclic Routing Error</h1><br />Sorry for the Inconvenience! The requested Page has a Cyclic Routing Problem.";
			// $this->commonView->pick("errorPages" . DS . "cyclicRoutingError");
			include $this->commonView->getViewsDir() . "errorPages" . DS . "cyclicRoutingError.phtml";
			
		}

	}

?>