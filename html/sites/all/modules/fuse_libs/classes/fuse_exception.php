<?php

/**
 * Exception class to handle all the exceptional situations in the code
 * and log all the errors in different ways
 *
 * @author vparfaniuc
 *        
 *        
 */

class FuseException extends Exception {
	
	// Redefine the exception so message isn't optional
	public function __construct($message, $code = 0) {
		// make sure everything is assigned properly
		parent::__construct($message, $code);
		
		// some code
		$error_text = 'New exception happened on '.$this->getFile(). ' file; line '.$this->getLine().'. With the message:';
		$error_text .= $this->getMessage();
		$error_text .= '\r\n TRACE INFO: '.$this->getTraceAsString();
		watchdog('fuse_libs', $error_text, array(), WATCHDOG_WARNING);
		
	}
}

