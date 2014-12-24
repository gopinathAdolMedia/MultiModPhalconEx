<?php

	class StoreManager {

	
		private $storeObj;
		
		private static $storeChecker = 'default';
		
		
		public function __construct() {
			
			$this->storeObj = new StoresModel();
			
		}
		
		public function getStores() {
			
			$storeList = $this->storeObj->find();
			
			if($storeList) {
			
				return $storeList->toArray();
				
			} else {
			
				return FALSE;
				
			}
			
		}
		
		public function isStoreAvailable($store) {
			
			$storeId = $this->storeObj->getIdByCode($store);
			
			if($storeId) {
			
				return $storeId;
				
			} else {
			
				return FALSE;
				
			}
			
		}
		
		public function setCurrentStore($storeInput = 'default') {
			
			self::$storeChecker = $storeInput;
			
		}
		
		public function getCurrentStore() {
			
			return self::$storeChecker;
			
		}
		
		public function getDefaultStore() {
			
			return 'default';
			
		}
		
		public function getCurrentStoreId() {
			
			return $this->isStoreAvailable(self::$storeChecker);
			
		}
		
		public function getCurrentStoreDetails($searchKey = '') {
			
			if($this->getCurrentStoreId()) {
			
				$storeDetails = $this->storeObj->getStoreDetails($this->getCurrentStoreId());
				
				if($storeDetails) {
				
					if($searchKey == '') {
					
						return $storeDetails;
						
					} else {
					
						if(array_key_exists($searchKey, $storeDetails)) {
						
							return $storeDetails[$searchKey];
						
						} else {
						
							return FALSE;
							
						}
					
					}
					
				} else {
				
					return FALSE;
					
				}
				
			} else {
			
				return FALSE;
				
			}
		}
		
		public function getCurrentStoreCode() {
			
			$storeCode = $this->getCurrentStoreDetails('code');
				
			if($storeCode) {
			
				return $storeCode;
				
			} else {
			
				return FALSE;
				
			}

		}
		
		public function getCurrentStoreTheme() {
			
			$storeTheme = $this->getCurrentStoreDetails('theme');
				
			if($storeTheme) {
			
				return $storeTheme;
				
			} else {
			
				return FALSE;
				
			}
			
		}

	}

?>