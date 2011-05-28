<?php


class FMValidator extends Validator {
	var $rules = array();
	var $messages = array();
	
	/**
	 * for passing extra javascriptOptions to the js validate() method
	 */
	var $extraJavascriptOptions = array();
	
	/**
	 * for setting project-wide javascriptOptions
	 */
	static $default_javascript_options = array(
		'errorClass' => "message",
		'errorElement' => "span",
		'errorPlacement' => "function(error, element) { error.appendTo($(element).closest('.field')); }"
	);
	
	/**
	 * an array of singletons for the subclasses of FMValidationMethod
	 */
	static $validation_methods = array();
	
	/**
	 * an array of project-wide default error messages in [ruleName] => "message" format
	 */
	static $default_messages = array();
	
	/**
	 * if false, throws an error when told to validate a method that doesn't exist, if true it just ignores the rule
	 */
	static $ignore_missing_methods = false;
	
	
	/**
	 * 
	 * @param array $rules 
	 * @param array $messages 
	 */
	function __construct($rules = null, $messages = null) {
		if (is_array($rules)) {
			$this->setRules($rules);
		}
		
		if (is_array($messages)) {
			$this->setMessages($messages);
		}
		
		parent::__construct();
	}
	
	
	function getField($fieldName) {
		return $this->form->dataFieldByName($fieldName);
	}
	
	/****************************** RULES ******************************/
	
	/**
		* @return array
	 */
	function getRules() {
		// @TODO
		return $this->rules;
	}
	
	/**
	 * @param string $field 
	 * @return array
	 */
	function getRulesForField($fieldName) {
		if ($field = $this->getField($fieldName) && $rules = $field->getValidationRules()) {
			return $rules;
		} else {
			return array();
		}
	}
	
	/**
	 * @param array $rules 
	 */
	function setRules($rules) {
		foreach($rules as $fieldName => $fieldRules) {
			$this->setRulesForField($fieldName, $fieldRules);
		}
	}
	
	/**
	 * @param string $fieldName 
	 * @param array $rules 
	 */
	function setRulesForField($fieldName, $rules) {
		if (!is_array($rules)) {
			$rules = array(
				$rules => true
			);
		}
		
		if ($field = $this->getField($fieldName)) {
			$field->setRules($rules);
		}
		
	}
	
	/**
	 * @param string $fieldName 
	 * @param string $rule 
	 * @param string $value 
	 */
	function addRule($fieldName, $rule, $value) {
		if ($field = $this->getField($fieldName)) {
			$field->addRule($rule, $value);
		}
	}

	
	
	/****************************** MESSAGES ******************************/

	/**
	 * 
	 */
	function getMessages() {
		// @TODO
		return $this->messages;
	}
	
	/**
	 * @param string $fieldName 
	 * @return array
	 */
	function getMessagesForField($fieldName) {
		if ($field = $this->getField($fieldName) && $messages = $field->getValidationMessages()) {
			return $messages;
		} else {
			return array();
		}
	}
	
	
	/**
	 * Returns all messages for all fields, using getErrorMessage()
	 * 
	 * @return array
	 */
	function getErrorMessages() {
		// @TODO
		$output = array();
		
		$rules = $this->getRules();
		foreach ($rules as $fieldName => $fieldRules) {
			$output[$fieldName] = array();
			foreach ($fieldRules as $ruleName => $ruleValue) {
				$output[$fieldName][$ruleName] = $this->getErrorMessage($ruleName, $fieldName);
			}
			
		}
		
		return $output;
	}
	
	/**
	 * Returns the message for a field, falling back to default if not defined
	 * 
	 * @param string $ruleName 
	 * @param string $fieldName 
	 * @return string
	 */
	function getErrorMessage($ruleName, $fieldName = false) {
		// @TODO
		if ($fieldName && isset($this->messages[$fieldName]) && isset($this->messages[$fieldName][$ruleName])) {
			return $this->messages[$fieldName][$ruleName];
		} else if ($defaultMessage = self::get_default_message($ruleName)) {
			return $defaultMessage;
		} else if ($method = self::get_validation_method($ruleName)) {
			return $method->defaultMessage();
		} else {
			return 'error validating field';
		}
	}
	
	
	/**
	 * @param array $messages 
	 */
	function setMessages($messages) {
		foreach($messages as $fieldName => $fieldMessages) {
			$this->setMessagesForField($fieldName, $fieldMessages);
		}
	}
	
	/**
	 * @param string $fieldName
	 * @param array $messages 
	 */
	function setMessagesForField($fieldName, $messages) {
		if ($field = $this->getField($fieldName)) {
			if (is_array($messages)) {
				$field->setValidationMessages($messages);
			} else {
				if (is_array($field->getValidationRules())) {
					$firstRuleName = key($field->getValidationRules());
					if (!$field->getValidationMessage($firstRuleName)) {
						$this->setValidationMessage($firstRuleName, $messages);
					}
				}
			}
		}
	}

