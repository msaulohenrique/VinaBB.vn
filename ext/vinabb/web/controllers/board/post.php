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

class post
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

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
	* Display a post
	*
	* @param int $post_id Post ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($post_id)
	{
		if (!$post_id)
		{
			trigger_error('NO_POST');
		}
		else
		{
			// Initiate and load the entity
			/** @var \vinabb\web\entities\post_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.post')->load($post_id);

			if (!$this->auth->acl_gets('f_list', 'f_read', $entity->get_forum_id()))
			{
				trigger_error('SORRY_AUTH_READ');
			}

			/** @var \vinabb\web\entities\topic_interface $topic */
			$topic = $this->container->get('vinabb.web.entities.topic')->load($entity->get_topic_id());

			/** @var \vinabb\web\entities\user_interface $poster */
			$poster = $this->container->get('vinabb.web.entities.user')->load($entity->get_poster_id());

			// Breadcrumb
			$this->ext_helper->set_breadcrumb($this->language->lang('BOARD'), $this->helper->route('vinabb_web_board_route'));
			$this->ext_helper->set_breadcrumb($this->forum_data[$entity->get_forum_id()]['name'], $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $entity->get_forum_id(), 'seo' => $this->forum_data[$entity->get_forum_id()]['name_seo'] . constants::REWRITE_URL_SEO]));
			$this->ext_helper->set_breadcrumb($topic->get_title(), $this->helper->route('vinabb_web_board_topic_route', ['forum_id' => $entity->get_forum_id(), 'topic_id' => $entity->get_topic_id(), 'seo' => $topic->get_title_seo() . constants::REWRITE_URL_SEO]));
			$this->ext_helper->set_breadcrumb($this->language->lang('POST'));

			$this->template->assign_vars([
				'POST_SUBJECT'	=> $entity->get_subject(),
				'POST_TEXT'		=> $entity->get_text_for_display(),
				'POSTER'		=> ($entity->get_poster_id()) ? get_username_string('full', $poster->get_id(), $poster->get_username(), $poster->get_colour()) : (($entity->get_username() != '') ? $entity->get_username() : $this->language->lang('GUEST')),
				'POST_TIME'		=> $this->user->format_date($entity->get_time()),

				'U_POST'	=> $this->helper->route('vinabb_web_board_post_route', ['foorum_id' => $entity->get_forum_id(), 'topic_id' => $entity->get_topic_id(), 'post_id' => $post_id, 'seo' => $entity->get_subject_seo() . constants::REWRITE_URL_SEO]),
				'U_POSTER'	=> ($entity->get_poster_id()) ? $this->helper->route('vinabb_web_user_profile_route', ['username' => $poster->get_username()]) : ''
			]);
		}

		return $this->helper->render('viewpost_body.html');
	}
}
