<?php

class EqualToValidationMethod extends FMValidationMethod {
	var $ruleName = 'equalTo';
	
	function php($field, $ruleValue, $form) {
		$valid = false;
		$fieldValue = $this->getField()->value();
		$fields = $this->getForm()->Fields();
		
		if ($equalToField = $fields->dataFieldByName($this->getFieldRule())) {
			$valid = ($fieldValue == $equalToField->value());
		} else {
			// @TODO: what do we do if the field we're matching with doesn't exist?
		}
		
		return $valid;
	}
	
	function defaultMessage() {
		return 'field does not match';
	}
	
	
	function convertRuleForJavascript() {
		$otherField = $this->getForm()->fields->dataFieldByName($this->getFieldRule());
		if ($otherField) {
			return '#'.$otherField->ID();
		} else {
			return false;
		}
		
	}
	
}