	/**
	 * @param string $fieldName 
	 * @param string $rule 
	 * @param string $message 
	 */
	function addMessage($fieldName, $rule, $message) {
		$field = $this->getField($fieldName);
		$field->addValidationMessage($rule, $message);
	}



	/**
	 * overwrites/sets the $default_messages array, letting you define messages as a project level
	 * 
	 * @param array $messages
	 */
	static function set_default_messages($messages) {
		if (is_array($messages)) {
			foreach($messages as $ruleName => $message) {
				self::addDefaultMessage($ruleName, $message);
			}
		}
	}

	/**
	 * @param string $ruleName 
	 * @param string $message 
	 */
	static function add_default_message($ruleName, $message) {
		self::$default_messages[$ruleName] = $message;
	}
	
	
	/**
	 * @return array
	 */
	static function get_default_messages() {
		return self::$default_messages;
	}
	
	/**
	 * @param string $ruleName 
	 * @return string
	 */
	function get_default_message($ruleName) {
		if (isset(self::$default_messages[$ruleName])) {
			return self::$default_messages[$ruleName];
		} else {
			return false;
		}
	}
	

	/****************************** EXTRA PARAMETERS ******************************/

	/**
	 * 
	 */
	function getJavascriptOptions() {
		return $this->extraJavascriptOptions;
	}
	
	/**
	 * 
	 */
	function getJavascriptOptionsWithDefaults() {
		$options = array_merge(self::get_default_javascript_options(), $this->getJavascriptOptions());
		return $options;
	}
	
	/**
	 * @param string $paramName  
	 */
	function getJavascriptOption($paramName) {
		if (isset($this->extraJavascriptOptions[$paramName])) {
			$this->extraJavascriptOptions[$paramName];
		} else {
			return false;
		}
	}
	
	
	/**
	 * @param array $javascriptOptions 
	 */
	function setJavascriptOptions($javascriptOptions) {
		foreach($javascriptOptions as $javascriptOptionName => $javascriptOptionValue) {
			$this->addJavascriptOption($javascriptOptionName, $javascriptOptionValue);
		}
	}

	/**
	 * @param string $javascriptOptionName
	 * @param array $messages 
	 */
	function addJavascriptOption($javascriptOptionName, $javascriptOptionValue) {
		if ($javascriptOptionName == 'rules' || $javascriptOptionName == 'messages') {
			return;
		}
		$this->extraJavascriptOptions[$javascriptOptionName] = $javascriptOptionValue;
	}
	
	
	/*
		have static default_javascriptOptions
			eventually array_merged with regular args so ones specific to this instance will overwrite global ones
	*/
	
	
	/**
	 * @param array $javascriptOptions
	 */
	static function set_default_javascript_options($options) {
		if (is_array($options)) {
			self::$default_javascript_options = $options;
		}
	}
	
	/**
	 * @param string optionName
	 * @param string optionValue
	 */
	static function set_default_javascript_option($optionName, $optionValue) {
		self::$default_javascript_options[$optionName] = $optionValue;
	}


	/**
	 * @return array
	 */
	static function get_default_javascript_options() {
		return self::$default_javascript_options;
	}
	
	/**
	 * @param string optionName
	 * @return string
	 */
	static function get_default_javascript_option($optionName) {
		if (isset(self::$default_javascript_options[$optionName])) {
			return self::$default_javascript_options[$optionName];
		} else {
			return false;
		}
	}

	/****************************** JAVASCRIPT ******************************/

	function includeJavascriptValidation() {
		$formID = $this->form->FormName();
		$params = $this->javascript();
		
		// @TODO: find a better way of including the js that doesn't involve hacking here if we're using a different version or jQuery or something
		Requirements::javascript('http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js');
		Requirements::javascript('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.8/jquery.validate.min.js');
		
		$this->includeCustomJavascriptMethods();
		
		$script = <<<JS
			$().ready(function() {
				$("#{$formID}").validate({
					$params
				});
			});
JS
;
		
		Requirements::customScript($script, $formID.'_Validation');
		
		if($this->form) {
			$this->form->jsValidationIncluded = true;
		}
	}
	
