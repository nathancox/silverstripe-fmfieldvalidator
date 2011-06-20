<?php

class EqualToValidationMethod extends FMValidationMethod {
	var $name = 'equalTo';
	
	// we don't need to add any js, jquery.validate already supports this
	function javascript() {
		return false;
	}
	
	function php($field, $ruleValue, $form) {
		$valid = false;
		$fieldValue = $field->value();
		$fields = $form->Fields();
		
		if ($equalToField = $fields->dataFieldByName($ruleValue)) {
			$valid = ($fieldValue == $equalToField->value());
		} else {
			// @TODO: what do we do if the field we're matching with doesn't exist?
		}
		
		return $valid;
	}
	
	function defaultMessage() {
		return 'field does not match';
	}
	
	
	function convertRuleForJavascript($field, $ruleValue, $form) {
		$otherField = $form->dataFieldByName($ruleValue);
		if ($otherField) {
			return '#'.$otherField->ID();
		} else {
			return false;
		}
		
	}
	
}