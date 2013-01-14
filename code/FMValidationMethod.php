<?php


class FMValidationMethod extends Object {

	/**
	 * Store the form field we're validating
	 * 
	 * @var FormField
	 */
	var $ruleName;


	/**
	 * The default error message
	 * 
	 * @var String
	 */
	var $defaultMessage = 'error validating field';


	/**
	 * For manually setting error messages
	 * 
	 * @var String
	 */
	var $message = false;


	/**
	 * Stores whether we've called validate();
	 * 
	 * @var boolean
	 */
	var $haveValidated = false;

	/**
	 * Stores whether the field is valid
	 * 
	 * @var boolean
	 */
	var $isValid = true;



	/**
	 * Store the form field we're validating
	 * 
	 * @var FormField
	 */
	private $field;

	/**
	 * __constructor
	 * 
	 * @param FormField $field The field we're validating against 
	 */
	function __construct(FormField $field = null) {
		if ($field) {
			$this->setField($field);
		}
		$this->setMessageType($this->getRuleName());
		parent::__construct();
	}



	/**
	 * Set the form field we're validating
	 * 
	 * @param FormField $field The field we're validating against 
	 */
	function setField(FormField $field) {
		$this->field = $field;
	}

	/**
	 * Get the form field we're validating
	 * 
	 * @return FormField
	 */
	function getField() {
		return $this->field;
	}


	/**
	 * Return the default error message
	 * 
	 * @return string
	 */
	function getDefaultMessage() {
		return $this->defaultMessage;
	}

	/**
	 * Get the form our field belongs to
	 * 
	 * @return FormField
	 */
	function getForm() {
		return $this->field->getForm();
	}

	/**
	 * Return the form field's value for this rule
	 * 
	 * @return string
	 */
	function getFieldRule() {
		if ($field = $this->getField()) {
			return $field->getValidationRule($this->getRuleName());
		}
		return false;
	}

	/**
	 * Return the form field's message for this 
	 * 
	 * @return string
	 */
	function getFieldMessage() {
		if ($field = $this->getField()) {
			return $field->getValidationMessage($this->getRuleName());
		}
		return false;
	}



	/**
	 * Return the rule name (eg "required" or "maxlength")
	 * 
	 * @return string
	 */
	function getRuleName() {
		return $this->ruleName;
	}


	/**
	 * Set the message type for the validator error (used as a classname on the field)
	 * 
	 * @return string
	 */
	function setMessageType($input) {
		$this->messageType = $input;
	}	

	/**
	 * Get the message type for the validator error (used as a classname on the field)
	 * 
	 * @return string
	 */
	function getMessageType() {
		return $this->messageType;
	}

	
	/**
	 * 
	 * 
	 * @return boolean
	 */
	function isValid() {
		return $this->isValid;
	}


		/**
	 * 
	 * 
	 * @param boolean
	 */
	function setValid($isValid, $dontMarkAsValidated = false) {
		$this->isValid = $isValid;
		if (!$dontMarkAsValidated) {
			$this->haveValidated = true;
		}
		
	}


	/**
	 * 
	 * 
	 * @return boolean
	 */
	function haveValidated() {
		return $this->haveValidated;
	}

	/**
	 * Manually set the error message
	 * 
	 * @return string
	 */
	function setMessage($message) {
		$this->message = $message;
	}


	/**
	 * Get the message after validation
	 * 
	 * @return string
	 */
	function getMessage() {
		if ($this->message) {
			return $this->message;
		}

		return false;
	}



	/**
	 * Returns the rule value converted for use in JavaScript if needed
	 * 
	 * @return mixed String or false to use the normal value
	 */
	function convertRuleForJavascript() {
		return false;
	}


	/****************************** VALIDATION ******************************/

	/**
	 * Get the javascript used to validate this rule client-side.  This should be overwritten in subclasses.
	 * 
	 * @return string
	 */
	function javascript() {
		return false;
	}

	/**
	 * The method to validate server side.  This should be overwritten in subclasses.
	 * 
	 * @return string
	 */
	function php($field, $ruleValue, $form) {
		return true;
	}


	/**
	 * The method to validate server side.  This should be overwritten in subclasses.
	 * 
	 * @return string
	 */
	function validate() {
		$result = $this->php($this->getField(), $this->getFieldRule(), $this->getForm());

		if (!is_null($result)) {
			if (is_array($result)) {
				$this->setValid($result['valid']);
				if (isset($result['message'])) {
					$this->setMessage($result['message']);
				}
			} else if (is_string($result)) {
				$this->setMessage($result);
				$this->setValid(false);
			} else if (is_bool($result)) {
				$this->setValid($result);
			}
		}

		$this->haveValidated = true;
		return $this->isValid();
	}
	

}