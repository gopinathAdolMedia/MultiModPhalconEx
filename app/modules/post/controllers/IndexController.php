<?php

	namespace MultiMod\Post\Controllers;
	

	// class IndexController extends \BaseController {
	class IndexController extends \FrontendController {

		public function initialize() {
		
			// $scope = "frontend";
			
			// $this->initializeSettings($scope);
			$this->initializeSettings();
			
		}

		public function indexAction() {
			
			$sendingVariables = array(
				"pageName" => "Posts"
			);
			$this->view->setVars($sendingVariables);
			
		}

	}

?>