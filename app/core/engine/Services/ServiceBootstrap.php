<?php

	use Phalcon\DI\FactoryDefault;
	use Phalcon\Mvc\Url as UrlResolver;
	use Phalcon\Session\Adapter\Files as SessionAdapter;
	use Phalcon\Tag as TagCollection;
	use Phalcon\Filter as FilterOptions;
	use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
	use \Phalcon\Mvc\View as MvcView;
	use \Phalcon\Mvc\View\Engine\Volt as VoltEngine;

	class ServiceBootstrap {
	
		private $appConfig;
		private $store_code;
		private $_di;
		private $appInstance;
		
		
		public function __construct($appInstance) {
		
			$this->appConfig = \Application::getAppConfig();
			
			$this->store_code = \Application::getAppStoreCode();
			
			$this->_di = \Application::getAppDI();
			
			$this->appInstance = $appInstance;
			
		}
		
		public function initServices() {
		
			$config = $this->appConfig;
			
			$di = $this->_di;
			
			$store_code = $this->store_code;
			
			$appInstance = $this->appInstance;
			
			/**
			 * Setting the Time Zone
			 */
			date_default_timezone_set($config->application->timeZone);

			/**
			 * Read Registry Settings
			 */
			$this->initInlineRegistry($di, $config);

			/**
			 * Read URL Settings
			 */
			$this->initUrl($di, $config);

			/**
			 * Read Site Activity Profiler Settings
			 */
			$this->initProfiler($di, $config);
			
			/**
			 * Read Log Settings
			 */
			$logger = new AppLogger();
			$logger->initLogger($di, $config);
			
			/**
			 * Read Database Settings
			 */
			$dbConn = new AppDatabase();
			$dbConn->initDatabase($di, $config);

			/**
			 * Read Store Settings
			 */
			$this->initStoreManager($di, $config, $store_code);

			/**
			 * Read Site Settings
			 */
			$this->initSiteSettings($di, $config);
			
			/**
			 * Read Cache Settings
			 */
			$appCache = new AppCache();
			$appCache->initCache($di, $config);

			/**
			 * Read Routes Settings
			 */
			$routes = new AppRoutes();
			$routes->initRoutes($di, $config);

			/**
			 * Read Module Settings
			 */
			$modules = new AppModules();
			$modules->initModules($config, $appInstance);
			
			/**
			 * Read ACL Settings
			 */
			$appAcl = new AppAcl();
			$appAcl->initAcl($di, $config);
			
			/**
			 * Read Annotations Settings
			 */
			$appAnnotation = new AppAnnotations();
			$appAnnotation->initAnnotations($di, $config);
			
			/**
			 * Read Language Translator Settings
			 */
			$appLang = new AppLanguage();
			$appLang->initLanguage($di, $config);

			/**
			 * Read Application Settings
			 */
			$this->initConfig($di, $config);

			/**
			 * Read Session Settings
			 */
			$this->initSession($di, $config);

			/**
			 * Read HTML Tags Collection
			 */
			$this->initHtmlTags($di);

			/**
			 * Read Filter and Sanitizing Class
			 */
			$this->initInputFilter($di);

			/**
			 * Read Transaction Manager Settings
			 */
			$this->initTransactions($di);
		
		}
		
		private function initInlineRegistry($di, $config) {
		
			/**
			 * The Inline Global Variable - Registry
			 */
			$di->set('inlineRegistry', function () use ($config) {
			
				$registry = new InlineRegistry();
				// $url->setBaseUri($config->application->baseUri);

				return $registry;
				
			}, true);
			
		}
		
		private function initUrl($di, $config) {
		
			/**
			 * The URL component is used to generate all kind of urls in the application
			 */
			$di->set('url', function () use ($config) {
			
				$url = new UrlResolver();
				$url->setBaseUri($config->application->baseUri);

				return $url;
				
			}, true);
			
		}
		
		private function initProfiler($di, $config) {
		
			/**
			 * The Site Activity Profiler
			 */
			$di->set('profiler', function () use ($config) {
			
				$profiler = new CustomProfiler();
				return $profiler;
				
			}, true);
			
		}
		
		private function initStoreManager($di, $config, $store_code) {
		
			/**
			 * Setting up the Store Details
			 */
			$di->set('storeManager', function () use ($config, $store_code) {
			
				$sdm = new \StoreManager();
				
				if($sdm->isStoreAvailable($store_code)) {
				
					$sdm->setCurrentStore($store_code);
					
				} else {

					$sdm->setCurrentStore($sdm->getDefaultStore());
					
				}
				
				return $sdm;
				
			}, true);
			
		}
		
		private function initSiteSettings($di, $config) {
		
			/**
			 * Setting up the Site Settings Singleton
			 */
			$di->set('siteSettings', function () use ($config) {
			
				$siteSettingObj = new \SiteSettings();
				
				return $siteSettingObj;
				
			}, true);
			
		}
		
		private function initConfig($di, $config) {
		
			/**
			 * Access the Application Settings
			 */
			$di->set('config', function() use ($config){
			
				return $config;
				
			});
			
		}
		
		private function initSession($di, $config) {
		
			/**
			 * Start the session the first time some component request the session service
			 */
			$di->set('session', function () use ($config) {
			
				$sessionOptions = $config->session->toArray();
				$session = new SessionAdapter($sessionOptions);
				$session->start();

				return $session;
				
			});
			
		}
		
		private function initHtmlTags($di) {
		
			/**
			 * Registering the HTML Tags
			 */
			$di->set('htmlTags', function(){
			
				return new TagCollection();
				
			});
			
		}
		
		private function initInputFilter($di) {
		
			/**
			 * Registering the Filter and Sanitizing Class
			 */
			$di->set('inputFilter', function(){
			
				return new FilterOptions();
				
			});
			
		}
		
		private function initTransactions($di) {
		
			/**
			 * Setting the Transaction Manager
			 */
			$di->set('transactions', function () {
			
				return new TxManager();
				
			}, true);
			
		}
	
	}
	
?>