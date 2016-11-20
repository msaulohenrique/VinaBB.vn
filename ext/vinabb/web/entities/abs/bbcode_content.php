<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\abs;

/**
* Abstract entity for a single BBCode content field
*/
abstract class bbcode_content
{
	/**
	* Data for this abstract entity
	*
	* @var array
	*	...
	*		{prefix}
	*		{prefix}_uid
	*		{prefix}_bitfield
	*		{prefix}_options
	*	...
	*/
	protected $data;

	/** @var bool */
	protected $ignore_max_post_chars = true;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config	Config object
	* @param \phpbb\db\driver\driver_interface	$db		Database object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db
	)
	{
		$this->config = $config;
		$this->db = $db;
	}

	/**
	* Get content for edit
	*
	* @param string $prefix Column prefix
	* @return string
	*/
	public function get_for_edit($prefix)
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data[$prefix]) ? $this->data[$prefix] : '';
		$uid = isset($this->data[$prefix . '_uid']) ? $this->data[$prefix . '_uid'] : '';
		$options = isset($this->data[$prefix . '_options']) ? (int) $this->data[$prefix . '_options'] : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	* Get content for display
	*
	* @param string	$prefix	Column prefix
	* @param bool	$censor	True to censor the content
	* @return string
	*/
	public function get_for_display($prefix, $censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data[$prefix]) ? $this->data[$prefix] : '';
		$uid = isset($this->data[$prefix . '_uid']) ? $this->data[$prefix . '_uid'] : '';
		$bitfield = isset($this->data[$prefix . '_bitfield']) ? $this->data[$prefix . '_bitfield'] : '';
		$options = isset($this->data[$prefix . '_options']) ? (int) $this->data[$prefix . '_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
	}

	/**
	* Set text
	*
	* @param string				$prefix	Column prefix
	* @param string				$text	Content
	* @return bbcode_content	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set($prefix, $text)
	{
		// Override maximum post characters limit
		if ($this->ignore_max_post_chars)
		{
			$this->config['max_post_chars'] = 0;
		}

		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->bbcode_enabled($prefix), $this->urls_enabled($prefix), $this->smilies_enabled($prefix));

		// Set the value on our data array
		$this->data[$prefix] = $text;
		$this->data[$prefix . '_uid'] = $uid;
		$this->data[$prefix . '_bitfield'] = $bitfield;
		// Option flags are already set

		return $this;
	}

	/**
	* Check if BBCode is enabled on the content
	*
	* @param string $prefix Column prefix
	* @return bool
	*/
	public function bbcode_enabled($prefix)
	{
		return (bool) ($this->data[$prefix . '_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	* Enable/Disable BBCode on the content
	* This should be called before set(); enable_bbcode()->set()
	*
	* @param string				$prefix	Column prefix
	* @param bool				$enable	true: enable; false: disable
	* @return bbcode_content	$this	Object for chaining calls: load()->set()->save()
	*/
	public function enable_bbcode($prefix, $enable = true)
	{
		$this->set_options($prefix, OPTION_FLAG_BBCODE, !$enable);

		return $this;
	}

	/**
	* Check if URLs is enabled on the content
	*
	* @param string $prefix Column prefix
	* @return bool
	*/
	public function urls_enabled($prefix)
	{
		return (bool) ($this->data[$prefix . '_options'] & OPTION_FLAG_LINKS);
	}

	/**
	* Enable/Disable URLs on the content
	* This should be called before set(); enable_urls()->set()
	*
	* @param string				$prefix	Column prefix
	* @param bool				$enable	true: enable; false: disable
	* @return bbcode_content	$this	Object for chaining calls: load()->set()->save()
	*/
	public function enable_urls($prefix, $enable = true)
	{
		$this->set_options($prefix, OPTION_FLAG_LINKS, !$enable);

		return $this;
	}

	/**
	* Check if smilies are enabled on the content
	*
	* @param string $prefix Column prefix
	* @return bool
	*/
	public function smilies_enabled($prefix)
	{
		return (bool) ($this->data[$prefix . '_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	* Enable/Disable smilies on the content
	* This should be called before set(); enable_smilies()->set()
	*
	* @param string				$prefix	Column prefix
	* @param bool				$enable	true: enable; false: disable
	* @return bbcode_content	$this	Object for chaining calls: load()->set()->save()
	*/
	public function enable_smilies($prefix, $enable = true)
	{
		$this->set_options($prefix, OPTION_FLAG_SMILIES, !$enable);

		return $this;
	}

	/**
	* Set BBCode options for the content
	*
	* @param string	$prefix		Column prefix
	* @param int	$value		Value of the option
	* @param bool	$negate		Negate (Unset) option
	* @param bool	$reparse	Reparse the content after setting option
	*/
	protected function set_options($prefix, $value, $negate = false, $reparse = true)
	{
		// Set {$prefix}_options to 0 if it does not yet exist
		$this->data[$prefix . '_options'] = isset($this->data[$prefix . '_options']) ? $this->data[$prefix . '_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data[$prefix . '_options'] & $value))
		{
			// Add the option to the options
			$this->data[$prefix . '_options'] += $value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data[$prefix . '_options'] & $value)
		{
			// Subtract the option from the options
			$this->data[$prefix . '_options'] -= $value;
		}

		// Reparse the content
		if ($reparse && !empty($this->data[$prefix]))
		{
			$text = $this->data[$prefix];

			decode_message($text, $this->data[$prefix . '_uid']);

			$this->set($prefix, $text);
		}
	}
}
