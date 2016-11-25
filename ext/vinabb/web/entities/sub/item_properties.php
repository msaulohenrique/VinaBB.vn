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
* Sub-entity for item properties and descriptions
*/
class item_properties extends item_desc
{
	/** @var array */
	protected $data;

	/** @var \vinabb\web\entities\helper\helper_interface */
	protected $entity_helper;

	/** @var int */
	private $bb_type;

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
	* @param bool				$value	Config value
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
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
	* @param bool				$value	Config value
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
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
	* @param bool				$value	Config value
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
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
	* @param bool				$value	Config value
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
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
	* @param bool				$value	Config value
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
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

	/**
	* Get the style property: Number of Presets
	*
	* @return int
	*/
	public function get_style_presets()
	{
		return (($this->bb_type === constants::BB_TYPE_STYLE || $this->bb_type === constants::BB_TYPE_ACP_STYLE) && isset($this->data['item_style_presets'])) ? (int) $this->data['item_style_presets'] : 0;
	}

	/**
	* Set the style property: Number of Presets
	*
	* @param int				$value	Config value
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_presets($value)
	{
		$value = (int) $value;

		// This is a field only for styles
		if ($this->bb_type !== constants::BB_TYPE_STYLE && $this->bb_type !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_presets'] = $value;

		return $this;
	}

	/**
	* Get the style property: All Presets in One Style
	*
	* @return bool
	*/
	public function get_style_presets_aio()
	{
		return (($this->bb_type === constants::BB_TYPE_STYLE || $this->bb_type === constants::BB_TYPE_ACP_STYLE) && isset($this->data['item_style_presets_aio'])) ? (bool) $this->data['item_style_presets_aio'] : false;
	}

	/**
	* Set the style property: All Presets in One Style
	*
	* @param bool				$value	Config value
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_presets_aio($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->bb_type !== constants::BB_TYPE_STYLE && $this->bb_type !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_presets_aio'] = $value;

		return $this;
	}

	/**
	* Get the style property: Source Files
	*
	* @return bool
	*/
	public function get_style_source()
	{
		return (($this->bb_type === constants::BB_TYPE_STYLE || $this->bb_type === constants::BB_TYPE_ACP_STYLE) && isset($this->data['item_style_source'])) ? (bool) $this->data['item_style_source'] : false;
	}

	/**
	* Set the style property: Source Files
	*
	* @param bool				$value	Config value
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_source($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->bb_type !== constants::BB_TYPE_STYLE && $this->bb_type !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_source'] = $value;

		return $this;
	}

	/**
	* Get the style property: Responsive Support
	*
	* @return bool
	*/
	public function get_style_responsive()
	{
		return (($this->bb_type === constants::BB_TYPE_STYLE || $this->bb_type === constants::BB_TYPE_ACP_STYLE) && isset($this->data['item_style_responsive'])) ? (bool) $this->data['item_style_responsive'] : false;
	}

	/**
	* Set the style property: Responsive Support
	*
	* @param bool				$value	Config value
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_responsive($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->bb_type !== constants::BB_TYPE_STYLE && $this->bb_type !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_responsive'] = $value;

		return $this;
	}

	/**
	* Get the style property: Bootstrap Support
	*
	* @return bool
	*/
	public function get_style_bootstrap()
	{
		return (($this->bb_type === constants::BB_TYPE_STYLE || $this->bb_type === constants::BB_TYPE_ACP_STYLE) && isset($this->data['item_style_bootstrap'])) ? (bool) $this->data['item_style_bootstrap'] : false;
	}

	/**
	* Set the style property: Bootstrap Support
	*
	* @param bool				$value	Config value
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_bootstrap($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->bb_type !== constants::BB_TYPE_STYLE && $this->bb_type !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_bootstrap'] = $value;

		return $this;
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
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
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
	* @return item_properties	$this	Object for chaining calls: load()->set()->save()
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
