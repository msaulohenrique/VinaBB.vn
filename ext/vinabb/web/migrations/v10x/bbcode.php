<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

/**
* ACP settings for phpBB Resource
*/
class bbcode extends migration
{
	/** @var array $existing_tags */
	protected $existing_tags;

	/** @var array $bbcode_data */
	protected $bbcode_data;

	/** @var array $update_data */
	protected $update_data;

	/** @var array $insert_data */
	protected $insert_data;

	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [['custom', [[&$this, 'add_bbcode_tags']]]];
	}

	/**
	* Add new BBCode tags for SCEditor
	*/
	public function add_bbcode_tags()
	{
		$this->update_or_insert();
		$this->update_bbcode_tags();
		$this->insert_bbcode_tags();
	}

	/**
	* Update existing BBCode tags with new data
	*/
	protected function update_bbcode_tags()
	{
		if (sizeof($this->update_data))
		{
			foreach ($this->update_data as $bbcode_id => $data)
			{
				$sql = 'UPDATE ' . BBCODES_TABLE . '
					SET ' . $this->db->sql_build_array('UPDATE', $data) . '
					WHERE bbcode_id = ' . (int) $bbcode_id;
				$this->db->sql_query($sql);
			}
		}
	}

	/**
	* Insert new BBCode tags
	*/
	protected function insert_bbcode_tags()
	{
		if (sizeof($this->insert_data))
		{
			$sql_ary = [];
			$bbcode_id = $this->get_max_bbcode_id() + 1;

			foreach ($this->insert_data as $tag_data)
			{
				$sql_ary[] = array_merge($tag_data, ['bbcode_id' => $bbcode_id]);
				$bbcode_id++;
			}

			$this->db->sql_multi_insert(BBCODES_TABLE, $sql_ary);
		}
	}

	/**
	* Get BBCode syntax and its replacement HTML code
	* Help line is not used in SCEditor, but let store it as a language key for later
	*/
	protected function get_bbcode_data()
	{
		$this->bbcode_data = [
			's'			=> ['bbcode_match' => '[s]{TEXT}[/s]', 'bbcode_tpl' => '<s>{TEXT}</s>', 'bbcode_helpline' => 'S'],
			'sup'		=> ['bbcode_match' => '[sup]{TEXT}[/sup]', 'bbcode_tpl' => '<sup>{TEXT}</sup>', 'bbcode_helpline' => 'SUP'],
			'sub'		=> ['bbcode_match' => '[sub]{TEXT}[/sub]', 'bbcode_tpl' => '<sub>{TEXT}</sub>', 'bbcode_helpline' => 'SUB'],
			'left'		=> ['bbcode_match' => '[left]{TEXT}[/left]', 'bbcode_tpl' => '<p class="text-left">{TEXT}</p>', 'bbcode_helpline' => 'LEFT'],
			'center'	=> ['bbcode_match' => '[center]{TEXT}[/center]', 'bbcode_tpl' => '<p class="text-center">{TEXT}</p>', 'bbcode_helpline' => 'CENTER'],
			'right'		=> ['bbcode_match' => '[right]{TEXT}[/right]', 'bbcode_tpl' => '<p class="text-right">{TEXT}</p>', 'bbcode_helpline' => 'RIGHT'],
			'justify'	=> ['bbcode_match' => '[justify]{TEXT}[/justify]', 'bbcode_tpl' => '<p class="text-justify">{TEXT}</p>', 'bbcode_helpline' => 'JUSTIFY'],
			'table'		=> ['bbcode_match' => '[table]{TEXT}[/table]', 'bbcode_tpl' => '<table class="table table-striped table-hover">{TEXT}</table>', 'bbcode_helpline' => 'TABLE'],
			'tr'		=> ['bbcode_match' => '[tr]{TEXT}[/tr]', 'bbcode_tpl' => '<tr>{TEXT}</tr>', 'bbcode_helpline' => 'TR'],
			'td'		=> ['bbcode_match' => '[td]{TEXT}[/td]', 'bbcode_tpl' => '<td>{TEXT}</td>', 'bbcode_helpline' => 'TD']
		];
	}

	/**
	* Generate match and replacement data for BBCode tags
	*/
	protected function build_bbcode_data()
	{
		// Load the class acp_bbcode
		include "{$this->phpbb_root_path}includes/acp/acp_bbcodes.{$this->php_ext}";

		$bbcode = new \acp_bbcodes();

		$this->get_bbcode_data();

		foreach ($this->bbcode_data as $tag_name => $tag_data)
		{
			$pass_data = $bbcode->build_regexp($tag_data['bbcode_match'], $tag_data['bbcode_tpl']);

			$this->bbcode_data[$tag_name] += [
				'bbcode_tag'			=> $pass_data['bbcode_tag'],
				'first_pass_match'		=> $pass_data['first_pass_match'],
				'first_pass_replace'	=> $pass_data['first_pass_replace'],
				'second_pass_match'		=> $pass_data['second_pass_match'],
				'second_pass_replace'	=> $pass_data['second_pass_replace']
			];
		}
	}

	/**
	* Update existing tags, then insert new ones
	*/
	protected function update_or_insert()
	{
		$this->get_existing_bbcode_tags();
		$this->build_bbcode_data();

		foreach ($this->bbcode_data as $tag_name => $tag_data)
		{
			if (isset($this->existing_tags[$tag_name]))
			{
				$this->update_data[$this->existing_tags[$tag_name]] = $tag_data;
			}
			else
			{
				$this->insert_data[] = $tag_data;
			}
		}
	}

	/**
	* Get existing BBCode tag names
	*/
	protected function get_existing_bbcode_tags()
	{
		$sql = 'SELECT bbcode_id, bbcode_tag
			FROM ' . BBCODES_TABLE;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->existing_tags[$row['bbcode_tag']] = $row['bbcode_id'];
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* Get the next BBCode ID for the SQL INSERT query
	*
	* @return int
	*/
	protected function get_max_bbcode_id()
	{
		$sql = 'SELECT MAX(bbcode_id) AS max_bbcode_id
			FROM ' . BBCODES_TABLE;
		$result = $this->db->sql_query($sql);
		$max_bbcode_id = (int) $this->db->sql_fetchfield('max_bbcode_id');
		$this->db->sql_freeresult($result);

		return max(NUM_CORE_BBCODES, $max_bbcode_id);
	}
}
