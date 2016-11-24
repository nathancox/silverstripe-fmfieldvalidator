<?php
/*
	Test class: actually does the same thing as "equal"
*/
class IsExactlyValidationMethod extends FMValidationMethod {
	var $ruleName = 'isExactly';

	function javascript() {
		$script = <<<JS
			function(value, element, param) {
				if (value == param) {
					return true;
				} else {
					return false;
				}
			}
JS
		;
		return $script;
	}

	function php($field, $ruleValue, $form) {
		$fieldValue = $this->getField()->value();

		if ($fieldValue !== $this->getFieldRule()) {
			$this->setValid(false);
		}

	}


	function defaultMessage() {
		return 'wrong answer';
	}

}