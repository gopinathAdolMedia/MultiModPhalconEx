<?php

	return new \Phalcon\Config(array(

		'application' => array(
			
			'baseUri'        	 => '/MultiModPhalconEx/',
			'environment'        => 'development',  							/* 		'development', 'testing', 'staging', 'production'	 */
			'timeZone'        	 => 'UTC',
			
			'site_language'      => 'EnUS',
			'default_language'   => 'EnUS',
			
			'siteName'       	 => 'A Sample Multi-Module Phalcon App',
			'siteHeader'     	 => 'A Sample Multi-Module Phalcon App - Header',
			
		),
		
		'database' => array(
		
			'default' => array(
				'adapter'     => 'Mysql',
				'host'        => 'localhost',
				'username'    => 'root',
				'password'    => '',
				'dbname'      => 'multimodphalcondb',
			)
			
		),
		
		'modules' => array(
			
			'post'
			
		),
		
		'logger' => array(
		
			'siteLog' => array(
				'logFile'     	 => 'SiteActivities.log',
				'logFormat'    	 => "[%date%][%type%] - %message%"
			),
		
			'dbLog' => array(
				'logFile'     	 => 'DbActivities.log',
				'logFormat'    	 => "[%date%][%type%] - %message%"
			),
		
			'errorLog' => array(
				'logFile'     	 => 'ErrorLog.log',
				'logFormat'    	 => "[%date%][%type%] - %message%"
			)
			
		),
		
		'cache' => array (
		
			'lifetime'   => '86400',
			'prefix'     => 'mmp_',
			'adapter'    => 'File',
			'cacheDir'   => APP_CACHE_PATH . 'data/',
			
		),
		
		'session' => array (
		
			'uniqueId' => 'MultiMod_',
			
		),

		'annotations' => array (
		
			'adapter'         => 'Files',
			'annotationsDir'  => APP_CACHE_PATH . 'annotations/',
			
		)
		
	));

?>