<?php

	use \Phalcon\Cache\Backend;


	class SystemCache {

		/**
		 * Cache key for router data.
		 */
		const CACHE_KEY_ROUTER_DATA = 'router_data';

		/**
		 * Widgets metadata, stored from modules.
		 */
		const CACHE_KEY_WIDGETS_METADATA = 'widgets_metadata';

	}

?>