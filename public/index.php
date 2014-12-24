<?php
	
	/**
	 * Render Correct Sub-Domain System Requirements.
	 */
	 
	$selected_store = 'default';
	
	/* $current_entry_list = scandir(__DIR__ . "/store/");
	$ignorable_dir_list = array('.', '..', );
	$current_dir_list = array();
	
	foreach($current_entry_list as $current_pointer) {
		if(is_dir(__DIR__ . "/store/$current_pointer") && !(in_array($current_pointer, $ignorable_dir_list))) {
			$current_dir_list[] = $current_pointer;
		}
	}

	$current_store = (in_array($selected_store, $current_dir_list)) ? $selected_store : 'default'; */
	
	$current_store = (isset($selected_store)) ? $selected_store : 'default'; 
	
	/**
	 * Pathes.
	 */
	define('DS', DIRECTORY_SEPARATOR);
	define('PS', PATH_SEPARATOR);

	if (!defined('ROOT_PATH')) {
		define('ROOT_PATH', dirname(__DIR__) . DS);
	}
	if (!defined('PUBLIC_PATH')) {
		define('PUBLIC_PATH', __DIR__ . DS);
	}
	
	$coreEnginePath = ROOT_PATH . 'app' . DS . 'core' . DS . 'engine' . DS;
	
	/**
	 * Check System Requirements.
	 */
	require_once $coreEnginePath . 'Requirements.php';
	
	/**
	 * Create Application Class.
	 */
	require_once $coreEnginePath . 'Application.php';

	/**
	 * Invoke and Run Application.
	 */
	$application = new Application($current_store);
	$application->run();

?>