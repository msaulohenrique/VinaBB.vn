<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\board;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

/**
* Controller for the single post page
*/
class post implements post_interface
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

	/** @var string $root_path */
	protected $root_path;

	/** @var string $php_ext */
	protected $php_ext;

	/** @var array $forum_data */
	protected $forum_data;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth									$auth		Authentication object
	* @param \vinabb\web\controllers\cache\service_interface	$cache		Cache service
	* @param ContainerInterface									$container	Container object
	* @param \phpbb\language\language							$language	Language object
	* @param \phpbb\template\template							$template	Template object
	* @param \phpbb\user										$user		User object
	* @param \phpbb\controller\helper							$helper		Controller helper
	* @param \vinabb\web\controllers\helper_interface			$ext_helper	Extension helper
	* @param string												$root_path	phpBB root path
	* @param string												$php_ext	PHP file extension
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\vinabb\web\controllers\cache\service_interface $cache,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->container = $container;
		$this->language = $language;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->forum_data = $this->cache->get_forum_data();
	}

	/**
	* Main method
	*
	* @param int	$post_id		Post ID
	* @param string	$tpl_filename	Template filename
	* @return \Symfony\Component\HttpFoundation\Response
	* @throws \phpbb\exception\http_exception
	*/
	public function main($post_id, $tpl_filename = 'viewpost_body.html')
	{
		try
		{
			/** @var \vinabb\web\entities\post_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.post')->load($post_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_POST');
		}

		if (!$this->auth->acl_gets('f_list', 'f_read', $entity->get_forum_id()))
		{
			send_status_line(403, 'Forbidden');
			trigger_error('SORRY_AUTH_READ');
		}

		/** @var \vinabb\web\entities\topic_interface $topic */
		$topic = $this->container->get('vinabb.web.entities.topic')->load($entity->get_topic_id());

		// Poster info
		if ($entity->get_poster_id())
		{
			$this->get_poster_info($entity->get_poster_id());
		}
		else
		{
			$this->template->assign_var('POSTER_USERNAME', $entity->get_username());
		}

		// Breadcrumb
		$this->ext_helper->set_breadcrumb($this->language->lang('BOARD'), $this->helper->route('vinabb_web_board_route'));
		$this->ext_helper->set_breadcrumb($this->forum_data[$entity->get_forum_id()]['name'], $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $entity->get_forum_id(), 'seo' => $this->forum_data[$entity->get_forum_id()]['name_seo'] . constants::REWRITE_URL_SEO]));
		$this->ext_helper->set_breadcrumb($topic->get_title(), $this->helper->route('vinabb_web_board_topic_route', ['forum_id' => $entity->get_forum_id(), 'topic_id' => $entity->get_topic_id(), 'seo' => $topic->get_title_seo() . constants::REWRITE_URL_SEO]));
		$this->ext_helper->set_breadcrumb($this->language->lang('POST'));

		$this->template->assign_vars([
			'POST_SUBJECT'		=> $entity->get_subject(),
			'POST_TEXT'			=> $entity->get_text_for_display(),
			'POST_TIME'			=> $this->user->format_date($entity->get_time()),
			'POST_TIME_EMBED'	=> $this->user->format_date($entity->get_time(), 'd/m/Y H:i'),

			'U_POST'	=> $this->helper->get_current_url()
		]);

		return $this->helper->render($tpl_filename);
	}

	/**
	* Get poster data
	*
	* @param int $poster_id Poster user ID
	*/
	public function get_poster_info($poster_id)
	{
		/** @var \vinabb\web\entities\user_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.user')->load($poster_id);

		$avatar_row = [
			'user_avatar'			=> $entity->get_avatar(),
			'user_avatar_type'		=> $entity->get_avatar_type(),
			'user_avatar_width'		=> $entity->get_avatar_width(),
			'user_avatar_height'	=> $entity->get_avatar_height()
		];

		$this->template->assign_vars([
			'POSTER'			=> get_username_string('full', $poster_id, $entity->get_username(), $entity->get_colour()),
			'POSTER_USERNAME'	=> $entity->get_username(),
			'POSTER_AVATAR'		=> ($avatar_row['user_avatar_type'] == 'avatar.driver.gravatar') ? $this->ext_helper->get_gravatar_url($avatar_row) : phpbb_get_user_avatar($avatar_row),

			'U_POSTER'	=> $this->helper->route('vinabb_web_user_profile_route', ['username' => $entity->get_username()])
		]);
	}
}
