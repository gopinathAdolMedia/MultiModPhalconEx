<?php

	use \Phalcon\Cache\Backend;


	class SiteSettings {

	
		private $siteSettingsObj;
		
		
		public function __construct() {
			
			$this->siteSettingsObj = new SiteSettingModel();
			
		}
		
		public function getSettings() {
			
			$settingsList = $this->siteSettingsObj->find();
			
			if($settingsList) {
			
				return $settingsList->toArray();
				
			} else {
			
				return FALSE;
				
			}
			
		}
		
		public function isSettingAvailable($label) {
			
			return $this->getValueByLabel($label);
			
		}
		
		public function getValueById($id) {
			
			$settingValue = $this->siteSettingsObj->getValueById($id);
			
			if($settingValue) {
			
				return $settingValue;
				
			} else {
			
				return FALSE;
				
			}
			
		}
		
		public function getValueByLabel($label) {
			
			$settingValue = $this->siteSettingsObj->getValueByLabel($label);
			
			if($settingValue) {
			
				return $settingValue;
				
			} else {
			
				return FALSE;
				
			}
			
		}

	}

?>