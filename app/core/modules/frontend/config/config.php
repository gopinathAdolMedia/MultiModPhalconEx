<?php

	return new \Phalcon\Config(array(
		
		'module' => array(
		
			'active'         => true,
			'moduleName'     => 'frontend',
			'urlSegment'     => 'default',
			'nameSpace'      => 'MultiMod\Frontend',
			'theme'          => 'default',
			
		),
			
		'flashMessage' => array(
		
			'direct' => array(
				'errorClass'     => 'errorMessage messages',
				'successClass'   => 'successMessage messages',
				'noticeClass'    => 'noticeMessage messages',
				'warningClass'   => 'warningMessage messages',
			),
			
			'session' => array(
				'errorClass'     => 'errorMessage messages',
				'successClass'   => 'successMessage messages',
				'noticeClass'    => 'noticeMessage messages',
				'warningClass'   => 'warningMessage messages',
			)
			
		)
	));

?>