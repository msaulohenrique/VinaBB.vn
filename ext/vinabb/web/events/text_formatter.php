<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* PHP events
*/
class text_formatter implements EventSubscriberInterface
{
	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \vinabb\web\events\helper\helper_interface */
	protected $event_helper;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface $cache
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \vinabb\web\events\helper\helper_interface $event_helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\vinabb\web\events\helper\helper_interface $event_helper
	)
	{
		$this->cache = $cache;
		$this->language = $language;
		$this->template = $template;
		$this->event_helper = $event_helper;
	}

	/**
	* List of phpBB's PHP events to be used
	*
	* @return array
	*/
	static public function getSubscribedEvents()
	{
		return [
			'core.modify_format_display_text_after'		=> 'modify_format_display_text_after',
			'core.modify_submit_post_data'				=> 'modify_submit_post_data',
			'core.modify_text_for_display_after'		=> 'modify_text_for_display_after',
			'core.modify_text_for_edit_before'			=> 'modify_text_for_edit_before',
			'core.modify_text_for_storage_after'		=> 'modify_text_for_storage_after',
			'core.submit_pm_before'						=> 'submit_pm_before',
			'core.text_formatter_s9e_configure_before'	=> 'text_formatter_s9e_configure_before',
			'core.text_formatter_s9e_configure_after'	=> 'text_formatter_s9e_configure_after'
		];
	}

	/**
	* core.modify_format_display_text_after
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_format_display_text_after($event)
	{
		$event['text'] = $this->event_helper->render($this->event_helper->parse($event['text']));
	}

	/**
	* core.modify_submit_post_data
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_submit_post_data($event)
	{
		$data = $event['data'];
		$data['message'] = $this->event_helper->parse($data['message']);
		$event['data'] = $data;
	}

	/**
	* core.modify_text_for_display_after
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_text_for_display_after($event)
	{
		$event['text'] = $this->event_helper->render($event['text']);

		// Load highlight.js
		$this->template->assign_var('S_LOAD_HIGHLIGHT', true);
	}

	/**
	* core.modify_text_for_edit_before
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_text_for_edit_before($event)
	{
		$event['text'] = $this->event_helper->unparse($event['text']);
	}

	/**
	* core.modify_text_for_storage_after
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_text_for_storage_after($event)
	{
		$event['text'] = $this->event_helper->parse($event['text']);
	}

	/**
	* core.submit_pm_before
	*
	* @param array $event Data from the PHP event
	*/
	public function submit_pm_before($event)
	{
		$data = $event['data'];
		$data['message'] = $this->event_helper->parse($data['message']);
		$event['data'] = $data;
	}

	/**
	* core.text_formatter_s9e_configure_before
	*
	* @param array $event Data from the PHP event
	*/
	public function text_formatter_s9e_configure_before($event)
	{
		$configurator = $event['configurator'];

		foreach ($configurator->MediaEmbed->defaultSites->getIds() as $site_id)
		{
			if (in_array($site_id, ['facebook', 'twitter', 'googleplus', 'youtube', 'flickr', 'instagram', 'gist']))
			{
				$configurator->MediaEmbed->add($site_id);
			}
		}

		// Add our site
		$configurator->MediaEmbed->add('vinabb', [
			'host'		=> 'vinabb.vn',
			'extract'	=> [
				"!vinabb\\.vn/viewtopic\\.php\\?f=(?'f'[0-9]+)\\&t=(?'t'[0-9]+)!",
				"!vinabb\\.vn/viewtopic\\.php\\?t=(?'t'[0-9]+)!"
			],
			'iframe'	=> [
				'width'		=> 560,
				'height'	=> 260,
				'src'		=> 'http://localhost/vinabb/embed/topic/{@t}'
			]
		]);
	}

	/**
	* core.text_formatter_s9e_configure_after
	*
	* @param array $event Data from the PHP event
	*/
	public function text_formatter_s9e_configure_after($event)
	{
		$configurator = $event['configurator'];

		/**
		* Use backticks to post inline code: `$phpBB`
		*
		* https://github.com/s9e/phpbb-ext-incode
		* @copyright Copyright (c) 2015 The s9e Authors
		*/
		$action = $configurator->tags->onDuplicate('ignore');

		$configurator->Preg->replace('/`(.*?)`/', '<code class="inline">$1</code>', 'C');
		$configurator->tags->onDuplicate($action);

		// Remove old smiley data
		unset($configurator->Emoticons);

		// Set new smiley data with emoticon based on user language
		foreach ($this->cache->get_smilies() as $smiley_code => $smiley_data)
		{
			if ($this->language->is_set(['EMOTICON_TEXT', strtoupper($smiley_data['emotion'])]))
			{
				$emotion_text = '{$LE_' . strtoupper($smiley_data['emotion']) . '}';
			}
			else
			{
				$emotion_text = $smiley_data['emotion'];
			}

			$configurator->Emoticons->add($smiley_code, '<img class="smilies" src="{$T_SMILIES_PATH}/' . $smiley_data['url'] . '" width="' . $smiley_data['width'] . '" height="' . $smiley_data['height'] . '" alt="{.}" data-tooltip="true" title="' . $emotion_text . '"/>');
		}

		if (isset($configurator->Emoticons))
		{
			// Force emoticons to be rendered as text if $S_VIEWSMILIES is not set
			$configurator->Emoticons->notIfCondition = 'not($S_VIEWSMILIES)';

			// Only parse emoticons at the beginning of the text or if they're preceded by any
			// one of: a new line, a space, a dot, or a right square bracket
			$configurator->Emoticons->notAfter = '[^\\n .\\]]';
		}
	}
}
