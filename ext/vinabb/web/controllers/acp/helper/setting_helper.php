<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp\helper;

use vinabb\web\includes\constants;

/**
* Helper for the settings_module
*/
class setting_helper
{
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\config\db_text $config_text */
	protected $config_text;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \vinabb\web\controllers\acp\helper\setting_tasks_interface $task_helper */
	protected $task_helper;

	/** @var array $data List of config items which has data changed, ready to write */
	protected $data;

	/** @var array $tasks List of methods need to be executed before saving config items */
	protected $tasks;

	/** @var array $errors List of errors to be triggered, neither data updated or tasks executed */
	protected $errors;

	/** @var array $config_text_data */
	protected $config_text_data;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface			$cache			Cache service
	* @param \phpbb\config\config										$config			Config object
	* @param \phpbb\config\db_text										$config_text	Config text object
	* @param \phpbb\language\language									$language		Language object
	* @param \phpbb\request\request										$request		Request object
	* @param \phpbb\template\template									$template		Template object
	* @param \phpbb\user												$user			User object
	* @param \vinabb\web\controllers\acp\helper\setting_tasks_interface	$task_helper	Task helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\config\db_text $config_text,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\vinabb\web\controllers\acp\helper\setting_tasks_interface $task_helper
	)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->config_text = $config_text;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->task_helper = $task_helper;

		$this->config_text_data = $this->cache->get_config_text();
	}

	/**
	* Helper to output setting items to template variables
	*
	* @param string $group_name Group name of settings
	*/
	protected function output_group_settings($group_name = 'main')
	{
		foreach ($this->{'list_' . $group_name . '_settings'}() as $group_name => $group_data)
		{
			// Group output
			$this->template->assign_block_vars('groups', [
				'LEGEND'	=> $this->language->lang($this->language->is_set($group_name) ? $group_name : 'SETTINGS')
			]);

			foreach ($group_data as $name => $data)
			{
				// Row output
				$this->template->assign_block_vars('groups.rows', [
					'FIELD'		=> $name,
					'TITLE'		=> (substr($name, -3) == '_' . constants::LANG_VIETNAMESE) ? $this->language->lang(strtoupper(substr($name, 0, -3))) . ' (' . $this->language->lang('VIETNAMESE') . ')' : $this->language->lang(strtoupper($name)),
					'EXPLAIN'	=> (isset($data['explain']) && $data['explain'] === true) ? ((substr($name, -3) == '_' . constants::LANG_VIETNAMESE) ? $this->language->lang(strtoupper(substr($name, 0, -3)) . '_EXPLAIN') : $this->language->lang(strtoupper($name) . '_EXPLAIN')) : '',
					'HTML'		=> $this->return_input_html($name, $data),
					'PREPEND'	=> (isset($data['prepend']) && $data['prepend'] != '') ? $data['prepend'] : '',
					'APPEND'	=> (isset($data['append']) && $data['append'] != '') ? $data['append'] : '',
					'EXTRA'		=> (isset($data['extra']) && $data['extra'] != '') ? $data['extra'] : ''
				]);
			}
		}
	}

	/**
	* Helper to get and check setting items
	*
	* @param string $group_name Group name of settings
	*/
	protected function check_group_settings($group_name = 'main')
	{
		foreach ($this->{'list_' . $group_name . '_settings'}() as $group_name => $group_data)
		{
			foreach ($group_data as $name => $data)
			{
				if ($data['type'] != 'tpl')
				{
					// Get form input
					${$name} = $this->request->variable($name, $data['default'], (substr($data['type'], -4) == '_uni'));

					// config or config_text?
					$key = (substr($data['type'], 0, 4) == 'text') ? 'config_text_data' : 'config';
					$check = true;

					if (isset($data['check']))
					{
						switch ($data['check'])
						{
							case 'empty':
								if (${$name} == '')
								{
									$this->errors[] = $this->language->lang('ERROR_' . strtoupper($name) . '_EMPTY');
									$check = false;
								}
							break;

							case 'regex':
								if (isset($data['check_data']) && $data['check_data'] != '' && !preg_match($data['check_data'], ${$name}))
								{
									$this->errors[] = $this->language->lang('ERROR_' . strtoupper($name) . '_REGEX');
									$check = false;
								}
							break;

							case 'method':
								if (isset($data['check_data']) && $data['check_data'] != '' && method_exists($this, $data['check_data']) && !$this->{$data['check_data']}(${$name}))
								{
									$this->errors[] = $this->language->lang('ERROR_' . strtoupper($data['check_data']));
									$check = false;
								}
							break;
						}
					}

					// Valid data, add to array if has data changed
					if ($check && ${$name} != $this->$key['vinabb_web_' . $name])
					{
						// This is not a real config item?
						if (!isset($data['unset']) || (isset($data['unset']) && $data['unset'] === false))
						{
							$this->data[$key]['vinabb_web_' . $name] = ${$name};
						}

						// This config item comes with a task?
						if (isset($data['task']) && $data['task'] != '')
						{
							$this->tasks[$data['task']] = ${$name};
						}
					}
				}
			}
		}
	}

	/**
	* Generate HTML from our defined data types for each config row
	*
	* Input types:
	*	tpl			Return a template variable as string {ABC}
	*	int			Integer number: <input type="number"
	*	url			URL <input type="url"
	*	email		Email address <input type="email"
	*	string		Text <input type="text"
	*	string_uni	Unicode text <input type="text"
	*	text		Block text <textarea (Stored in the table _config_text)
	*	text_uni	Unicode block text <textarea (Stored in the table _config_text)
	*	radio		Radio button
	*	select		Dropdown selection
	*
	* @param string $name	Config name, used for field name: <input name="..."
	* @param array $data	Config item data
	* @return string HTML code
	*/
	protected function return_input_html($name, $data)
	{
		$html = '';

		switch ($data['type'])
		{
			case 'tpl':
				$html = $data['default'];
			break;

			case 'int':
				$min_html = ' min="' . (isset($data['type_data']['min']) ? $data['type_data']['min'] : 0) . '"';
				$max_html = isset($data['type_data']['max']) ? ' max="' . $data['type_data']['max'] .'"' : '';
				$step_html = isset($data['type_data']['step']) ? ' step="' . $data['type_data']['step'] . '"' : '';
				$html = '<input class="text" type="number" name="' . $name . '" id="' . $name . '"' . $min_html . $max_html . $step_html . ' value="' . $this->config['vinabb_web_' . $name] . '">';
			break;

			case 'url':
			case 'email':
			case 'string':
			case 'string_uni':
				$type = str_replace(['string', 'string_uni'], 'text', $data['type']);
				$maxlength_html = ' maxlength="' . (isset($data['type_data']['max']) ? $data['type_data']['max'] : constants::MAX_CONFIG_NAME) . '"';
				$html = '<input class="text medium" type="' . $type . '" name="' . $name . '" id="' . $name . '"' . $maxlength_html . ' value="' . $this->config['vinabb_web_' . $name] . '">';
			break;

			case 'text':
			case 'text_uni':
				$rows_html = ' rows="' . (isset($data['type_data']['rows']) ? $data['type_data']['rows'] : 5) . '"';
				$maxlength_html = (isset($data['type_data']['max'])) ? ' maxlength=" ' . $data['type_data']['max'] .'"' : '';
				$html = '<textarea name="' . $name . '" id="' . $name . '"' . $rows_html . $maxlength_html . '>' . $this->config_text_data['vinabb_web_' . $name] . '</textarea>';
			break;

			case 'radio':
				$value = $this->config['vinabb_web_' . $name];

				// Radio with multiple options
				if (isset($data['type_data']) && sizeof($data['type_data']))
				{
					$id_html = ' id="' . $name . '"';

					foreach ($data['type_data'] as $radio_value => $label)
					{
						$checked_html = ($value == $radio_value) ? ' checked' : '';
						$html .= '<label><input type="radio" class="radio" name="' . $name . '"' . $id_html . ' value="' . $radio_value . '"' . $checked_html. '> ' . $label . '</label>';

						// Only assign id="" for the first item
						$id_html = '';
					}
				}
				// Normal radio with yes/no options
				else
				{
					$yes_checked_html = ($value) ? ' checked' : '';
					$no_checked_html = (!$value) ? ' checked' : '';
					$html .= '<label><input type="radio" class="radio" name="' . $name . '" id="' . $name . '" value="1"' . $yes_checked_html . '> ' . $this->language->lang('YES') . '</label>';
					$html .= '<label><input type="radio" class="radio" name="' . $name . '" value="0"' . $no_checked_html. '> ' . $this->language->lang('NO') . '</label>';
				}
			break;

			case 'select':
				$html = '<select name="' . $name . '" id="' . $name . '">' . $data['default'] . '</select>';
			break;
		}

		return $html;
	}

	/**
	* Helper to run taks before set_group_settings()
	*/
	protected function run_tasks()
	{
		if (sizeof($this->tasks))
		{
			foreach ($this->tasks as $method_name => $value)
			{
				if (method_exists($this->task_helper, $method_name))
				{
					$this->task_helper->$method_name($value);
				}
			}
		}
	}

	/**
	* Helper to save setting items
	*/
	protected function set_group_settings()
	{
		if (isset($this->data['config']))
		{
			foreach ($this->data['config'] as $config_name => $config_value)
			{
				$this->config->set($config_name, $config_value);
			}
		}

		if (isset($this->data['config_text_data']))
		{
			$this->config_text->set_array($this->data['config_text_data']);
			$this->cache->clear_config_text();
		}
	}
}
