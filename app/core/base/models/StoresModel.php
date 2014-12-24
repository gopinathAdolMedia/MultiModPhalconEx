<?php
	
	use \Phalcon\Mvc\Model\Validator\Uniqueness;
	
	
	class StoresModel extends \BasicModel {
		
		public function initialize() {
		
			parent::initializeModel("stores");
			
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
		
		public function getCodeById($id) {
		
			$searchOptions = array(
				"store_id = :store_id:",
				"bind" => array(
					"store_id" => $id
				)
			);
			
			$storeResult = $this->findFirst($searchOptions);

			$storeCode = (count($storeResult) > 0) ? $storeResult->code : FALSE;
			
			return $storeCode;
			
		}
		
		public function getIdByCode($code) {
		
			$searchOptions = array(
				"code = :store_code:",
				"bind" => array(
					"store_code" => $code
				)
			);
			
			$storeResult = $this->findFirst($searchOptions);

			$storeId = (count($storeResult) > 0) ? $storeResult->store_id : FALSE;
			
			return $storeId;
			
		}
		
		public function getStoreDetails($id) {
		
			$searchOptions = array(
				"store_id = :store_id:",
				"bind" => array(
					"store_id" => $id
				)
			);
			
			$storeResult = $this->findFirst($searchOptions);

			$storeDetails = (count($storeResult) > 0) ? $storeResult->toArray() : FALSE;
			
			return $storeDetails;
			
		}

	}

?>