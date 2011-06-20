<?php

class EmailValidationMethod extends FMValidationMethod {
	var $name = 'email';
	var $defaultMessage = 'email address not valid';
	
	// we don't need to add any js, jquery.validate already supports this
	function javascript() {
		return false;
	}
	
	function php($field, $ruleValue, $form) {
		$valid = false;
		$fieldValue = trim($field->value());
		
		$pattern = '^[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$';
		

		// PHP uses forward slash (/) to delimit start/end of pattern, so it must be escaped
		$pattern = str_replace('/', '\\/', $pattern);

		if($fieldValue && !preg_match('/' . $pattern . '/i', $fieldValue)){
			$valid = false;
		} else {
			$valid = true;
		}
		
		return $valid;
	}
	
}