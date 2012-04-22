<?php


/*
	MC Plain Plugin

	@package		ExpressionEngine
	@subpackage		Addons
	@category		Plugin
	@author			Michael C.
	@link			http://www.pro-image.co.il/
*/


$plugin_info = array(
	'pi_name'			=> 'MC Plain',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Michael C.',
	'pi_author_url'		=> 'http://www.pro-image.co.il/',
	'pi_description'	=> 'Plain-text values of the "email" member field (and any custom member fields containing email addresses).',
	'pi_usage'			=> Mc_Plain::usage()
);

class Mc_Plain
{

	function Mc_Plain()
	{

		/*
		 * As seen here: https://github.com/newism/nsm.body_class.ee_addon/pull/1/files
		 * @danott fork
		 * Technique from @erikreagan in Dan Benjamin's Shrimp plugin
		 * EE version check to properly reference our EE objects
		 */
		if (version_compare(APP_VER, '2', '<'))
		{
			// EE 1.x is in play
			global $TMPL, $DB;
			$this->TMPL =& $TMPL;
			$this->DB =& $DB;
		} else {
			// EE 2.x is in play
			$this->EE  =& get_instance();
			$this->TMPL =& $this->EE->TMPL;
			$this->DB =& $this->EE->DB;
		}
	}

	/**
	 * _log_item
	 *
	 * Write items to template debugging log
	 *
	 * @param      string    String to log
	 * @param      int       Tab indention
	 * @access     private
	 * @return     void
	 */
	private function _log_item($string = FALSE, $indent = 1)
	{

		if ($string)
		{
			$tab = str_repeat('&nbsp;', 4 * $indent);
			$this->TMPL->log_item($tab . '- ' . $string);
		}
	}
	// End function _log_item()


	/**
	 * Email
	 *
	 * This function returns the plain value of the specified member's 'email' field
	 *
	 * @access	public
	 * @return	string
	 */
	function email($member_id = '')
	{
		$member_id = $this->TMPL->fetch_param('member_id');

		// If member_id is blank
		if ($member_id == '')
		{
			$this->_log_item("ERROR in MC Plain plugin: No member_id specified; unable to continue.");
			return FALSE;
		}
		// If member_id is not a digit
		elseif ( ! ctype_digit($member_id))
		{

			$this->_log_item("ERROR in MC Plain plugin: Specified member_id is not a valid number; unable to continue.");
			return FALSE;
		}
		// fetch 'email' column value for $member_id
		else
		{
			$query = $this->DB->query("SELECT email
										FROM exp_members
										WHERE member_id = " . $this->DB->escape_str($member_id));
			return $query->row['email'];
		}
	} // END function email()


	/**
	 * Field
	 *
	 * This function returns the plain value of the specified field for the specified member
	 *
	 * @access	public
	 * @return	string
	 */
	function field($member_id = '',$field_name = '')
	{

		$member_id = $this->TMPL->fetch_param('member_id');
		$field_name = $this->TMPL->fetch_param('field_name');

		// If member_id is blank
		if ($member_id == '')
		{
			$this->_log_item("ERROR in MC Plain plugin: No member_id specified; unable to continue.");
			return FALSE;
		}
		// If member_id is not a digit
		elseif ( ! ctype_digit($member_id))
		{

			$this->_log_item("ERROR in MC Plain plugin: Specified member_id is not a valid number; unable to continue.");
			return FALSE;
		}
		elseif ($field_name == '')
		{
			$this->_log_item("ERROR in MC Plain plugin: No 'field_name' specified; unable to continue.");
			return FALSE;
		}
		// fetch $field_name column value for $member_id
		else
		{
			$query = $this->DB->query("SELECT m_field_id
										FROM exp_member_fields
										WHERE m_field_name = '" . $this->DB->escape_str($field_name) . "'");
			$column = 'm_field_id_' . $query->row['m_field_id'];
			$query = $this->DB->query("SELECT " . $column . "
										FROM exp_member_data
										WHERE member_id = " . $this->DB->escape_str($member_id));
			return $query->row[$column];
		}
	} // END function field()

	/**
	 * Usage
	 *
	 * This function describes how the plugin is used.
	 *
	 * @access	public
	 * @return	string
	 */
	function usage()
	{
		ob_start();
?>

This plugin allows you to bypass EE's forced javascript obfuscation of email addresses in member profile fields.

Examples
========

Fetch Member Email
------------------

	{exp:mc_plain:email member_id="{member_id}"}


Fetch Member Custom Field
-------------------------

	{exp:mc_plain:field member_id="{member_id}" field_name="custom-email"}

<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	} // END function usage()
}


/* End of file pi.mc_plain.php */
/* Location: /system/plugins/pi.mc_plain.php */