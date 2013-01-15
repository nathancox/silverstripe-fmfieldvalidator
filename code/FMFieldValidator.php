<?php


class FMFieldValidator extends Validator {

	/**
	 * Stores all the validation methods in name => ClassName format
	 * 
	 * @var array
	 */
	private static $validation_classes;

	/**
	 * An array of project-wide default error messages in [ruleName] => "message" format
	 * 
	 * @var array
	 */
	static $default_messages = array();



	/**
	 * The site-wide location to include jQuery from
	 * By default jQuery isn't included here since 90% of the time it's there already.
	 * You can set it in _config.php using FMFieldValidator::set_jquery_location(); if you aren't 
	 * including it elsewhere or you have an inclusion order problem.
	 * Version that comes with SilverStripe is at framework/thirdparty/jquery/jquery.min.js
	 * 
	 * @var string
	 */
	static $jquery_location = null;


	/**
	 * The site-wide location to include jQuery from
	 * By default we use our own copy of validate because it's newer than the one in the framework.
	 * You can set change it in _config.php using FMFieldValidator::set_jquery_validate_location();
	 * Version that comes with SilverStripe is at framework/thirdparty/jquery/jquery.min.js
	 * 
	 * @var string
	 */
	static $jquery_validate_location = 'fmfieldvalidator/thirdparty/jquery.validate.min.js';


	/**
	 * 
	 * 
	 * @var boolean
	 */
	static $include_javascript_methods = true;

	/**
	 * The instance-specific location to include jQuery from
	 * 
	 * @var string
	 */
	var $jQueryLocation = null;

	/**
	 * The instance-specific location to include jQuery from
	 * 
	 * @var string
	 */
	var $jQueryValidateLocation = null;


	/**
	 * 
	 * 
	 * @var boolean
	 */
	var $includeJavascriptMethods;



	/**
	 * for passing extra javascriptOptions to the js validate() method
	 * 
	 * @var boolean
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





	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Remove all validation rules
	 * 
	 * @todo Maybe it would be easier to just set a "don't validate" flag?
	 */
	public function removeValidation() {
		
		$fields = $this->getFields();

		foreach ($fields as $field) {
			$field->clearValidationRules();
		}
	}

	/**
	 * We include the javascript here instead of __construct so you have time to set
	 * 
	 * @param Form $form
	 */
	public function setForm($form) {
		$this->form = $form;

		$this->includeJQuery();
		$this->includeJQueryValidate();
		$this->includeValidation();
		$this->includeCustomJavascriptMethods();

		return $this;
	}



	
	/****************************** VALIDATION METHODS ******************************/

	/**
	 * Returns an array of available validation classes. Generates the list if it doesn't exist already and saves it to FMVieldValidator::$validation_classes
	 * 
	 * @return array of validation methods in the format [name] => classname
	 */
	static function get_validation_classes() {
		if (count(self::$validation_classes) == 0) {
			
			$methodClasses = ClassInfo::subclassesFor('FMValidationMethod');
			$methods = array();
			
			foreach($methodClasses as $class) {
				if ($class != 'FMValidationMethod') {
					$singleton = singleton($class);
					if ($singleton->ruleName == '') {
						trigger_error('Validation class '.$class.' has no $ruleName', E_USER_WARNING);
						continue;
					}
					if (isset($methods[$singleton->ruleName])) {
						trigger_error('Validation class with ruleName '.$singleton->ruleName.' already exists ('.$class.' & '.$methods[$singleton->ruleName]->class.')', E_USER_WARNING);
						continue;
					}
					$methods[$singleton->ruleName] = $class;
				}
			}
			self::$validation_classes = $methods;
		}
		
		return self::$validation_classes;
	}
	

