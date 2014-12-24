<?php

	//Registering a router
	
	
	use \Phalcon\Mvc\Router as MvcRouter;
	use \Phalcon\Mvc\Router\Annotations as RouterAnnotations;
	
	
	class AppRoutes {
	
	
		private $coreModules;
		
		
		public function __construct() {
		
			$this->coreModules = include CORE_MODULE_PATH . "core_modules.php";
			
		}
		
		private function getConfig($modulePath, $moduleInstance) {
		
			$moduleConfig = include $modulePath . $moduleInstance . DS . "config" . DS . "config.php";
			
			return $moduleConfig->module;
			
		}
		
		public function initRoutes($di, $config) {
		
			$cacheData = $di->get('cacheData');

			$classInst = $this;
			
			$di->set('router', function() use($config, $cacheData, $classInst){
				
				$router = new MvcRouter();
				
				
				// Registering the Routes for Core App Modules
				
				
				$coreModuleList = $this->coreModules->modules;
				$classInst->routeAdder($router, $coreModuleList, "core");
				
				
				// Registering the Routes for User-Defined App Modules
				
				
				$moduleList = array();
				$moduleList = $config->modules;
				if($moduleList) {
					$classInst->routeAdder($router, $moduleList, "app");
				}
				
				
				// Registering the Routes Cache
				
				
				$routerData = $cacheData->get(SystemCache::CACHE_KEY_ROUTER_DATA);
				if(PHALCON_DEBUG || ($routerData === null)) {
					$saveToCache = ($routerData === null);
					$cacheData->save(SystemCache::CACHE_KEY_ROUTER_DATA, $router, 2592000); // 30 days cache
				}/*  */
				
				// $router->notFound(array('controller' => 'error', 'action' => 'show404'));
				
				return $router;

			});
			
		}
		
		private function routeAdder($router, $moduleList, $type) {
		
			$routeDefaultFlag = TRUE;
				
			foreach($moduleList as $moduleInstance) {
			
				$is_module_includable = TRUE;
				
				if($type == "core") {
				
					$modulePath = CORE_MODULE_PATH;
					
				} elseif($type == "app") {
					
					$coreModuleList = $this->coreModules->modules;
					
					foreach($coreModuleList as $coreModuleInst) {
					
						$activeChecker = $this->getConfig(CORE_MODULE_PATH, $coreModuleInst);
						
						if((strtolower($coreModuleInst) == strtolower($moduleInstance)) && $activeChecker->active) {
						
							$is_module_includable = FALSE;
							break;
							
						}
						
					}
					
					$modulePath = APP_MODULE_PATH;
					
				}
				
				if($is_module_includable) {
				
					$moduleConfig = $this->getConfig($modulePath, $moduleInstance);
				
					if($moduleConfig->active) {
					
						$moduleName        = $moduleConfig->moduleName;
						$moduleUrlSegment  = $moduleConfig->urlSegment;
						$moduleNameSpace   = $moduleConfig->nameSpace;
						
						if(trim(strtolower($moduleUrlSegment)) == 'default') {
						
							if($routeDefaultFlag) {
							
								$router->setDefaultModule($moduleName);
								
								$router->add('/:controller', array(
									'module' => $moduleName,
									'controller' => 1,
								));
								
								$router->add('/:controller/:action', array(
									'module' => $moduleName,
									'controller' => 1,
									'action' => 2,
								));
								
								$router->add('/:controller/:action/:params', array(
									'module' => $moduleName,
									'controller' => 1,
									'action' => 2,
									'params' => 3,
								));
								
								$routeDefaultFlag = FALSE;

							}
							
						} else {
						
							$router->add("/$moduleUrlSegment", array(
								'module' => $moduleName
							));
							
							$router->add("/$moduleUrlSegment/", array(
								'module' => $moduleName
							));
							
							$router->add("/$moduleUrlSegment/:controller", array(
								'module' => $moduleName,
								'controller' => 1,
							));
							
							$router->add("/$moduleUrlSegment/:params", array(
								'module' => $moduleName,
								'controller' => 'index',
								'action' => 'index',
								'params' => 1
							));
							
							$router->add("/$moduleUrlSegment/:controller/:action", array(
								'module' => $moduleName,
								'controller' => 1,
								'action' => 2,
							));
							
							$router->add("/$moduleUrlSegment/:controller/:params", array(
								'module' => $moduleName,
								'controller' => 1,
								'action' => 'index',
								'params' => 2
							));
							
							$router->add("/$moduleUrlSegment/:controller/:action/:params", array(
								'module' => $moduleName,
								'controller' => 1,
								'action' => 2,
								'params' => 3,
							));
							
						}
					
					}
					
				}
				
			}
			
		}
	
	}

?>