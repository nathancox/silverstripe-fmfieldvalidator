SilverStripe FMFieldValidator
===================================

FMFieldValidator is a custom validator for SilverStripe designed to work with the jquery.validate plugin.  It also features an extensible validation rule system, project-wide defaults and other bits and pieces.

For now have a look at the wiki for more information: https://github.com/nathancox/silverstripe-fmfieldalidator/wiki

Maintainer Contacts
-------------------
* Nathan Cox (<nathan@flyingmonkey.co.nz>)

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

This Version
--------------

This is a partially-rewritten version for SilverStripe 3 compatibility.  It has some changes to the inner workings of FMValidationMethod, new ways to configure rules on a field and a few other things that aren't documented because I haven't decided if I'll keep them yet.  Overall though it should work like the original version most of the time.

You can still to get the original SilverStripe 2.X version at https://github.com/nathancox/silverstripe-fmfieldvalidator/tree/2.0


Known Issues
------------
[Issue Tracker](https://github.com/nathancox/silverstripe-fmfieldalidator/issues)