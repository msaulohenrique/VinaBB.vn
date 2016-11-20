<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

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
	public $data;

	/** @var string */
	public $prefix = 'text';

	/** @var bool */
	public $ignore_max_post_chars = true;

	/**
	* Get content for edit
	*
	* @return string
	*/
	public function get_text_for_edit()
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data[$this->prefix]) ? $this->data[$this->prefix] : '';
		$uid = isset($this->data[$this->prefix . '_uid']) ? $this->data[$this->prefix . '_uid'] : '';
		$options = isset($this->data[$this->prefix . '_options']) ? (int) $this->data[$this->prefix . '_options'] : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	* Get content for display
	*
	* @param bool $censor True to censor the content
	* @return string
	*/
	public function get_text_for_display($censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data[$this->prefix]) ? $this->data[$this->prefix] : '';
		$uid = isset($this->data[$this->prefix . '_uid']) ? $this->data[$this->prefix . '_uid'] : '';
		$bitfield = isset($this->data[$this->prefix . '_bitfield']) ? $this->data[$this->prefix . '_bitfield'] : '';
		$options = isset($this->data[$this->prefix . '_options']) ? (int) $this->data[$this->prefix . '_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
	}

	/**
	* Set text
	*
	* @param string				$text	Content
	* @return bbcode_content	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text)
	{
		// Override maximum post characters limit
		if ($this->ignore_max_post_chars)
		{
			$this->config['max_post_chars'] = 0;
		}

		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->bbcode_enabled(), $this->urls_enabled(), $this->smilies_enabled());

		// Set the value on our data array
		$this->data[$this->prefix] = $text;
		$this->data[$this->prefix . '_uid'] = $uid;
		$this->data[$this->prefix . '_bitfield'] = $bitfield;
		// Option flags are already set

		return $this;
	}

	/**
	* Check if BBCode is enabled on the content
	*
	* @return bool
	*/
	public function bbcode_enabled()
	{
		return (bool) ($this->data[$this->prefix . '_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	* Enable/Disable BBCode on the content
	* This should be called before set_text(); enable_bbcode()->set_text()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return bbcode_content	$this	Object for chaining calls: load()->set()->save()
	*/
	public function enable_bbcode($enable = true)
	{
		$this->set_text_options(OPTION_FLAG_BBCODE, !$enable);

		return $this;
	}

	/**
	* Check if URLs is enabled on the content
	*
	* @return bool
	*/
	public function urls_enabled()
	{
		return (bool) ($this->data[$this->prefix . '_options'] & OPTION_FLAG_LINKS);
	}

	/**
	* Enable/Disable URLs on the content
	* This should be called before set_text(); enable_urls()->set_text()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return bbcode_content	$this	Object for chaining calls: load()->set()->save()
	*/
	public function enable_urls($enable = true)
	{
		$this->set_text_options(OPTION_FLAG_LINKS, !$enable);

		return $this;
	}

	/**
	* Check if smilies are enabled on the content
	*
	* @return bool
	*/
	public function smilies_enabled()
	{
		return (bool) ($this->data[$this->prefix . '_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	* Enable/Disable smilies on the content
	* This should be called before set_text(); enable_smilies()->set_text()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return bbcode_content	$this	Object for chaining calls: load()->set()->save()
	*/
	public function enable_smilies($enable = true)
	{
		$this->set_text_options(OPTION_FLAG_SMILIES, !$enable);

		return $this;
	}

	/**
	* Set BBCode options for the content
	*
	* @param int	$value		Value of the option
	* @param bool	$negate		Negate (Unset) option
	* @param bool	$reparse	Reparse the content after setting option
	*/
	public function set_text_options($value, $negate = false, $reparse = true)
	{
		// Set article_text_options to 0 if it does not yet exist
		$this->data[$this->prefix . '_options'] = isset($this->data[$this->prefix . '_options']) ? $this->data[$this->prefix . '_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data[$this->prefix . '_options'] & $value))
		{
			// Add the option to the options
			$this->data[$this->prefix . '_options'] += $value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data[$this->prefix . '_options'] & $value)
		{
			// Subtract the option from the options
			$this->data[$this->prefix . '_options'] -= $value;
		}

		// Reparse the content
		if ($reparse && !empty($this->data[$this->prefix]))
		{
			$text = $this->data[$this->prefix];

			decode_message($text, $this->data[$this->prefix . '_uid']);

			$this->set_text($text);
		}
	}
}
