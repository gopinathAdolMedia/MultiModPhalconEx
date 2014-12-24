<?php
	
	use \Phalcon\Mvc\Model\Validator\Uniqueness;
	
	
	class SiteSettingModel extends \BasicModel {
		
		public function initialize() {
		
			parent::initializeModel("settings");
			
		}
		
		public function validation() {
		
			$this->validate(new Uniqueness(
				array(
					"field" => "label",
					"message" => "The Label of the Setting must be UNIQUE"
				)
			));
			
			return $this->validationHasFailed() != true;
			
		}
		
		public function getValueById($id) {
		
			$searchOptions = array(
				"id = :setting_id:",
				"bind" => array(
					"setting_id" => $id
				)
			);
			
			$settingsResult = $this->findFirst($searchOptions);

			$settingsValue = (count($settingsResult) > 0) ? $settingsResult->value : FALSE;
			
			return $settingsValue;
			
		}
		
		public function getValueByLabel($label) {
		
			$searchOptions = array(
				"label = :setting_label:",
				"bind" => array(
					"setting_label" => $label
				)
			);
			
			$settingsResult = $this->findFirst($searchOptions);

			$settingsValue = (count($settingsResult) > 0) ? $settingsResult->value : FALSE;
			
			return $settingsValue;
			
		}

	}

?>