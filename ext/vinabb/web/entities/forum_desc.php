<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

class forum_desc
{
	/**
	* Data for this entity
	*
	* @var array
	*	forum_id
	*	parent_id
	*	left_id
	*	right_id
	*	forum_parents
	*	forum_name
	*	forum_name_seo
	*	forum_lang
	*	forum_desc
	*	forum_desc_uid
	*	forum_desc_bitfield
	*	forum_desc_options
	*	forum_rules
	*	forum_rules_uid
	*	forum_rules_bitfield
	*	forum_rules_options
	*	forum_topics_per_page
	*	forum_type
	*	forum_status
	*/
	protected $data;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db			Database object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db)
	{
		$this->db = $db;
	}

	/**
	 * Get item description for edit
	 *
	 * @return string
	 */
	public function get_desc_for_edit()
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data['item_desc']) ? $this->data['item_desc'] : '';
		$uid = isset($this->data['item_desc_uid']) ? $this->data['item_desc_uid'] : '';
		$options = isset($this->data['item_desc_options']) ? (int) $this->data['item_desc_options'] : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	 * Get item description for display
	 *
	 * @param bool $censor True to censor the text
	 * @return string
	 */
	public function get_desc_for_display($censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['item_desc']) ? $this->data['item_desc'] : '';
		$uid = isset($this->data['item_desc_uid']) ? $this->data['item_desc_uid'] : '';
		$bitfield = isset($this->data['item_desc_bitfield']) ? $this->data['item_desc_bitfield'] : '';
		$options = isset($this->data['item_desc_options']) ? (int) $this->data['item_desc_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
	}

	/**
	 * Set item description
	 *
	 * @param string				$text	Item description
	 * @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	 */
	public function set_desc($text)
	{
		// Override maximum post characters limit
		$this->config['max_post_chars'] = 0;

		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->desc_bbcode_enabled(), $this->desc_urls_enabled(), $this->desc_smilies_enabled());

		// Set the value on our data array
		$this->data['item_desc'] = $text;
		$this->data['item_desc_uid'] = $uid;
		$this->data['item_desc_bitfield'] = $bitfield;
		// Option flags are already set

		return $this;
	}

	/**
	 * Check if BBCode is enabled on the item description
	 *
	 * @return bool
	 */
	public function desc_bbcode_enabled()
	{
		return (bool) ($this->data['item_desc_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	 * Enable/Disable BBCode on the item description
	 * This should be called before set_desc(); desc_enable_bbcode()->set_desc()
	 *
	 * @param bool				$enable	true: enable; false: disable
	 * @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	 */
	public function desc_enable_bbcode($enable)
	{
		$this->set_desc_options(OPTION_FLAG_BBCODE, !$enable);

		return $this;
	}

	/**
	 * Check if URLs is enabled on the item description
	 *
	 * @return bool
	 */
	public function desc_urls_enabled()
	{
		return (bool) ($this->data['item_desc_options'] & OPTION_FLAG_LINKS);
	}

	/**
	 * Enable/Disable URLs on the item description
	 * This should be called before set_desc(); desc_enable_urls()->set_desc()
	 *
	 * @param bool				$enable	true: enable; false: disable
	 * @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	 */
	public function desc_enable_urls($enable)
	{
		$this->set_desc_options(OPTION_FLAG_LINKS, !$enable);

		return $this;
	}

	/**
	 * Check if smilies are enabled on the item description
	 *
	 * @return bool
	 */
	public function desc_smilies_enabled()
	{
		return (bool) ($this->data['item_desc_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	 * Enable/Disable smilies on the item description
	 * This should be called before set_desc(); desc_enable_smilies()->set_desc()
	 *
	 * @param bool				$enable	true: enable; false: disable
	 * @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	 */
	public function desc_enable_smilies($enable)
	{
		$this->set_desc_options(OPTION_FLAG_SMILIES, !$enable);

		return $this;
	}

	/**
	 * Set BBCode options for the item description
	 *
	 * @param int	$value				Value of the option
	 * @param bool	$negate				Negate (Unset) option
	 * @param bool	$reparse_content	Reparse the content after setting option
	 */
	protected function set_desc_options($value, $negate = false, $reparse_content = true)
	{
		// Set article_text_options to 0 if it does not yet exist
		$this->data['item_desc_options'] = isset($this->data['item_desc_options']) ? $this->data['item_desc_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data['item_desc_options'] & $value))
		{
			// Add the option to the options
			$this->data['item_desc_options'] += $value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data['item_desc_options'] & $value)
		{
			// Subtract the option from the options
			$this->data['item_desc_options'] -= $value;
		}

		// Reparse the content
		if ($reparse_content && $this->data['item_desc'] != '')
		{
			$text = $this->data['item_desc'];

			decode_message($text, $this->data['item_desc_uid']);

			$this->set_desc($text);
		}
	}
}