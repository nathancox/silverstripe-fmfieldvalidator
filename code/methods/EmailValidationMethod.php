<?php
/**
 * Makes the field invalid if it's not a properly formatted email address.
 * Uses the default email checking from jquery.validate on the client side
 */
class EmailValidationMethod extends FMValidationMethod {
	var $ruleName = 'email';
	var $defaultMessage = 'email address not valid';

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