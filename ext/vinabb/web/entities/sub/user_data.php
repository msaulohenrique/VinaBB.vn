<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for user_data + user_logtime + user_options + user_reg + user_profile + user_sig
*/
class user_data extends user_logtime
{
	/** @var array $data */
	protected $data;

	/** @var \phpbb\config\config $config */
	protected $config;

	/**
	* Constructor
	*
	* @param \phpbb\config\config $config Config object
	*/
	public function __construct(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	/**
	* Get the user language
	*
	* @return string
	*/
	public function get_lang()
	{
		return (string) (isset($this->data['user_lang']) ? $this->data['user_lang'] : $this->config['default_lang']);
	}

	/**
	* Set the user language
	*
	* @param string		$text	2-letter language ISO code
	* @return user_data	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_lang($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_lang', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_lang_iso($text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_lang', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['user_lang'] = $text;

		return $this;
	}

	/**
	* Get the user style
	*
	* @return int
	*/
	public function get_style()
	{
		return (int) (isset($this->data['user_style']) ? $this->data['user_style'] : $this->config['default_style']);
	}

	/**
	* Set the user style
	*
	* @param int		$id		Style ID
	* @return user_data	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_style($id)
	{
		$id = (int) $id;

		// This is a required field
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_style', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_style_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_style', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['user_style'] = $id;

		return $this;
	}

	/**
	* Get the user timezone
	*
	* @return string
	*/
	public function get_timezone()
	{
		return (string) (isset($this->data['user_timezone']) ? $this->data['user_timezone'] : $this->config['board_timezone']);
	}

	/**
	* Set the user timezone
	*
	* @param string		$text	UNIX timezone
	* @return user_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_timezone($text)
	{
		// Set the value on our data array
		$this->data['user_timezone'] = (string) $text;

		return $this;
	}

	/**
	* Get the user date format
	*
	* @return string
	*/
	public function get_dateformat()
	{
		return (string) (isset($this->data['user_dateformat']) ? $this->data['user_dateformat'] : $this->config['default_dateformat']);
	}

	/**
	* Set the user date format
	*
	* @param string		$text	PHP date format
	* @return user_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_dateformat($text)
	{
		// Set the value on our data array
		$this->data['user_dateformat'] = (string) $text;

		return $this;
	}

	/**
	* Get the user's posts
	*
	* @return int
	*/
	public function get_posts()
	{
		return isset($this->data['user_posts']) ? (int) $this->data['user_posts'] : 0;
	}

	/**
	* Set the user's posts
	*
	* @param int		$value	Number of posts
	* @return user_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_posts($value)
	{
		// Set the value on our data array
		$this->data['user_posts'] = (int) $value;

		return $this;
	}

	/**
	* Get the user's new PMs
	*
	* @return int
	*/
	public function get_new_privmsg()
	{
		return isset($this->data['user_new_privmsg']) ? (int) $this->data['user_new_privmsg'] : 0;
	}

	/**
	* Set the user new PMs
	*
	* @param int		$value	Number of new PMs
	* @return user_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_new_privmsg($value)
	{
		// Set the value on our data array
		$this->data['user_new_privmsg'] = (int) $value;

		return $this;
	}

	/**
	* Get the user's unread PMs
	*
	* @return int
	*/
	public function get_unread_privmsg()
	{
		return isset($this->data['user_unread_privmsg']) ? (int) $this->data['user_unread_privmsg'] : 0;
	}

	/**
	* Set the user's unread PMs
	*
	* @param int		$value	Number of unread PMs
	* @return user_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_unread_privmsg($value)
	{
		// Set the value on our data array
		$this->data['user_unread_privmsg'] = (int) $value;

		return $this;
	}

	/**
	* Get the user's warnings
	*
	* @return int
	*/
	public function get_warnings()
	{
		return isset($this->data['user_warnings']) ? (int) $this->data['user_warnings'] : 0;
	}

	/**
	* Set the user's warnings
	*
	* @param int		$value	Number of warnings
	* @return user_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_warnings($value)
	{
		// Set the value on our data array
		$this->data['user_warnings'] = (int) $value;

		return $this;
	}

	/**
	* Get the user's PM rules
	*
	* @return int
	*/
	public function get_message_rules()
	{
		return isset($this->data['user_message_rules']) ? (int) $this->data['user_message_rules'] : 0;
	}

	/**
	* Set the user's PM rules
	*
	* @param int		$value	Number of PM rules
	* @return user_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_message_rules($value)
	{
		// Set the value on our data array
		$this->data['user_message_rules'] = (int) $value;

		return $this;
	}
}
