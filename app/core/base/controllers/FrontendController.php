<?php

	class FrontendController extends \BaseController {

		
		public function initialize() {
		
		}
		
		public function initializeSettings($scope = 'frontend', $clearanceLevel = '', $strict = FALSE) {
		
			parent::initializeSettings('frontend', $clearanceLevel, $strict);
			
		}

	}

?>