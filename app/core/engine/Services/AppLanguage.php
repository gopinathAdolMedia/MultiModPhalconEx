<?php

	// Setting up the Language Translators
	
	
	use Phalcon\Translate\Adapter\NativeArray as LangTransAdapter;
	
	
	class AppLanguage {
	
		public function __construct() {
		
		}
		
		public function initLanguage($di, $config) {
		
			$di->set('translator', function () use ($config) {
			
				$langLocale  = $config->application->site_language;
				$langDefault = $config->application->default_language;

				$messages = array();
				if (file_exists(CORE_LANG_PATH . $langLocale . ".php")) {
					require CORE_LANG_PATH . $langLocale . ".php";
				} elseif (file_exists(CORE_LANG_PATH . $langDefault . ".php")) {
					// fall-back to some default
					require CORE_LANG_PATH . $langDefault . ".php";
				} else {
					$messages = array();
				}

				$translate = new LangTransAdapter(array(
					"content" => $messages
				));

				return $translate;
				
			});

		}
	
	}

?>