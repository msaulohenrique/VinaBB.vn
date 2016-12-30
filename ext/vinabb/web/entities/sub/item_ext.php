<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for item_ext + item_style + item_lang_tool + item_desc
*/
class item_ext extends item_style
{
	/** @var array $data */
	protected $data;

	/**
	* Get the extension property: Style Changes
	*
	* @return bool
	*/
	public function get_ext_style()
	{
		return isset($this->data['item_ext_style']) ? (bool) $this->data['item_ext_style'] : false;
	}

	/**
	* Set the extension property: Style Changes
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_style($value)
	{
		// Set the value on our data array
		$this->data['item_ext_style'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: ACP Style Changes
	*
	* @return bool
	*/
	public function get_ext_acp_style()
	{
		return isset($this->data['item_ext_acp_style']) ? (bool) $this->data['item_ext_acp_style'] : false;
	}

	/**
	* Set the extension property: ACP Style Changes
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_acp_style($value)
	{
		// Set the value on our data array
		$this->data['item_ext_acp_style'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: Language Files
	*
	* @return bool
	*/
	public function get_ext_lang()
	{
		return isset($this->data['item_ext_lang']) ? (bool) $this->data['item_ext_lang'] : false;
	}

	/**
	* Set the extension property: Language Files
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_lang($value)
	{
		// Set the value on our data array
		$this->data['item_ext_lang'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: Schema Changes
	*
	* @return bool
	*/
	public function get_ext_db_schema()
	{
		return isset($this->data['item_ext_db_schema']) ? (bool) $this->data['item_ext_db_schema'] : false;
	}

	/**
	* Set the extension property: Schema Changes
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_db_schema($value)
	{
		// Set the value on our data array
		$this->data['item_ext_db_schema'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: Data Changes
	*
	* @return bool
	*/
	public function get_ext_db_data()
	{
		return isset($this->data['item_ext_db_data']) ? (bool) $this->data['item_ext_db_data'] : false;
	}

	/**
	* Set the extension property: Data Changes
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_db_data($value)
	{
		// Set the value on our data array
		$this->data['item_ext_db_data'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: PHP Events
	*
	* @return bool
	*/
	public function get_ext_event()
	{
		return isset($this->data['item_ext_event']) ? (bool) $this->data['item_ext_event'] : false;
	}

	/**
	* Set the extension property: PHP Events
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_event($value)
	{
		// Set the value on our data array
		$this->data['item_ext_event'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: Template Events
	*
	* @return bool
	*/
	public function get_ext_event_style()
	{
		return isset($this->data['item_ext_event_style']) ? (bool) $this->data['item_ext_event_style'] : false;
	}

	/**
	* Set the extension property: Template Events
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_event_style($value)
	{
		// Set the value on our data array
		$this->data['item_ext_event_style'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: ACP Template Events
	*
	* @return bool
	*/
	public function get_ext_event_acp_style()
	{
		return isset($this->data['item_ext_event_acp_style']) ? (bool) $this->data['item_ext_event_acp_style'] : false;
	}

	/**
	* Set the extension property: ACP Template Events
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_event_acp_style($value)
	{
		// Set the value on our data array
		$this->data['item_ext_event_acp_style'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: ACP Modules
	*
	* @return bool
	*/
	public function get_ext_module_acp()
	{
		return isset($this->data['item_ext_module_acp']) ? (bool) $this->data['item_ext_module_acp'] : false;
	}

	/**
	* Set the extension property: ACP Modules
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_module_acp($value)
	{
		// Set the value on our data array
		$this->data['item_ext_module_acp'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: MCP Modules
	*
	* @return bool
	*/
	public function get_ext_module_mcp()
	{
		return isset($this->data['item_ext_module_mcp']) ? (bool) $this->data['item_ext_module_mcp'] : false;
	}

	/**
	* Set the extension property: MCP Modules
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_module_mcp($value)
	{
		// Set the value on our data array
		$this->data['item_ext_module_mcp'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: UCP Modules
	*
	* @return bool
	*/
	public function get_ext_module_ucp()
	{
		return isset($this->data['item_ext_module_ucp']) ? (bool) $this->data['item_ext_module_ucp'] : false;
	}

	/**
	* Set the extension property: UCP Modules
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_module_ucp($value)
	{
		// Set the value on our data array
		$this->data['item_ext_module_ucp'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: Notifications
	*
	* @return bool
	*/
	public function get_ext_notification()
	{
		return isset($this->data['item_ext_notification']) ? (bool) $this->data['item_ext_notification'] : false;
	}

	/**
	* Set the extension property: Notifications
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_notification($value)
	{
		// Set the value on our data array
		$this->data['item_ext_notification'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: Cron Tasks
	*
	* @return bool
	*/
	public function get_ext_cron()
	{
		return isset($this->data['item_ext_cron']) ? (bool) $this->data['item_ext_cron'] : false;
	}

	/**
	* Set the extension property: Cron Tasks
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_cron($value)
	{
		// Set the value on our data array
		$this->data['item_ext_cron'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: Console Commands
	*
	* @return bool
	*/
	public function get_ext_console()
	{
		return isset($this->data['item_ext_console']) ? (bool) $this->data['item_ext_console'] : false;
	}

	/**
	* Set the extension property: Console Commands
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_console($value)
	{
		// Set the value on our data array
		$this->data['item_ext_console'] = (bool) $value;

		return $this;
	}

	/**
	* Get the extension property: ext.php
	*
	* @return bool
	*/
	public function get_ext_ext_php()
	{
		return isset($this->data['item_ext_ext_php']) ? (bool) $this->data['item_ext_ext_php'] : false;
	}

	/**
	* Set the extension property: ext.php
	*
	* @param bool		$value	true: yes; false: no
	* @return item_ext	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_ext_php($value)
	{
		// Set the value on our data array
		$this->data['item_ext_ext_php'] = (bool) $value;

		return $this;
	}
}
