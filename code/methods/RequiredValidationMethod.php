<?php
/**
 * Makes a field required.
 * Uses the default required checking from jquery.validate on the client side
 */
class RequiredValidationMethod extends FMValidationMethod {
	var $ruleName = 'required';
	var $defaultMessage = 'this field is required';

	// we don't need to add any js, jquery.validate already supports this
	/*
	function javascript() {
		return false;
	}
	*/

	function php($field, $ruleValue, $form) {
	//	$field = $this->getField();
	//	$ruleValue = $this->getFieldRule();
		$fieldValue = $field->value();

		$valid = false;

		// if it was specified as false for some reason then return true, since it's not required
		if (!$ruleValue) {
			return true;
		}

		if ($field instanceof FileField && isset($fieldValue['error']) && $fieldValue['error']) {
			$valid = false;
		} else if (is_array($fieldValue)) {
			$valid = (count($fieldValue)) ? true : false;
		} else if ($field instanceof CheckboxField) {
			$valid = ($fieldValue) ? true : false;
		} else {
			$valid = (strlen($fieldValue)) ? true : false;
		}


		$this->setValid($valid);
	}

}