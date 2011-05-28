<?php


class FMFieldValidator extends Validator {
	
	/**
	 * an array of project-wide default error messages in [ruleName] => "message" format
	 */
	static $default_messages = array();
	
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
	//static $default_messages = array();
	
	/**
	 * if false, throws an error when told to validate a method that doesn't exist, if true it just ignores the rule
	 */
	static $ignore_missing_methods = false;
	
	
	/**
	 * 
	 */
	function __construct() {
		parent::__construct();
	}
	
	
	function getField($fieldName) {
		return $this->form->dataFieldByName($fieldName);
	}
	
	
	function getFields() {
		return $this->form->fields();
	}
	
	
	function getValidation() {
		$fields = $this->getFields();
		$form = $this->form;
		
		$allRules = array();
		$allMessages = array();
		
		foreach ($fields as $field) {
			$fieldName = $field->Name();
			
			$fieldRules = $field->getValidationRules();
			if ($fieldRules) {
				$allRules[$fieldName] = $fieldRules;
				
				$fieldMessages = $field->getValidationMessages();
				$allMessages[$fieldName] = $fieldMessages;
			}
		}
		
		return array(
			'rules' => $allRules,
			'messages' => $allMessages
		);
	}

	function getForm() {
		return $this->form;
	}
	
	
	/****************************** RULES ******************************/
	
	function getValidationRules() {
		$fields = $this->getFields();
		$form = $this->form;
		$allRules = array();
		
		foreach ($fields as $field) {
			$fieldRules = $field->getValidationRules();
			$fieldName = $field->Name();
			$allRules[$fieldName] = $fieldRules;
		}
		
		return $allRules;
	}
	
	/****************************** MESSAGES ******************************/

	
	function getValidationMessages() {
		$fields = $this->getFields();
		$form = $this->form;
		$allMessages = array();
		
		foreach ($fields as $field) {
			$fieldMessages = $field->getValidationMessages();
			$fieldName = $field->Name();
			$allMessages[$fieldName] = $fieldMessages;
		}
		
		return $allMessages;
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
	
		$validation = $this->getValidation();
		
		// don't do Convert::array2json($validation) because it wraps in an extra {} that we don't want
		$output = '"rules": ' . Convert::array2json($validation['rules']);
		$output .= ', "messages": ' . Convert::array2json($validation['messages']);
		
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
		$fields = $this->getFields();
		$form = $this->form;
		$allRules = array();
		
		foreach ($fields as $field) {
			$rules = $field->getValidationRules();
			$fieldName = $field->Name();
			foreach ($rules as $ruleName => $ruleValue) {
				if ($method = self::get_validation_method($ruleName)) {
					if ($newValue = $method->convertRuleForJavascript($field, $ruleValue, $form)) {
						if (!isset($allRules[$fieldName])) {
							$allRules[$fieldName] = array();
						}
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
		$rules = $this->getValidationRules();
		
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

		$fields = $this->getFields();
		$form = $this->form;
		
		foreach ($fields as $field) {
			$validates = $field->validatePHP($this);
			$valid = ($validates && $valid);
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






