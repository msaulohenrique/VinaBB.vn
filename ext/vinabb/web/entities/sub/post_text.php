<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for post/post_text
*/
class post_text
{
	/**
	* Data for this abstract entity
	*
	* @var array
	*	...
	*		post_text
	*		bbcode_uid
	*		bbcode_bitfield
	*		enable_bbcode
	*		enable_smilies
	*		enable_magic_url
	*	...
	*/
	protected $data;

	/**
	* Get post content for edit
	*
	* @return string
	*/
	public function get_text_for_edit()
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data['post_text']) ? $this->data['post_text'] : '';
		$uid = isset($this->data['bbcode_uid']) ? $this->data['bbcode_uid'] : '';
		$options = (isset($this->data['enable_bbcode']) && $this->data['enable_bbcode'] === true) ? OPTION_FLAG_BBCODE : 0;
		$options |= (isset($this->data['enable_magic_url']) && $this->data['enable_magic_url'] === true) ? OPTION_FLAG_LINKS : 0;
		$options |= (isset($this->data['enable_smilies']) && $this->data['enable_smilies'] === true) ? OPTION_FLAG_SMILIES : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	* Get post content for display
	*
	* @param bool $censor True to censor the content
	* @return string
	*/
	public function get_text_for_display($censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['post_text']) ? $this->data['post_text'] : '';
		$uid = isset($this->data['bbcode_uid']) ? $this->data['bbcode_uid'] : '';
		$bitfield = isset($this->data['bbcode_bitfield']) ? $this->data['bbcode_bitfield'] : '';
		$options = (isset($this->data['enable_bbcode']) && $this->data['enable_bbcode'] === true) ? OPTION_FLAG_BBCODE : 0;
		$options |= (isset($this->data['enable_magic_url']) && $this->data['enable_magic_url'] === true) ? OPTION_FLAG_LINKS : 0;
		$options |= (isset($this->data['enable_smilies']) && $this->data['enable_smilies'] === true) ? OPTION_FLAG_SMILIES : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
	}

	/**
	* Set post content
	*
	* @param string		$text	Post content
	* @return post_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text)
	{
		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->text_bbcode_enabled(), $this->text_urls_enabled(), $this->text_smilies_enabled());

		// Set the value on our data array
		$this->data['post_text'] = $text;
		$this->data['bbcode_uid'] = $uid;
		$this->data['bbcode_bitfield'] = $bitfield;
		// Option flags are already set

		return $this;
	}

	/**
	* Check if BBCode is enabled on the post content
	*
	* @return bool
	*/
	public function text_bbcode_enabled()
	{
		return (bool) $this->data['enable_bbcode'];
	}

	/**
	* Enable/Disable BBCode on the post content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return post_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode($enable = true)
	{
		$this->set_text_options('enable_bbcode', $enable);

		return $this;
	}

	/**
	* Check if URLs is enabled on the post content
	*
	* @return bool
	*/
	public function text_urls_enabled()
	{
		return (bool) $this->data['enable_magic_url'];
	}

	/**
	* Enable/Disable URLs on the post content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return post_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls($enable = true)
	{
		$this->set_text_options('enable_magic_url', $enable);

		return $this;
	}

	/**
	* Check if smilies are enabled on the post content
	*
	* @return bool
	*/
	public function text_smilies_enabled()
	{
		return (bool) $this->data['enable_smilies'];
	}

	/**
	* Enable/Disable smilies on the post content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return post_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies($enable = true)
	{
		$this->set_text_options('enable_smilies', $enable);

		return $this;
	}

	/**
	* Set BBCode options for the content
	*
	* @param string	$field		Value of the option
	* @param bool	$enable		Enable or disable the option
	* @param bool	$reparse	Reparse the content after setting option
	*/
	protected function set_text_options($field, $enable = true, $reparse = true)
	{
		$this->data[$field] = (bool) $enable;

		// Reparse the content
		if ($reparse && !empty($this->data['post_text']))
		{
			$text = $this->data['post_text'];

			decode_message($text, $this->data['bbcode_uid']);

			$this->set_text($text);
		}
	}
}
