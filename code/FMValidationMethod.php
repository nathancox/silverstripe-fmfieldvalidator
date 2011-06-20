<?php


class FMValidationMethod extends Object {
	var $name;
	var $defaultMessage = 'error validating field';
	
	/**
		* returns a js function that gets added to the jQuery validator, or false to not include (ie if using an exisiting method)
		* should be overridden in child classes
		* 
		* @return string|false
	 */
	function javascript() {
		return false;
	}
	

	/**
		* this method is here so that if I ever need to add parameters for javascript() I can put them here and set properties rather than have to update every subclass with the new params
		* 
		* @return string|false
	 */
	function getJavascript() {
		return $this->javascript();
	}
	
 
	/**
		* performs server-side validation.  Defaults to true, so field will not be validated
		* should be overridden in child classes
		* @TODO: update this to use properties set in validate() instead of arguments?
		* 
		* @return boolean
	 */
	function php($field, $ruleValue, $validator) {
		return true;
	}
	
	/**
		* just a wrapper for php() for now
		* 
		* @return boolean
	 */
	function validate($field, $ruleValue, $form) {
		
		return $this->php($field, $ruleValue, $form);
	}
	
	
	/**
		* returns the default error message for this method, in case none were defined for the field or validator
		* 
		* @return string
	 */
	function defaultMessage() {
		return $this->defaultMessage;
	}
	
	
	/**
		* filter to convert the rule value from PHP to JS format, if needed
		* 
		* @return string
	 */
	function convertRuleForJavascript($field, $ruleValue, $form) {
		return false;
	}
	
	
	
	
	
	
	
	
	
	
	
	
}