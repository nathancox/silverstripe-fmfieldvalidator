<?php

FMValidator::ignore_missing_methods(false);


FMValidator::add_default_message('required', 'you need to fill in this field');

Object::add_extension('FormField', 'FMFormFieldExtension');