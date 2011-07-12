<?php

class FMFormFieldExtension extends Extension {
	var $validationRules = array();
	var $validationMessages = array();
	
	
	
	
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
	
	
	function getValidationRulesForJavascript() {
		$fieldName = $this->owner->Name();
		$rules = $this->getValidationRules();
		foreach ($rules as $ruleName => $ruleValue) {
			if ($method = FMFieldValidator::get_validation_method($ruleName)) {
				
				if ($newValue = $method->convertRuleForJavascript($this, $ruleValue, $this->owner->getForm())) {
					
					$rules[$ruleName] = $newValue;
				}
			}
		}
		return $rules;
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
		$this->validationMessages = $messages;
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
	 * Returns the message for a field, falling back to default if not defined
	 * 
	 * @param string $ruleName 
	 * @param string $fieldName 
	 * @return string
	 */
	function getErrorMessage($ruleName) {
		if (isset($this->validationMessages[$ruleName])) {
			return $this->validationMessages[$ruleName];
		} else if ($defaultMessage = FMFieldValidator::get_default_message($ruleName)) {
			return $defaultMessage;
		} else if ($method = FMFieldValidator::get_validation_method($ruleName)) {
			return $method->defaultMessage();
		} else {
			return 'error validating field';
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
	
	
	function validatePHP($validator) {
		$rules = $this->getValidationRules();
		$valid = true;
		foreach ($rules as $ruleName => $ruleValue) {
		
			if ($method = FMFieldValidator::get_validation_method($ruleName)) {
				$validates = $method->validate($this->owner, $ruleValue, $validator->getForm());
				$valid = ($validates && $valid);
				if (!$validates) {
					
					$validator->validationError(
						$this->owner->Name(),
						$this->getErrorMessage($ruleName),
						$ruleName
					);
				}
				
			} else {
				if (!FMFieldValidator::$ignore_missing_methods) {
					trigger_error('Field '.$this->owner->Name().' is trying to validate with a non-existant rule ('.$ruleName.')');
				}
			}
		}
		return $valid;
	}
	
	
}