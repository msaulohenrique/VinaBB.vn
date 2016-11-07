<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\decorated\help\controller;

/**
* BBCode help page
*/
class bbcode extends controller
{
	/**
	* @return string The title of the page
	*/
	public function display()
	{
		// Breadcrumb
		$this->template->assign_block_vars('breadcrumb', [
			'NAME'	=> $this->language->lang('BBCODE_GUIDE')
		]);

		$this->language->add_lang('help/bbcode');

		$this->manager->add_block(
			'HELP_BBCODE_BLOCK_INTRO',
			false,
			[
				'HELP_BBCODE_INTRO_BBCODE_QUESTION' => 'HELP_BBCODE_INTRO_BBCODE_ANSWER'
			]
		);

		$this->manager->add_block(
			'HELP_BBCODE_BLOCK_TEXT',
			false,
			[
				'HELP_BBCODE_TEXT_BASIC_QUESTION' => 'HELP_BBCODE_TEXT_BASIC_ANSWER',
				'HELP_BBCODE_TEXT_COLOR_QUESTION' => 'HELP_BBCODE_TEXT_COLOR_ANSWER',
				'HELP_BBCODE_TEXT_COMBINE_QUESTION' => 'HELP_BBCODE_TEXT_COMBINE_ANSWER'
			]
		);

		$this->manager->add_block(
			'HELP_BBCODE_BLOCK_QUOTES',
			false,
			[
				'HELP_BBCODE_QUOTES_TEXT_QUESTION' => 'HELP_BBCODE_QUOTES_TEXT_ANSWER',
				'HELP_BBCODE_QUOTES_CODE_QUESTION' => 'HELP_BBCODE_QUOTES_CODE_ANSWER'
			]
		);

		$this->manager->add_block(
			'HELP_BBCODE_BLOCK_LISTS',
			false,
			[
				'HELP_BBCODE_LISTS_UNORDERER_QUESTION' => 'HELP_BBCODE_LISTS_UNORDERER_ANSWER',
				'HELP_BBCODE_LISTS_ORDERER_QUESTION' => 'HELP_BBCODE_LISTS_ORDERER_ANSWER'
			]
		);

		$this->manager->add_block(
			'HELP_BBCODE_BLOCK_LINKS',
			true,
			[
				'HELP_BBCODE_LINKS_BASIC_QUESTION' => 'HELP_BBCODE_LINKS_BASIC_ANSWER'
			]
		);

		$this->manager->add_block(
			'HELP_BBCODE_BLOCK_IMAGES',
			false,
			[
				'HELP_BBCODE_IMAGES_BASIC_QUESTION' => 'HELP_BBCODE_IMAGES_BASIC_ANSWER',
				'HELP_BBCODE_IMAGES_ATTACHMENT_QUESTION' => 'HELP_BBCODE_IMAGES_ATTACHMENT_ANSWER'
			]
		);

		$this->manager->add_block(
			'HELP_BBCODE_BLOCK_OTHERS',
			false,
			[
				'HELP_BBCODE_OTHERS_CUSTOM_QUESTION' => 'HELP_BBCODE_OTHERS_CUSTOM_ANSWER'
			]
		);

		return $this->language->lang('BBCODE_GUIDE');
	}
}