	/**
	 * Finds all subclasses of FMValidationMethod and add them to a list (if it hasn't already)
	 * 
	 * @param string $ruleName The name of the validation method (eg "required")
	 * @param FormField|null $field The form field to load into the validation method (optional, this can be done manually later)
	 * @return FMValidationMethod or null
	 */
	static function get_validation_method($ruleName, $field = null) {
		if (count(self::$validation_classes) == 0) {
			self::get_validation_classes();
		}
		if (isset(self::$validation_classes[$ruleName])) {
			$className = self::$validation_classes[$ruleName];
			$method = new $className();
			if ($field) {
				$method->setField($field);
			}
			return $method;
		} else {
			trigger_error("Couldn't find an FMValidationMethod with ruleName \"$ruleName\"", E_USER_WARNING);
		}
	}


	/****************************** DEFAULT MESSAGES ******************************/
	
	/**
	 * overwrites/sets the $default_messages array, letting you define messages as a project level
	 * 
	 * @param array $messages
	 */
	static function set_default_messages($messages) {
		foreach($messages as $ruleName => $message) {
			self::add_default_message($ruleName, $message);
		}
	}

	/**
	 * @param string $ruleName 
	 * @param string $message 
	 */
	static function set_default_message($ruleName, $message) {
		self::$default_messages[$ruleName] = $message;
	}

