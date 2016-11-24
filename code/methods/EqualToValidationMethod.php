<?php
/**
 * Valid if the field's value is equal to the value of another field (specified by it's name).
 *
 * eg:
 * 			$fields->push($passwordField = new TextField('Password', 'Password'));
 *
 *			...
 *
 *   		$fields->push($confirmPasswordField = new TextField('ConfirmPassword', 'Confirm Password'));
 * 			$confirmPasswordField->setValidationRules(array(
 *				'equalTo' => 'Password'
 *			));
 *			$confirmPasswordField->setValidationMessages(array(
 *				'equalTo' => "whoops, your passwords don't match"
 *			));
 *
 * It could also be formatted like this:
 *
 *			$passwordField->setValidationRules(array(
 *				'equalTo' => array(
 *					'value' => 'Password',
 *					'message' => "whoops, your passwords don't match"
 *				)
 *			));
 *
 *
 */
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