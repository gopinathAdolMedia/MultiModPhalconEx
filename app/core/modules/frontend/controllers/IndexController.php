<?php

	namespace MultiMod\Frontend\Controllers;
	

	// class IndexController extends \BaseController {
	class IndexController extends \FrontendController {

		public function initialize() {
		
			// $scope = "frontend";
			
			// $this->initializeSettings($scope);
			$this->initializeSettings();
			
		}

		public function indexAction() {
		
			$sendingVariables = array(
				"pageName" => "Home"
			);
			$this->view->setVars($sendingVariables);
			
		}

	}

?>