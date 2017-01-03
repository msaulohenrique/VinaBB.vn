<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\portal;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

/**
* Controller for the article
*/
class article implements article_interface
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

	/** @var string $php_ext */
	protected $php_ext;

	/** @var array $portal_cats */
	protected $portal_cats;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth									$auth			Authentication object
	* @param \vinabb\web\controllers\cache\service_interface	$cache			Cache service
	* @param \phpbb\config\config								$config			Config object
	* @param ContainerInterface									$container		Container object
	* @param \phpbb\language\language							$language		Language object
	* @param \phpbb\request\request								$request		Request object
	* @param \phpbb\template\template							$template		Template object
	* @param \phpbb\user										$user			User object
	* @param \phpbb\controller\helper							$helper			Controller helper
	* @param \vinabb\web\controllers\helper_interface			$ext_helper		Extension helper
	* @param string												$php_ext		PHP file extension
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->container = $container;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->php_ext = $php_ext;

		$this->portal_cats = $this->cache->get_portal_cats();
	}

	/**
	* View an article
	*
	* @param int	$article_id		Article ID
	* @param string	$tpl_filename	Template filename
	* @return \Symfony\Component\HttpFoundation\Response
	* @throws \phpbb\exception\http_exception
	*/
	public function article($article_id, $tpl_filename = 'portal_article.html')
	{
		try
		{
			/** @var \vinabb\web\entities\portal_article_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.portal_article')->load($article_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_PORTAL_ARTICLE');
		}

		// Category data
		$category_name = $this->portal_cats[$entity->get_cat_id()][($this->user->lang_name == constants::LANG_VIETNAMESE) ? 'name_vi' : 'name'];
		$cat_varname = $this->portal_cats[$entity->get_cat_id()]['varname'];

		// Tracking views
		$this->update_view_counter($article_id);

		// Breadcrumb
		$this->ext_helper->set_breadcrumb($this->language->lang('NEWS'), $this->helper->route('vinabb_web_portal_route'));
		$this->ext_helper->set_breadcrumb($category_name, $this->helper->route('vinabb_web_portal_cat_route', ['varname' => $cat_varname]));
		$this->ext_helper->set_breadcrumb($this->language->lang('PORTAL_ARTICLE'));

		// Author info
		$this->get_author_info($entity->get_user_id());

		// Comments
		$this->display_comments($article_id, $entity->get_user_id());
		$this->ext_helper->load_sceditor();

		$this->template->assign_vars([
			'ARTICLE_NAME'			=> $entity->get_name(),
			'ARTICLE_NAME_SHARE'	=> html_entity_decode($entity->get_name()),
			'ARTICLE_IMG'			=> $entity->get_img(),
			'ARTICLE_DESC'			=> $entity->get_desc(),
			'ARTICLE_DESC_SHARE'	=> html_entity_decode($entity->get_desc()),
			'ARTICLE_TEXT'			=> $entity->get_text_for_display(),
			'ARTICLE_TIME'			=> $this->user->format_date($entity->get_time()),

			'ARTICLE_SHARE_URL'	=> htmlspecialchars_decode($this->helper->get_current_url()),
			'U_PRINT'			=> $this->helper->route('vinabb_web_portal_article_print_route', ['varname' => $cat_varname, 'seo' => $entity->get_name_seo() . constants::REWRITE_URL_SEO, 'article_id' => $article_id]),
			'U_ACTION'			=> $this->helper->get_current_url(),

			'S_PORTAL_ARTICLE'	=> true
		]);

		return $this->helper->render($tpl_filename, $entity->get_name());
	}

	/**
	* Print the article
	*
	* @param int $article_id Article ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function print_page($article_id)
	{
		return $this->article($article_id, 'portal_article_print.html');
	}

	/**
	* Update the view counter
	*
	* @param int $article_id Article ID
	*/
	protected function update_view_counter($article_id)
	{
		$session_page = ($this->config['enable_mod_rewrite']) ? str_replace("app.{$this->php_ext}/", '', $this->user->data['session_page']) : $this->user->data['session_page'];

		if (generate_board_url() . '/' . $session_page != $this->helper->get_current_url() || isset($this->user->data['session_created']))
		{
			$this->container->get('vinabb.web.operators.portal_article')->increase_views($article_id);
		}
	}

	/**
	* Generate template variables for the article author
	*
	* @param int $user_id User ID
	*/
	public function get_author_info($user_id)
	{
		/** @var \vinabb\web\entities\user_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.user')->load($user_id);

		$avatar_row = [
			'user_avatar'			=> $entity->get_avatar(),
			'user_avatar_type'		=> $entity->get_avatar_type(),
			'user_avatar_width'		=> $entity->get_avatar_width(),
			'user_avatar_height'	=> $entity->get_avatar_height()
		];

		$this->template->assign_vars([
			'AUTHOR'			=> get_username_string('full', $user_id, $entity->get_username(), $entity->get_colour()),
			'AUTHOR_USERNAME'	=> $entity->get_username(),
			'AUTHOR_AVATAR'		=> ($avatar_row['user_avatar_type'] == 'avatar.driver.gravatar') ? $this->ext_helper->get_gravatar_url($avatar_row) : phpbb_get_user_avatar($avatar_row),

			'U_AUTHOR'	=> get_username_string('profile', $user_id, $entity->get_username(), $entity->get_colour()),
		]);
	}

	/**
	* Display comments from the article
	*
	* @param int	$article_id		Article ID
	* @param int	$author_user_id	User ID
	*/
	public function display_comments($article_id, $author_user_id)
	{
		$entities = $this->container->get('vinabb.web.operators.portal_comment')->get_comments($article_id);

		/** @var \vinabb\web\entities\portal_comment_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('comments', [
				'TEXT'	=> $entity->get_text_for_display(),
				'TIME'	=> $this->user->format_date($entity->get_time()),

				'S_AUTHOR'	=> $entity->get_user_id() == $author_user_id,
				'S_PENDING'	=> $entity->get_pending() == constants::ARTICLE_COMMENT_MODE_PENDING
			]);
		}
	}
}
