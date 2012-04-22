Plugin: MC Plain
================
This plugin allows you to bypass EE's forced javascript obfuscation of email addresses in member profile fields.


Changelog
=========
- 1.0 (2012-04-22):
	- Initial public release


Examples
========

Fetch Member Email
------------------

	{exp:mc_plain:email member_id="{member_id}"}


Fetch Member Custom Field
-------------------------

	{exp:mc_plain:field member_id="{member_id}" field_name="custom-email"}