	// @TODO: why is this in a different method?
	function javascript() {
		// we can't just merge the arrays and Convert::array2json() the whole thing because functions in
		// extra javascriptOptions get converted and break
	//	$output = '{';
		$output = '"rules": ' . Convert::array2json($this->getRulesForJavascript());
		$output .= ', "messages": ' . Convert::array2json($this->getErrorMessages());
		
		// loop over the extra javascriptOptions
		foreach ($this->getJavascriptOptionsWithDefaults() as $key => $value) {
			$output .= ', "' . $key . '": ';
			// need to treat/quote it differently depending on what the value is
			if (is_array($value)) {
				$output .= Convert::array2json($value);
			} else if (substr(trim($value), 0, 8) == 'function') {
				$output .= $value;
			} else if (is_string($value)) {
					$output .= '"' . $value . '"';
			} else {
				$output .= $value;
			}
		}
		
//		$output .= '}';
		
		return $output;
	}
	
	
	/**
		* returns the rules array, but filtered for anything that needs to change when used in the javascript
		* 
		* @return array
	 */
	function getRulesForJavascript() {
		$allRules = $this->getRules();
		$form = $this->form;
		
		foreach ($allRules as $fieldName => $rules) {
			$field = $this->form->dataFieldByName($fieldName);
			foreach ($rules as $ruleName => $ruleValue) {
			
				if ($method = self::get_validation_method($ruleName)) {
					if ($newValue = $method->convertRuleForJavascript($field, $ruleValue, $form)) {
						$allRules[$fieldName][$ruleName] = $newValue;
					}
				}
				
			}
		}
		
		return $allRules;
	}
	
	
	function includeCustomJavascriptMethods() {
		// include custom rules
		$fields = $this->form->Fields();
		$rules = $this->getRules();
		
		foreach ($rules as $fieldName => $fieldRules) {
			foreach ($fieldRules as $ruleName => $ruleValue) {
				$method = self::get_validation_method($ruleName);
				if ($method && $script = $method->javascript()) {
					Requirements::customScript(<<<JS
						$.validator.addMethod("$ruleName", $script);
JS
					, 'FMValidator_'.$ruleName);
				}
			}
		}
		
	}
	
	

	/****************************** PHP VALIDATION ******************************/

	
	
	function php($data) {
		$valid = true;

		$fields = $this->form->Fields();
		$rules = $this->getRules();

		
		foreach($rules as $fieldName => $fieldRules) {
			$field = $fields->dataFieldByName($fieldName);
			if (!$field) {
				// @TODO: what?  Ignore it?  Fail validaton?
			//	trigger_error('Trying to validate non-existent field '.$fieldName);
				continue;
			}
			
			// @TODO: do we want to use the field's built-in validation too?
			//$valid = ($field->validate($this) && $valid);
			
			
			// loop over each rule
			foreach($fieldRules as $ruleName => $ruleValue) {
				// if this is a valid rule then check against it, otherwise have an error so we know
				if ($method = self::get_validation_method($ruleName)) {
					$validates = $method->validate($field, $ruleValue, $this->form);
					$valid = ($validates && $valid);
					
					if (!$validates) {
						$this->validationError(
							$fieldName,
							$this->getErrorMessage($ruleName, $fieldName),
							$ruleName
						);
					}
					
				} else {
					if (!self::ignore_missing_methods()) {
						trigger_error('Field '.$fieldName.' is trying to validate with a non-existant rule ('.$ruleName.')');
						//	info('@TODO: disabled trigger_error for invalid method names ('.$ruleName.')');
					}
				}
			}
				
				
			
		}
		
		return $valid;
	}
	
	
	
	
	/****************************** VALIDATION METHODS ******************************/

	/**
		* finds all subclasses of FMValidationMethod and adds singletons to a list (if it hasn't already)
		* 
		* @return array
	 */
	static function get_validation_methods() {
		if (count(self::$validation_methods) == 0) {
			
			$methodCLasses = ClassInfo::subclassesFor('FMValidationMethod');
			$methods = array();
			
			foreach($methodCLasses as $class) {
				if ($class != 'FMValidationMethod') {
					$singleton = singleton($class);
					if ($singleton->name == '') {
						trigger_error('Validation class '.$class.' has no $name');
						continue;
					}
					if (isset($methods[$singleton->name])) {
						trigger_error('Validation class with name '.$singleton->name.' already exists ('.$class.' & '.$methods[$singleton->name]->class.')');
						continue;
					}
					$methods[$singleton->name] = $singleton;
				}
			}
			self::$validation_methods = $methods;
		}
		
		return self::$validation_methods;
	}
	
	/**
		* finds all subclasses of FMValidationMethod and adds singletons to a list (if it hasn't already)
		* 
		* @return mixed FMValidationMethod or false
	 */
	static function get_validation_method($methodName) {
		if (count(self::$validation_methods) == 0) {
			self::get_validation_methods();
		}
		if (isset(self::$validation_methods[$methodName])) {
			return self::$validation_methods[$methodName];
		} else {
			return false;
		}
	}

	
	/**
		* if passed a boolean, will set $ignore_missing_methods, if not passed anything will return $ignore_missing_methods
		* 
		* @param bool
		* @return bool
	 */
	static function ignore_missing_methods($set = null) {
		if (is_null($set)) {
			return self::$ignore_missing_methods;
		} else {
			self::$ignore_missing_methods = ($set ? true : false);
		}
	}


	/******************************  ******************************/


	/**
	 * Returns true if the named field is "required".
	 * 
	 * @param string $fieldName 
	 * @return bool
	 */
	function fieldIsRequired($fieldName) {
		if (isset($this->rules[$fieldName]['required'])) {
			return true;
		}
		return false;
	}



}






