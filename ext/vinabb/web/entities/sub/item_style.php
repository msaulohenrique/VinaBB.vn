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
* Sub-entity for item_style + item_lang_tool + item_desc
*/
class item_style extends item_lang_tool
{
	/** @var array $data */
	protected $data;

	/** @var \vinabb\web\entities\helper\helper_interface $entity_helper */
	protected $entity_helper;

	/**
	* Constructor
	*
	* @param \vinabb\web\entities\helper\helper_interface $entity_helper Entity helper
	*/
	public function __construct(\vinabb\web\entities\helper\helper_interface $entity_helper)
	{
		$this->entity_helper = $entity_helper;
	}

	/**
	* Get the style property: Number of Presets
	*
	* @return int
	*/
	public function get_style_presets()
	{
		return isset($this->data['item_style_presets']) ? (int) $this->data['item_style_presets'] : 0;
	}

	/**
	* Set the style property: Number of Presets
	*
	* @param int			$value	Number of presets
	* @return item_style	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_presets($value)
	{
		$value = (int) $value;

		// This is a field only for styles
		if ($this->data['bb_type'] !== constants::BB_TYPE_STYLE && $this->data['bb_type'] !== constants::BB_TYPE_ACP_STYLE)
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
		return isset($this->data['item_style_presets_aio']) ? (bool) $this->data['item_style_presets_aio'] : false;
	}

	/**
	* Set the style property: All Presets in One Style
	*
	* @param bool			$value	true: yes; false: no
	* @return item_style	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_presets_aio($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->data['bb_type'] !== constants::BB_TYPE_STYLE && $this->data['bb_type'] !== constants::BB_TYPE_ACP_STYLE)
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
		return isset($this->data['item_style_source']) ? (bool) $this->data['item_style_source'] : false;
	}

	/**
	* Set the style property: Source Files
	*
	* @param bool			$value	true: yes; false: no
	* @return item_style	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_source($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->data['bb_type'] !== constants::BB_TYPE_STYLE && $this->data['bb_type'] !== constants::BB_TYPE_ACP_STYLE)
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
		return isset($this->data['item_style_responsive']) ? (bool) $this->data['item_style_responsive'] : false;
	}

	/**
	* Set the style property: Responsive Support
	*
	* @param bool			$value	true: yes; false: no
	* @return item_style	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_responsive($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->data['bb_type'] !== constants::BB_TYPE_STYLE && $this->data['bb_type'] !== constants::BB_TYPE_ACP_STYLE)
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
		return isset($this->data['item_style_bootstrap']) ? (bool) $this->data['item_style_bootstrap'] : false;
	}

	/**
	* Set the style property: Bootstrap Support
	*
	* @param bool			$value	true: yes; false: no
	* @return item_style	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_bootstrap($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->data['bb_type'] !== constants::BB_TYPE_STYLE && $this->data['bb_type'] !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_bootstrap'] = $value;

		return $this;
	}
}
