<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

use vinabb\web\includes\constants;

/**
* Sub-entity for item_lang_tool + item_desc
*/
class item_lang_tool extends item_desc
{
	/** @var array $data */
	protected $data;

	/** @var \vinabb\web\entities\helper\helper_interface $entity_helper */
	protected $entity_helper;

	/** @var int $bb_type */
	protected $bb_type;

	/**
	* Constructor
	*
	* @param \vinabb\web\entities\helper\helper_interface $entity_helper Entity helper
	*/
	public function __construct(\vinabb\web\entities\helper\helper_interface $entity_helper)
	{
		$this->entity_helper = $entity_helper;
		$this->bb_type = isset($this->data['bb_type']) ? $this->data['bb_type'] : 0;
	}

	/**
	* Get the language package property: ISO code
	*
	* @return string
	*/
	public function get_lang_iso()
	{
		return (($this->bb_type === constants::BB_TYPE_LANG) && isset($this->data['item_lang_iso'])) ? (string) $this->data['item_lang_iso'] : '';
	}

	/**
	* Set the language package property: ISO code
	*
	* @param string				$text	2-letter language ISO code
	* @return item_lang_tool	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function set_lang_iso($text)
	{
		$text = (string) $text;

		// This is a field only for language packages
		if ($this->bb_type !== constants::BB_TYPE_LANG)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_lang_iso', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_lang_iso($text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_lang_iso', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['item_lang_iso'] = $text;

		return $this;
	}

	/**
	* Get the tool property: OS Support
	*
	* @return int
	*/
	public function get_tool_os()
	{
		return (($this->bb_type === constants::BB_TYPE_TOOL) && isset($this->data['item_tool_os'])) ? (int) $this->data['item_tool_os'] : constants::OS_ALL;
	}

	/**
	* Set the tool property: OS Support
	*
	* @param int				$value	OS constant value
	* @return item_lang_tool	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_tool_os($value)
	{
		$value = (int) $value;

		// This is a field only for tools
		if ($this->bb_type !== constants::BB_TYPE_TOOL)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Do not set unknown OS
		if ($value > constants::OS_WP)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('item_tool_os');
		}

		// Set the value on our data array
		$this->data['item_tool_os'] = $value;

		return $this;
	}
}
