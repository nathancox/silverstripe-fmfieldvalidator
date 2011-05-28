<?php

class FMFormFieldExtension extends Extension {
	var $validationRules = array();
	var $validationMessages = array();
	
	function test() {
		info($this->getValidationRules(), $this->owner->class);
	}
	
	
	
	/**
	 * save a complete ruleset to this field
	 * 
	 * @param array $rules 
	 */
	function setValidationRules($rules) {
		$this->validationRules = $rules;
	}
	
	/**
	 * fetch the complete ruleset for this field
	 * 
	 * @return array 
	 */
	function getValidationRules() {
		return $this->validationRules;
	}
	
	/**
	 * return the details for a given rule on this field, if they exist
	 * 
	 * @param string $ruleName
	 * @return mixed false if this rule isn't set, otherwise an array 
	 */
	function getValidationRule($ruleName) {
		if (isset($this->validationRules[$ruleName])) {
			return $this->validationRules[$ruleName];
		} else {
			return false;
		}
	}
	/**
	 * set a given rule
	 * 
	 * @param string $ruleName
	 * @param mixed $ruleValue
	 */
	function addValidationRule($ruleName, $ruleValue = true) {
		$this->validationRules[$ruleName] = $ruleValue;
	}
	
	
	
	
	
	/**
	 * save a complete message set to this field
	 * 
	 * @param array $messages 
	 */
	function setValidationMessages($messages) {
		$this->validationMessages = messages;
	}
	
	/**
	 * fetch the complete message set for this field
	 * 
	 * @return array 
	 */
	function getValidationMessages() {
		return $this->validationMessages;
	}
	
	/**
	 * return the message for a given rule on this field, if it exists
	 * 
	 * @param string $ruleName
	 * @return mixed false if this rule isn't set, otherwise a string 
	 */
	function getValidationMessage($ruleName) {
		if (isset($this->validationMessages[$ruleName])) {
			return $this->validationMessages[$ruleName];
		} else {
			return false;
		}
	}
	
	/**
	 * set the message for a given rule
	 * 
	 * @param string $ruleName
	 * @param string $message
	 */
	function setValidationMessage($ruleName, $message) {
		$this->validationMessages[$ruleName] = $message;
	}
	
	
}