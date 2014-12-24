<?php

	use \Phalcon\Loader;
	use \Phalcon\Mvc\Dispatcher as UrlDispatcher;
	use \Phalcon\Events\Manager as EventManager;
	use \Phalcon\Mvc\View as MvcView;
	use \Phalcon\Mvc\View\Simple as MvcSimpleView;
	use \Phalcon\Mvc\View\Engine\Volt as VoltEngine;
	use \Phalcon\Flash\Direct as DirectFlash;
	use \Phalcon\Flash\Session as SessionFlash;
	
	
	class ModuleInitiator {
	
	
		private $workingDir = '';
		private $modNameSpace = '';
		
		
		public function __construct($workingDir, $modNameSpace) {
			
			$this->workingDir = $workingDir;
			$this->modNameSpace = $modNameSpace;
			
		}
		
		public function registerAutoloaders() {
		
			$config = include APP_CONFIG_PATH . "config.php";
			$moduleConfig = include $this->workingDir . DS . "config" . DS . "config.php";
			
			$moduleNameExtracted = basename($this->workingDir);
			$moduleNameCapped = ucfirst($moduleNameExtracted);
			$moduleName = $moduleConfig->module->moduleName;

			$loader = new Loader();
			
			$loader->registerPrefixes(array(
				$moduleNameCapped . "_Libraries"   => $this->workingDir . DS . "libraries" . DS,
				$moduleNameCapped . "_Helpers"     => $this->workingDir . DS . "helpers" . DS,
			));
			
			$loader->registerNamespaces(array(
				$this->modNameSpace . "\Controllers" => $this->workingDir . DS . "controllers" . DS,
				$this->modNameSpace . "\Models"      => $this->workingDir . DS . "models" . DS,
			));

			$loader->register();
			
		}

		/**
		 * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
		 */
		public function registerServices($di) {

			/**
			 * Read the configuration
			 */
			$config = require APP_CONFIG_PATH . "config.php";
			$moduleConfig = include $this->workingDir . DS . "config" . DS . "config.php";
			
			/** Registering a dispatcher **/
			$this->initModuleDispatcher($di, $config, $moduleConfig);
			
			/** Read Common View Settings **/
			$this->initCommonViewer($di);
			
			/** Setting up the view component **/
			$this->initModuleViewer($di, $config, $moduleConfig);
	
			/**  Register the flash service with custom CSS classes **/
			$this->initModuleFlashes($di, $config, $moduleConfig);

		}
		
		private function initModuleDispatcher($di, $config, $moduleConfig) {
		
			//Registering a dispatcher
			$di->set('dispatcher', function ()  use ($config, $moduleConfig){
				$dispatcher = new UrlDispatcher();

				//Attach a event listener to the dispatcher
				$eventManager = new EventManager();
				
				$eventManager->attach(
					"dispatch:beforeException", function ($event, $dispatcher, $exception) {
					switch ($exception->getCode()) {
						case \Phalcon\Mvc\Dispatcher::EXCEPTION_NO_DI:
							$dispatcher->forward(array(
								'controller' => 'index',
								'action' => 'showNoDI'
							));
							return false;
							break;
						case \Phalcon\Mvc\Dispatcher::EXCEPTION_CYCLIC_ROUTING:
							$dispatcher->forward(array(
								'controller' => 'index',
								'action' => 'showCyclicRouting'
							));
							return false;
							break;
						case \Phalcon\Mvc\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
						case \Phalcon\Mvc\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
							$dispatcher->forward(array(
								'controller' => 'index',
								'action' => 'show404'
							));
							return false;
							break;
						case \Phalcon\Mvc\Dispatcher::EXCEPTION_INVALID_HANDLER:
						case \Phalcon\Mvc\Dispatcher::EXCEPTION_INVALID_PARAMS:
							$dispatcher->forward(array(
								'controller' => 'index',
								'action' => 'showInvalidUrl'
							));
							return false;
							break;
					}
				});
			
				$moduleNameExtracted = basename($this->workingDir);
				$moduleName = $moduleConfig->module->moduleName;
				
				$eventManager->attach('dispatch', new \AclOpns($moduleName));
				
				$dispatcher->setEventsManager($eventManager);
				$dispatcher->setDefaultNamespace($this->modNameSpace . "\Controllers\\");
				return $dispatcher;
			});
			
		}
		
		private function initCommonViewer($di) {
		
			/**
			 * Setting up the common view component
			 */
			$di->set('commonView', function () use ($di) {

				$commonView = new MvcView();
				// $commonView = new MvcSimpleView();
				
				$currentStore  = $di->get('storeManager');
				
				$currentTheme  = $currentStore->getCurrentStoreTheme();
				
				//$viewDir = APP_DESIGN_PATH . $currentTheme . DS;
				
				$coreDirCheck = strpos($this->workingDir, 'core' . DS . 'modules');
				
				$dirCheckResult = (!($coreDirCheck === false)) ? DS . ".." . DS : DS;
				
				$viewDir = $this->workingDir . DS . "views" . DS . ".." . DS . ".." . DS . ".." . $dirCheckResult . "design" . DS . $currentTheme . DS;
				
				$commonView->setViewsDir($viewDir);
				
				$commonView->registerEngines(
					array(
					
						'.volt' => function ($commonView, $di) {

							$volt = new VoltEngine($commonView, $di);

							$volt->setOptions(array(
								'compiledPath' => APP_CACHE_PATH,
								'compiledSeparator' => '_'
							));

							return $volt;
						},
						
						'.phtml' => 'Phalcon\Mvc\View\Engine\Php'
						
					)
				);
				
				// $commonView->setRenderLevel(MvcView::LEVEL_ACTION_VIEW);

				return $commonView;
			}, true);
			
		}
		
		private function initModuleViewer($di, $config, $moduleConfig) {
		
			/**
			 * Setting up the view component
			 */
			$di->set('view', function () use ($di, $config, $moduleConfig) {

				$view = new MvcView();
				
				$moduleNameExtracted = basename($this->workingDir);
				$moduleName = $moduleConfig->module->moduleName;
				
				$currentStore   = $di->get('storeManager');
				$inlineRegistry = $di->get('inlineRegistry');
				
				$currentTheme  = $currentStore->getCurrentStoreTheme();
				$viewDir = APP_DESIGN_PATH . $currentTheme;
				
				$coreDirCheck = strpos($this->workingDir, 'core' . DS . 'modules');
				
				$dirCheckResult = (!($coreDirCheck === false)) ? DS . ".." . DS : DS;
				
				$startViewPage = ".." . DS . ".." . DS . ".." . $dirCheckResult . "design" . DS . $currentTheme . DS . "template" . DS;
				
				$view->setViewsDir($this->workingDir . DS . "views" . DS);

				$view->setMainView($startViewPage . DS . 'base_page');
				
				$view->setLayoutsDir(".." . DS . "layouts");
				
				$view->setPartialsDir(".." . DS . "layouts" . DS . "layout_partials");
				
				$view->registerEngines(array(
					'.volt' => function ($view, $di) use ($config) {

						$volt = new VoltEngine($view, $di);

						$volt->setOptions(array(
							'compiledPath' => APP_CACHE_PATH,
							'compiledSeparator' => '_'
						));

						return $volt;
					},
					'.phtml' => 'Phalcon\Mvc\View\Engine\Php'
				));
				
				return $view;
			}, true);
		
		}
		
		private function initModuleFlashes($di, $config, $moduleConfig) {
		
			/**  Register the normal flash service with custom CSS classes **/
			$di->set('flash', function() use ($moduleConfig){
				return new DirectFlash(array(
					'error'   => $moduleConfig->flashMessage->direct->errorClass,
					'success' => $moduleConfig->flashMessage->direct->successClass,
					'notice'  => $moduleConfig->flashMessage->direct->noticeClass,
					'warning' => $moduleConfig->flashMessage->direct->warningClass
				));
			});
			
			/**  Register the session flash service with custom CSS classes **/
			$di->set('flashSession', function() use ($moduleConfig){
				return new SessionFlash(array(
					'error'   => $moduleConfig->flashMessage->session->errorClass,
					'success' => $moduleConfig->flashMessage->session->successClass,
					'notice'  => $moduleConfig->flashMessage->session->noticeClass,
					'warning' => $moduleConfig->flashMessage->session->warningClass
				));
			});
		
		}

	}
	
?>