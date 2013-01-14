SilverStripe FMFieldValidator
===================================

Maintainer Contacts
-------------------
*  Nathan Cox (<nathan@flyingmonkey.co.nz>)

Requirements
------------
* SilverStripe 3.0+

Documentation
-------------
[GitHub Wiki](https://github.com/nathancox/silverstripe-fmfieldalidator/wiki)

Installation Instructions
-------------------------

1. Place the files in a directory called fmfieldvalidator in the root of your SilverStripe installation
2. Visit yoursite.com/dev/build to rebuild the database

Overview
--------------

FMFieldValidator is a custom validator for SilverStripe designed to work with the jquery.validate plugin.  It also features an extensible validation
rule system, project-wide defaults and other bits and pieces.

This is a variant of [FMValidator](https://github.com/nathancox/silverstripe-fmvalidator) that uses a FormField extension to store rules and messages instead of setting them in the validator itself

NOTE: This uses the same ValidationMethod classes FMValidator uses, so you'll have to delete one set if you install both modules in the same project for some reason.

Known Issues
------------
[Issue Tracker](https://github.com/nathancox/silverstripe-fmfieldalidator/issues)