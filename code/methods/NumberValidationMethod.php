<?php

class NumberValidationMethod extends FMValidationMethod {
	var $name = 'number';
	
	
	
	function php($field, $ruleValue, $form) {
		$valid = false;
		$fieldValue = $field->value();
		
		if (!$ruleValue) {
			return true;
		}
		
		if (is_numeric(trim($fieldValue))) {
			$valid = true;
		}
		
		return $valid;
	}
	
	
	function defaultMessage() {
		return 'must be a number';
	}
	
}