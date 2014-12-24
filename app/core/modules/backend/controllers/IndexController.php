<?php

	namespace MultiMod\Backend\Controllers;

	
	// class IndexController extends \BaseController {
	class IndexController extends \BackendController {

		public function initialize() {
			
			// $scope = "backend";
			
			$clearanceLevel = 1;
			
			$strict = false;
			
			// $this->initializeSettings($scope, $clearanceLevel, $strict);
			$this->initializeSettings($clearanceLevel, $strict);
			
		}
		
		public function indexAction() {
			
			$this->auth->checkPermission();
			
			$justChecking = new \Backend_Libraries_Marion();
			
			$sendingVariables = array(
				"pageName" => "Dashboard",
				"justLibrary" => $justChecking->justDisplay()
			);
			$this->view->setVars($sendingVariables);
			$this->view->pick("index/dashboard");
			
		}

	}

?>