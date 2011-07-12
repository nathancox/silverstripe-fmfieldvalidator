<?php

class RangeValidationMethod extends FMValidationMethod {
	var $name = 'range';
	
	
	function php($field, $ruleValue, $form) {
		$valid = false;
		$fieldValue = $field->value();
		
		if (!$ruleValue) {
			return true;
		}
		
		if (is_numeric(trim($fieldValue))) {
			$valid = true;
		}
		
		$value = trim($fieldValue);
		$value = (int)$fieldValue;
		
		if ($value < $ruleValue[0] || $value > $ruleValue[1]) {
			$valid = false;
		}
		
		return $valid;
	}
	
	
	function convertRuleForJavascriptX($field, $ruleValue, $form) {
		$newValue = '['.$ruleValue[0].', '.$ruleValue[1].']';
		info($newValue);
		return $newValue;
	}
	
	
	function defaultMessage() {
		return 'not in the valid range';
	}
	
}