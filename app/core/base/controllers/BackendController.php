<?php
	
	class BackendController extends \BaseController {

		
		public function initialize() {
		
		}
		
		public function initializeSettings($clearanceLevel = '', $strict = FALSE, $scope = 'backend') {
		
			parent::initializeSettings('backend', $clearanceLevel, $strict);
			
		}

	}

?>