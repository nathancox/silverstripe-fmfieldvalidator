<?php
/*
	Test class: actually does the same thing as "equal"
*/
class IsExactlyValidationMethod extends FMValidationMethod {
	var $name = 'isExactly';
	
	function javascript() {
		$script = <<<JS
			function(value, element, param) {
				if (value == param) {
					return true;
				} else {
					return false;
				}
			}
JS
		;
		return $script;
	}
	
	function php($field, $ruleValue, $form) {
		$valid = false;
		$fieldValue = $field->value();
		
		if ($fieldValue == $ruleValue) {
			$valid = true;
		}
		
		return $valid;
	}
	
	
	function defaultMessage() {
		return 'wrong answer';
	}
	
}