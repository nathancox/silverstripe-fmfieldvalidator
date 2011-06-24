<?php
/*
	Validate against a controller action.  The JS needs the strings "true" or "false", not the boolean true/false.
	The PHP validation can accept either.
	
	Example usage:
	
	// in the controller's Form method:
	$emailField = new EmailField('Email', 'Email Address')
	$emailField->setValidationRules(array(
		'remote' => array(
			'controller' => $this,
			'action' => 'emailexists'
		)
	));
	
	// 	the method to check against, on the same controller:
	
	function emailexists($input) {
		if (Director::is_ajax()) {
			$email = $input->getVar('Email');
		} else {
			$email = $input;
		}
		
		$result = DB::query(sprintf(
			"SELECT COUNT(*) FROM \"Member\" WHERE \"Email\" = '%s'",
			Convert::raw2sql($email)
		))->value();
		
		
		if ($result && $result > 0) {
			return 'false';
		} else {
			return 'true';
		};
	}
	
	
*/

class RemoteValidationMethod extends FMValidationMethod {
	var $name = 'remote';
	var $defaultMessage = 'this field is invalid';
	
	// we don't need to add any js, jquery.validate already supports this
	function javascript() {
		return false;
	}
	

	function php($field, $ruleValue, $form) {		
		$fieldValue = $field->value();
		$valid = false;
		
		if (is_string($ruleValue)) {
			// @TODO?
		} else {
			$controller = $ruleValue['controller'];
			$action = $ruleValue['action'];
			$valid = $controller->$action($fieldValue);
			if ($valid === 'true') {
				$valid = true;
			} else {
				$valid = false;
			}
		}
		
		return $valid;
	}
	
	function convertRuleForJavascript($field, $ruleValue, $form) {
		if (is_string($ruleValue)) {
			return $ruleValue;
		} else {
		//	$url = Director::absoluteURL($ruleValue['controller']->Link($ruleValue['action']));
			$url = $ruleValue['controller']->Link($ruleValue['action']) . '?ajax=1';
			return $url;
		}
		
	}
	
}