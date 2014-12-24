<?php

	// Initializing Cache for the Site
	
	use \Phalcon\Cache\Frontend\Data as CacheData;
	use \Phalcon\Cache\Frontend\Output as CacheOutput;
	

	class AppCache {
	
		public function __construct() {
		
		}
		
		public function initCache($di, $config) {
		
			if (!PHALCON_DEBUG) {
			 
				// Get the parameters.
				$cacheAdapter = '\Phalcon\Cache\Backend\\' . $config->cache->adapter;
				$frontEndOptions = ['lifetime' => $config->cache->lifetime];
				$backEndOptions = $config->cache->toArray();
				$frontOutputCache = new CacheOutput($frontEndOptions);
				$frontDataCache = new CacheData($frontEndOptions);

				// Cache:View.
				$di->set('viewCache', function () use ($cacheAdapter, $frontOutputCache, $backEndOptions) {
				
					return new $cacheAdapter($frontOutputCache, $backEndOptions);
					
				});

				// Cache:Output.
				$cacheOutput = new $cacheAdapter($frontOutputCache, $backEndOptions);
				$di->set('cacheOutput', $cacheOutput, true);

				// Cache:Data.
				$cacheData = new $cacheAdapter($frontDataCache, $backEndOptions);
				$di->set('cacheData', $cacheData, true);

				// Cache:Models.
				$cacheModels = new $cacheAdapter($frontDataCache, $backEndOptions);
				$di->set('modelsCache', $cacheModels, true);

			} else {
			
				// Create a dummy cache for system.
				// System will work correctly and the data will be always current for all adapters.
				
				$dummyCache = new \DummyCache(null);
				$di->set('viewCache', $dummyCache);
				$di->set('cacheOutput', $dummyCache);
				$di->set('cacheData', $dummyCache);
				$di->set('modelsCache', $dummyCache);
				
			}
		
		}
	
	}
	
?>