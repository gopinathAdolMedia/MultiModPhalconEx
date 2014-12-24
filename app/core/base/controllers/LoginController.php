<?php

	class LoginController extends \BaseController {

		
		public function initialize() {
		
		}
		
		public function initializeSettings($scope = 'login', $clearanceLevel = '', $strict = FALSE) {
		
			parent::initializeSettings('login', $clearanceLevel, $strict);
			
		}

	}

?>