<?php

	use Phalcon\DI;
	use \Phalcon\Exception as PhalconException;

	
	class ExceptionLogger extends PhalconException
	{
		/**
		 * Create exception.
		 */
		
		
		public function __construct($message = "", $args = [], $code = 0, \Exception $previous = null) {
		
			parent::__construct(vsprintf($message, $args), $code, $previous);
			
		}

		/**
		 * Log exception.
		 */
		 
		public static function logException(\Exception $e) {
		
			// echo $e->getSeverity();
			return self::logError(
				$e->getCode(),
				$e->getMessage(),
				$e->getFile(),
				$e->getLine(),
				$e->getTraceAsString()
			);
			
		}

		/**
		 * Log error.
		 */
		 
		public static function logError($type, $message, $file, $line, $trace = null) {
		
			$errType = $type;
			
			$errorCodeArray = array(
				'E_EXCEPTION'           => 0,
				'E_ERROR'               => 1,
				'E_WARNING'             => 2,
				'E_PARSE'               => 4,
				'E_NOTICE'              => 8,
				'E_CORE_ERROR'          => 16,
				'E_CORE_WARNING'        => 32,
				'E_COMPILE_ERROR'       => 64,
				'E_COMPILE_WARNING'     => 128,
				'E_USER_ERROR'          => 256,
				'E_USER_WARNING'        => 512,
				'E_USER_NOTICE'         => 1024,
				'E_STRICT'              => 2048,
				'E_RECOVERABLE_ERROR'   => 4096,
				'E_DEPRECATED'          => 8192,
				'E_USER_DEPRECATED'     => 16384,
				'E_ALL'                 => 32767,
			);
			
			if(gettype($type) == "integer") {
				foreach($errorCodeArray as $errTypeString => $errTypeCode) {
					if($type == $errTypeCode) {
						$errType = $errTypeString;
						break;
					}
				}
			}
			$id = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 7);
			$di = DI::getDefault();
			$template = "<%s> [%s] %s (File: %s Line: [%s])";
			$logMessage = sprintf($template, $id, $errType, $message, $file, $line);

			if ($di->has('profiler')) {
				$profiler = $di->getShared('profiler');
				if ($profiler) {
					$profiler->addError($logMessage, $trace);
				}
			}

			if ($trace) {
				$logMessage .= PHP_EOL . $trace . PHP_EOL;
			} else {
				$logMessage .= PHP_EOL;
			}

			if ($di->has('errorLog')) {
				$logger = $di->getShared('errorLog');
				if ($logger) {
					$logger->error($logMessage);
				} else {
					throw new \Exception($logMessage);
				}
			} else {
				throw new \Exception($logMessage);
			}
			
			if(error_reporting()) {
				if($type == 0) {
					echo "<br /><br />PhalconException : " . $message;
					echo "<br />Exception File : " . $file;
					echo "<br />Exception Line : " . $line;
				} else {
					echo "<br /><b>$errType</b> : $message ( <i>File</i>: <u>$file</u> => <i>Line</i>: [ $line ] )<br /><br />";
				}
			}

			return $id;
			
		}
		
	}

?>