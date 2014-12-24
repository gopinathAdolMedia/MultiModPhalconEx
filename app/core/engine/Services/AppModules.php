<?php

	//Register the installed modules
	
	
	class AppModules {
	
	
		private $moduleRegistry;
		private $coreModules;
		
		public function __construct() {
		
			$this->moduleRegistry = array();
			$this->coreModules = include CORE_MODULE_PATH . "core_modules.php";
			
		}
		
		private function getConfig($modulePath, $moduleInstance) {
		
			$moduleConfig = include $modulePath . $moduleInstance . DS . "config" . DS . "config.php";
			return $moduleConfig->module;
			
		}
		
		public function initModules($config, $appInstance) {
	
			// Registering the Core App Modules
			
			$coreModuleList = $this->coreModules->modules;
			$this->generateModuleRegistry($coreModuleList, "core");
			
			// Registering the User-Defined App Modules
			
			$moduleList = array();
			$moduleList = $config->modules;
			if($moduleList) {
				$this->generateModuleRegistry($moduleList, "app");
			}
			
			$appInstance->registerModules($this->moduleRegistry);
			
		}
		
		private function generateModuleRegistry($moduleList, $type) {
		
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
				
				if($is_module_includable){
				
					$moduleConfig = $this->getConfig($modulePath, $moduleInstance);
					
					if($moduleConfig->active) {
					
						$moduleName = $moduleConfig->moduleName;
						$moduleNameSpace = $moduleConfig->nameSpace;
						$setModule = array(
							'className' => "$moduleNameSpace\Module",
							'path' => $modulePath . $moduleName . DS . "Module.php"
						);
						$this->moduleRegistry[$moduleName] = $setModule;
						
					}
					
				}
				
			}
			
		}
	
	}

?>