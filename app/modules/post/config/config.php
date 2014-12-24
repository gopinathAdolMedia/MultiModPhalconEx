<?php

	return new \Phalcon\Config(array(
		
		'module' => array(
		
			'active'         => true,
			'moduleName'     => 'post',
			'urlSegment'     => 'posts',
			'nameSpace'      => 'MultiMod\Post',
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