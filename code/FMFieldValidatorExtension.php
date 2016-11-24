<?php
	/**
	 * An extension applied to FormField to add validation-related methods and properties for FMFieldValidator
	 *
	 */
class FMFieldValidatorExtension extends Extension {
	var $validationRules = array();
	var $validationMessages = array();



	/**
	 * Fetch the complete ruleset for this field
	 *
	 * @return array
	 */
	function getValidationRules() {
		return $this->validationRules;
	}

	/**
	 * Save a complete ruleset to this field
	 *
	 * @param array $rules
	 */
	function setValidationRules(array $rules) {
		$this->validationRules = array();

		foreach ($rules as $ruleName => $settings) {
			$this->setValidationRule($ruleName, $settings);
		}
	}


	/**
	 * Set a single validation rule
	 *
	 * @param string $ruleName The name of the rule
	 * @param mixed $settings either the rule value (eg "true") or an array with value and message parameters
	 */
	function setValidationRule($ruleName, $settings) {

		$value = $settings;
		if (is_array($settings)) {
			if (isset($settings['value'])) {
			 	$value = $settings['value'];
			}

			if (isset($settings['message'])) {
				$this->setValidationMessage($ruleName, $settings['message']);
			}

		}

		$this->validationRules[$ruleName] = $value;
	}

	/**
	 * Remove all this field's validation rules and messages
	 */
	function clearValidationRules() {
		$this->validationRules = array();
		$this->validationMessages = array();
	}

	/**
	 * Return the details for a given rule on this field, if they exist
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
	 * Get all this field's rules for use in the javascript validator
	 *
	 * @return array
	 */
	function getValidationRulesForJavascript() {
		$rules = $this->getValidationRules();
		foreach ($rules as $ruleName => $ruleValue) {
			$rules[$ruleName] = $this->getValidationRuleForJavascript($ruleName);
		}
		return $rules;
	}


	/**
	 * Get a specific rule for use in the javascript validator
	 *
	 * @param string $name 	The name of the rule
	 * @return string
	 */
	function getValidationRuleForJavascript($ruleName) {
		$fieldName = $this->owner->getName();
		$ruleValue = $this->getValidationRule($ruleName);
		if (!$rule) {
			return null;
		}
		if ($method = FMFieldValidator::get_validation_method($ruleName)) {

			if ($newValue = $method->convertRuleForJavascript($this, $ruleValue, $this->owner->getForm())) {
				$ruleValue = $newValue;
			}

			return $ruleValue;
		} else {
			trigger_error("FMFieldValidatorExtension can't find a validation rule for the name \"{$ruleName}\"", E_USER_WARNING);
			return null;
		}
	}








	/**
	 * Save a complete message set to this field
	 *
	 * @param array $messages
	 */
	function setValidationMessages(array $messages) {
		foreach ($messages as $ruleName => $message) {
			$this->setValidationMessage($ruleName, $message);
		}
	}


	/**
	 * Save a validation message set to this field
	 *
	 * @param string $ruleName
	 * @param string $message
	 */
	function setValidationMessage($ruleName, $message) {
		$this->validationMessages[$ruleName] = $message;
	}



	/**
	 * Fetch the complete message set for this field
	 *
	 * @return array
	 */
	function getValidationMessages() {
		return $this->validationMessages;
	}

	/**
	 * Return the message for a given rule on this field, if it exists
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
	 * @return string
	 */
	/*
	function getErrorMessage(string $ruleName) {
		$method = $this->getValidationMethod($ruleName);
		info($method->haveValidated());
		info($method->getMessage());
		if ($method->haveValidated() && $message = $method->getMessage()) {
			return $message;
		}
		if (isset($this->validationMessages[$ruleName])) {
			$messages = $this->validationMessages[$ruleName];
			if (is_array($messages)) {
				if ($variant && isset($messages[$variant])) {
					return $messages[$variant];
				}
			} else {
				return $messages;
			}
		}

		if ($defaultMessage = FMFieldValidator::get_default_message($ruleName)) {
			return $defaultMessage;
		} else if ($method = FMFieldValidator::get_validation_method($ruleName)) {
			return $method->defaultMessage();
		} else {
			trigger_error('Could not find validation message for "'.$ruleName.'" on field "'.$this->owner->getName().'"');
			return null;
		}
	}
*/

	/**
	 * Get the FMValidationMethod object for the given rule
	 *
	 * @param string $name 	The name of the rule
	 * @return FMValidationMethod
	 */
	function getValidationMethod($ruleName) {
		$method = FMFieldValidator::get_validation_method($ruleName, $this->owner);
		return $method;
	}



	/**
	 * Validates this form field server-side
	 *
	 * @param FMFieldValidator $validator
	 * @return
	 */
	function validatePHP(FMFieldValidator $validator) {
		$rules = $this->getValidationRules();
		$valid = true;
		$message = false;
		$validates = true;
		$variant = false;
		foreach ($rules as $ruleName => $ruleValue) {




			if ($method = $this->getValidationMethod($ruleName)) {

				$result = $method->validate();
				$validates = $method->isValid();

/*
				if (is_array($validationResult)) {
					$validates = $validationResult['valid'];
					if (isset($validationResult['message'])) {
						$message = $validationResult['message'];
					} else if (isset($validationResult['variant'])) {
						$variant = $validationResult['variant'];
					}

				} else if (is_string($validationResult)) {
					$message = $validationResult;
					$validates = false;
				} else {
					$validates = $validationResult;
				}
*/

				if ($customMessage = $method->getMessage()) {
					$message = $customMessage;
				} else if (isset($this->validationMessages[$ruleName])) {
					$message = $this->validationMessages[$ruleName];
					if (is_array($message)) {
						if ($variant && isset($messages[$variant])) {
							$message = $messages[$variant];
						}
					}
				} else if ($projectMessage = FMFieldValidator::get_default_message($ruleName)) {
					$message = $projectMessage;
				} else  {
					$message = $method->getDefaultMessage();
				}



				$valid = ($validates && $valid);
				if (!$validates) {
					$validator->validationError(
						$this->owner->getName(),
						$message,
						$method->getMessageType()
					);
				}

			} else {
				trigger_error('Field '.$this->owner->getName().' is trying to validate with a non-existent rule ('.$ruleName.')');
			}
		}
		return $valid;
	}


/*


	$validationRule = FMFieldValidator::get_validation_method($ruleName, $this->owner, $validator->getForm());
	$validationRule->setField($this->owner);
	$result = $validationRule->validate();

	$message = $validationRule->getMessage();
	$messageType = $validationRule->getMessageType();
	OR
	$message = $result['message'];
	$messageType = $result['messageType'];


*/

}