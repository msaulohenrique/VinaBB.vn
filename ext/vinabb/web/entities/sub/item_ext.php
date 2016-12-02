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
* Sub-entity for item_ext + item_style + item_lang_tool + item_desc
*/
class item_ext extends item_style
{
	/** @var array */
	protected $data;

	/** @var \vinabb\web\entities\helper\helper_interface */
	protected $entity_helper;

	/** @var int */
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
	* Get the extension property: Style Changes
	*
	* @return bool
	*/
	public function get_ext_style()
	{
		return ($this->bb_type === constants::BB_TYPE_EXT && isset($this->data['item_ext_style'])) ? (bool) $this->data['item_ext_style'] : false;
	}

	/**
	* Set the extension property: Style Changes
	*
	* @param bool		$value	Config value
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_style($value)
	{
		$value = (bool) $value;

		// This is a field only for extensions
		if ($this->bb_type !== constants::BB_TYPE_EXT)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_ext_style'] = $value;

		return $this;
	}

	/**
	* Get the extension property: ACP Style Changes
	*
	* @return bool
	*/
	public function get_ext_acp_style()
	{
		return ($this->bb_type === constants::BB_TYPE_EXT && isset($this->data['item_ext_acp_style'])) ? (bool) $this->data['item_ext_acp_style'] : false;
	}

	/**
	* Set the extension property: ACP Style Changes
	*
	* @param bool		$value	Config value
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_acp_style($value)
	{
		$value = (bool) $value;

		// This is a field only for extensions
		if ($this->bb_type !== constants::BB_TYPE_EXT)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_ext_acp_style'] = $value;

		return $this;
	}

	/**
	* Get the extension property: Language Changes
	*
	* @return bool
	*/
	public function get_ext_lang()
	{
		return ($this->bb_type === constants::BB_TYPE_EXT && isset($this->data['item_ext_lang'])) ? (bool) $this->data['item_ext_lang'] : false;
	}

	/**
	* Set the extension property: Language Changes
	*
	* @param bool		$value	Config value
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_lang($value)
	{
		$value = (bool) $value;

		// This is a field only for extensions
		if ($this->bb_type !== constants::BB_TYPE_EXT)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_ext_lang'] = $value;

		return $this;
	}

	/**
	* Get the extension property: Schema Changes
	*
	* @return bool
	*/
	public function get_ext_db_schema()
	{
		return ($this->bb_type === constants::BB_TYPE_EXT && isset($this->data['item_ext_db_schema'])) ? (bool) $this->data['item_ext_db_schema'] : false;
	}

	/**
	* Set the extension property: Schema Changes
	*
	* @param bool		$value	Config value
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_db_schema($value)
	{
		$value = (bool) $value;

		// This is a field only for extensions
		if ($this->bb_type !== constants::BB_TYPE_EXT)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_ext_db_schema'] = $value;

		return $this;
	}

	/**
	* Get the extension property: Data Changes
	*
	* @return bool
	*/
	public function get_ext_db_data()
	{
		return ($this->bb_type === constants::BB_TYPE_EXT && isset($this->data['item_ext_db_data'])) ? (bool) $this->data['item_ext_db_data'] : false;
	}

	/**
	* Set the extension property: Data Changes
	*
	* @param bool		$value	Config value
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_db_data($value)
	{
		$value = (bool) $value;

		// This is a field only for extensions
		if ($this->bb_type !== constants::BB_TYPE_EXT)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_ext_db_data'] = $value;

		return $this;
	}
}
