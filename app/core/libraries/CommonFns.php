<?php

	class CommonFns {

	
		private static $di = '';
		private static $dispatcher = '';
		
		private static $renderStartTime = '';
		private static $renderEndTime = '';
		
		
		public function __construct($diInput, $dispatcherInput) {
			
			self::$di = $diInput;
			self::$dispatcher = $dispatcherInput;
			
		}
		
		public function urlToAssocConverter($urlParamsArray) {
			
			$loopCounter = ceil(count($urlParamsArray) / 2);
			$j = 0;
			$finalSplitArray = array();
			
			for($i = 0; $i < $loopCounter; $i++) {
				$finalSplitArray[$urlParamsArray[$j]] = (isset($urlParamsArray[$j + 1])) ? $urlParamsArray[$j + 1] : '';
				$j += 2;
			}
			
			return $finalSplitArray;
		}
		
		public static function getTimeBenchMark() {
			
			$renderTime = microtime();
			$renderTime = explode(' ', $renderTime);
			$renderTime = $renderTime[1] + $renderTime[0];
			return $renderTime;
			
		}
		
		public static function getRenderStartTime() {
			
			$totalRenderStartTime = self::getTimeBenchMark();
			/* $di = self::$di;
			$session  = $di->getShared('session');
			if($session->has("renderStartTime")) {
				$session->remove("renderStartTime");
			}
			$session->set("renderStartTime", $totalRenderStartTime); */
			self::$renderStartTime = $totalRenderStartTime;
			
		}
		
		public static function getRenderEndTime() {
			
			$totalRenderEndTime = self::getTimeBenchMark();
			/* $di = self::$di;
			$session  = $di->getShared('session');
			if($session->has("renderEndTime")) {
				$session->remove("renderEndTime");
			}
			$session->set("renderEndTime", $totalRenderEndTime); */
			self::$renderEndTime = $totalRenderEndTime;
			
		}
		
		public static function getTotalPageRenderTime() {
			
			self::getRenderEndTime();
			/* $di = self::$di;
			$session  = $di->getShared('session');
			
			if($session->has("renderEndTime") && $session->has("renderStartTime")) {
				$totalRenderEndTime   = self::$renderEndTime;
				$totalRenderStartTime = self::$renderStartTime;
				$totalRenderTime = round(($totalRenderEndTime - $totalRenderStartTime), 4);
			} else {
				$totalRenderTime = 0;
			} */
			
			$totalRenderEndTime   = self::$renderEndTime;
			$totalRenderStartTime = self::$renderStartTime;
			$totalRenderTime = round(($totalRenderEndTime - $totalRenderStartTime), 4);
			
			// return $totalRenderTime . ' / ' . $totalRenderEndTime . ' / ' . $totalRenderStartTime;
			return $totalRenderTime;
			
		}
		
		public static function convertMemorySizeTo($targetSize, $to = 'MB', $from = 'B') {
		
			$sizeUnit = array('B','KB','MB','GB','TB','PB');
			
			$fromIndex = array_search($from, $sizeUnit);
			$toIndex   = array_search($to, $sizeUnit);
			
			if(($fromIndex !== FALSE) && ($toIndex !== FALSE)) {
			
				$fromFactor = pow(1024, $fromIndex);
				$toFactor   = pow(1024, $toIndex);
				
				$fromSize  = $targetSize * $fromFactor;
				$toSize    = @round($fromSize / $toFactor, 4);
				
				$sizeResult = $toSize . ' ' . $to;
				
			} else {
				$sizeResult = $targetSize;
			}
			
			return $sizeResult;
			
		}
		
		public function importSqlFile($filePath, $connectionService = 'db') {
		
			if (file_exists($filePath)) {
				$di = self::$di;
				$connection = $di->get($connectionService);
				$connection->begin();
				$connection->query(file_get_contents($filePath));
				$connection->commit();
				return true;
			} else {
				return false;
				
				// throw new Exception(sprintf('Sql file "%s" does not exists', $filePath));
				
				/* $errStr = sprintf('Sql file "%s" does not exists', $filePath);
				$errCode = 1;
				$errSeverity = 1;
				$errFile = __FILE__;
				$errLine = 26;
				throw new \Phalcon\Exception($errStr, $errCode, $errSeverity, $errFile, $errLine); */
			}
			
		}
		
	}

?>