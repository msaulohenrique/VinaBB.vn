<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for post_poster + post_options + post_actions + post_text
*/
class post_poster extends post_options
{
	/** @var array $data */
	protected $data;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \vinabb\web\entities\helper\helper_interface $entity_helper */
	protected $entity_helper;

	/**
	* Constructor
	*
	* @param \phpbb\language\language						$language		Language object
	* @param \vinabb\web\entities\helper\helper_interface	$entity_helper	Entity helper
	*/
	public function __construct(\phpbb\language\language $language, \vinabb\web\entities\helper\helper_interface $entity_helper)
	{
		$this->language = $language;
		$this->entity_helper = $entity_helper;
	}

	/**
	* Get the poster ID
	*
	* @return int
	*/
	public function get_poster_id()
	{
		return isset($this->data['poster_id']) ? (int) $this->data['poster_id'] : 0;
	}

	/**
	* Set the poster ID
	*
	* @param int			$id		User ID
	* @return post_poster	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_poster_id($id)
	{
		$id = (int) $id;

		// Check existing user
		if ($id && !$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['poster_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['poster_id'] = $id;

		return $this;
	}

	/**
	* Get the poster IP
	*
	* @return string
	*/
	public function get_poster_ip()
	{
		return isset($this->data['poster_ip']) ? (string) $this->data['poster_ip'] : '';
	}

	/**
	* Set the poster IP
	*
	* @param string			$text	User IP
	* @return post_poster	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_poster_ip($text)
	{
		$text = (string) $text;

		// Checking for valid IP address
		if ($text != '' && filter_var($text, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false && filter_var($text, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['poster_ip', 'INVALID_IP']);
		}

		// Set the value on our data array
		$this->data['poster_ip'] = $text;

		return $this;
	}

	/**
	* Get the guest poster username
	*
	* @return string
	*/
	public function get_username()
	{
		return !empty($this->data['post_username']) ? (string) $this->data['post_username'] : $this->language->lang('GUEST');
	}

	/**
	 * Set the guest poster username
	 *
	 * @param string		$text	Username
	 * @return post_poster	$this	Object for chaining calls: load()->set()->save()
	 * @throws \vinabb\web\exceptions\unexpected_value
	 */
	public function set_username($text)
	{
		// Set the value on our data array
		$this->data['post_username'] = (string) $text;

		return $this;
	}

	/**
	* Get the post time
	*
	* @return int
	*/
	public function get_time()
	{
		return isset($this->data['post_time']) ? (int) $this->data['post_time'] : 0;
	}

	/**
	* Set the post time
	*
	* @return post_poster $this Object for chaining calls: load()->set()->save()
	*/
	public function set_time()
	{
		// Set the value on our data array
		$this->data['post_time'] = time();

		return $this;
	}
}