	static function add_default_message($ruleName, $message) {
		self::set_default_message($ruleName, $message);
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
	static function get_default_message($ruleName) {
		if (isset(self::$default_messages[$ruleName])) {
			return self::$default_messages[$ruleName];
		} else {
			return false;
		}
	}



	/****************************** JAVASCRIPT VALIDATION ******************************/

	/**
	 * Includes the $(...).validate() block
	 * 
	 */
	function includeValidation() {
		$formID = $this->form->FormName();

		$params = '';
/*
		$rules = self::json_encode($this->getRulesForJavaScript());
		$messages = self::json_encode($this->getMessages());
*/


		$rules = $this->getRulesForJavaScript();
		$messages = $this->getMessages();
		$options = $this->getJavascriptOptionsWithDefaults();

		$options = array_merge(array('rules' => $rules, 'messages' => $messages), $options);

		$optionsJSON = self::json_encode($options);

		$script = <<<JS
			$().ready(function() {
				$("#{$formID}").validate({$optionsJSON});
			});
JS
;
		
		Requirements::customScript($script, $formID.'_Validation');
	}


	/**
	 * Includes the javascript for custom validation methods.  Uses FMFieldValidator::getCustomJavascriptMethods();
	 * 	Note that this is form-specific
	 * 
	 */
	function includeCustomJavascriptMethods() {

		if ($this->includeJavascriptMethods === true || (is_null($this->includeJavascriptMethods) && FMFieldValidator::$include_javascript_methods === true)) {

			$methods = $this->getCustomJavascriptMethods();
			foreach ($methods as $methodName => $script) {
				$js = <<<JS
							$.validator.addMethod("$methodName", $script);
JS;
			Requirements::customScript($js, 'FMValidator_'.$methodName);
			}

		}
	}




	/****************************** JAVASCRIPT FILES ******************************/

	/**
	 * Includes the jQuery file
	 * 
	 */
	function includeJQuery() {
		if ($this->jQueryLocation) {
			Requirements::javascript($this->jQueryLocation);
		} else if (self::$jquery_location) {
			Requirements::javascript(self::$jquery_location);
		}
		
	}

	/**
	 * Includes the jQuery file
	 * 
	 */
	function includeJQueryValidate() {
		if ($this->jQueryValidateLocation) {
			Requirements::javascript($this->jQueryValidateLocation);
		} else if (self::$jquery_validate_location) {
			Requirements::javascript(self::$jquery_validate_location);
		}
	}







	/**
	 * Set the project-wide location for including jQuery
	 * 
	 * @param string $file
	 */
	static function set_jquery_location($file = null) {
		self::$jquery_location = $file;
	}

	/**
	 * Set the instance-specific location for including jQuery
	 * 
	 * @param string $file
	 */
	function setJQueryLocation($file = null) {
		$this->jQueryLocation = $file;
		return $this;
	}


	/**
	 * Set the project-wide location for including jQuery Validate
	 * 
	 * @param string $file
	 */
	static function set_jquery_validate_location($file = null) {
		self::$jquery_validate_location = $file;
	}

	/**
	 * Set the instance-specific location for including jQuery Validate
	 * 
	 * @param string $file
	 */
	static function setJQueryValidateLocation($file = null) {
		$this->jQueryValidateLocation = $file;
		return $this;
	}


	/**
	 * Enable or disable automatically including the JS for custom validation rules (if for example you're putting them in a .js file manually)
	 * 
	 * @param boolean
	 */
	static function set_include_javascript_methods($include) {
		self::$include_javascript_methods = $include;
	}

	/**
	 * Enable or disable including the JS for custom validation rules for this form 
	 * 
	 * @param boolean
	 */
	static function setIncludeJavascriptMethods($include) {
		$this->includeJavascriptMethods = $include;
		return $this;
	}


	/****************************** JAVASCRIPT OPTIONS ******************************/

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



	/****************************** PHP VALIDATION ******************************/

	
	/**
	 * Performs server-side validation.  Returns true/false if the form is 
	 * valid but the validator actually checks validation error set to decide that.
	 * 
	 * @param array $data The form data
	 * @return boolean
	 */
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

















	/****************************** UTILTIY ******************************/

	function getFields() {
		return $this->form->fields->dataFields();
	}



	/**
	 * Convert a PHP array to json but DON'T encode functions
	 * 
	 * @param array $input
	 * @return string
	 */
	static function json_encode(array $input) {

		$replaced = array();

		foreach ($input as $key => $value) {
			if (is_array($value)) {
				$replaced['"%'.$key.'%"'] = self::json_encode($value);
				$input[$key] = '%'.$key.'%';
			} else if (strpos($value, 'function(') === 0) {
				$replaced['"%'.$key.'%"'] = $value;
				$input[$key] = '%'.$key.'%';
			}
		}

		$output = json_encode($input);


		$output = str_replace(array_keys($replaced), $replaced, $output);

		return $output;
	}


	/**
	 * Get all the validation rules for the associated form
	 * 
	 * @return 
	 */
	function getRules() {
		$fields = $this->getFields();
		$allRules = array();
		
		foreach ($fields as $field) {
			$fieldRules = $field->getValidationRules();
			$allRules[$field->getName()] = $fieldRules;
		}
		
		return $allRules;
	}

	/**
	 * Get all the validation rules for the associated form for use in javascript
	 * 
	 * @return 
	 */
	function getRulesForJavaScript() {
		$fields = $this->getFields();
		$allRules = array();
		
		foreach ($fields as $field) {
			$fieldRules = $field->getValidationRules();
			foreach ($fieldRules as $ruleName => $ruleValue) {
				$method = $field->getValidationMethod($ruleName);
				if ($ruleForJavaScript = $method->convertRuleForJavascript()) {
					$fieldRules[$ruleName] = $ruleForJavaScript;
				}
			}
			if ($fieldRules) {
				$allRules[$field->getName()] = $fieldRules;
			}
			
		}
		
		return $allRules;
	}


	/**
	 * Get all the validation messages for the associated form
	 * 
	 * @return array
	 */
	function getMessages() {
		$fields = $this->getFields();
		$allMessages = array();
		
		foreach ($fields as $field) {
			$fieldMessages = $field->getValidationMessages();
			if ($fieldMessages) {
				$allMessages[$field->getName()] = $fieldMessages;
			}
			
		}
		
		return $allMessages;
	}


	/**
	 * Get an array of the custom JS validation functions in $ruleName => "function() {}" format
	 * 
	 * @return array
	 */
	function getCustomJavascriptMethods() {
		// include custom rules
		$fields = $this->getFields();
		$methods = array();

		foreach ($fields as $field) {
			$fieldRules = $field->getValidationRules();
			foreach ($fieldRules as $ruleName => $ruleValue) {
				$method = $field->getValidationMethod($ruleName);
				if ($script = $method->javascript()) {
					$methods[$ruleName] = $script;
				}
			}
			
		}

		return $methods;
	}



}






