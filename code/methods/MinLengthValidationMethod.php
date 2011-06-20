<?php
	/**
	 * Add a minimum length requirement for a field, added as
	 * 	array(
	 * 		'minlength' => 5
	 * 	);
	 */
class MinLengthValidationMethod extends FMValidationMethod {
	/**
	 * This is the name used to refer to this rule
	 *
	 * @var bool
	 */
	var $name = 'minlength';
	
	/**
	 * This is the rule's default error message
	 *
	 * @var bool
	 */
	var $defaultMessage = "this field isn't long enough";
	
	
	/**
	 * this method returns the javascript validation function as a string, or false to not define a custom function (ie if jquery.validate already supports it)
	 * 
	 * @return bool true to validate, false to not validate 
	 */
	function javascript() {
		// we don't need to add any js, jquery.validate already supports this
		return false;
	}
	
	/**
	 * this method is used to validate the rule server-side.
	 * 
	 * @param FormField $field this is the actual form field we're validating
	 * @param mixed $ruleValue this is the value assigned to the rule when it's defined.  In this case it would be the minimum length
	 * @return bool true to validate, false to not validate 
	 */
	function php($field, $ruleValue, $form) {
		$valid = false;
		$fieldValue = trim($field->value());
		
		// if the submitted value is >= the rule value, we're valid
		if (strlen($fieldValue) == 0 || strlen($fieldValue) >= $ruleValue) {
			$valid = true;
		}
		
		return $valid;
	}
	
}