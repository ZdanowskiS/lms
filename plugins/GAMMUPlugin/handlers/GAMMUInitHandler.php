<?php

class GAMMUInitHandler {
    /**
     * Sets plugin Smarty templates directory
     * 
     * @param Smarty $hook_data Hook data
     * @return \Smarty Hook data
     */
	public function smartyInit(Smarty $hook_data) {

		$template_dirs = $hook_data->getTemplateDir();
		$plugin_templates = PLUGINS_DIR . DIRECTORY_SEPARATOR . GAMMUPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'templates';
		array_unshift($template_dirs, $plugin_templates);
		$hook_data->setTemplateDir($template_dirs);

		$SMARTY = $hook_data;
		require_once(PLUGINS_DIR . DIRECTORY_SEPARATOR . GAMMUPlugin::plugin_directory_name . DIRECTORY_SEPARATOR
			. 'lib' . DIRECTORY_SEPARATOR . 'definitions.php');

		return $hook_data;
	}

    /**
     * Sets plugin Smarty modules directory
     * 
     * @param array $hook_data Hook data
     * @return array Hook data
     */
	public function modulesDirInit(array $hook_data = array()) {
		$plugin_modules = PLUGINS_DIR . DIRECTORY_SEPARATOR . GAMMUPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'modules';
		array_unshift($hook_data, $plugin_modules);
		return $hook_data;
	}

	public function menuInit(array $hook_data = array()) {
		$menu_gammu = array(
			'GAMMU' => array(
				'name' => 'GAMMU',
				'img' => 'sms.gif',
				'link' =>'?m=gammuinboxlist',
				'tip' => 'Gammu',
				'accesskey' =>'k',
				'prio' => 36,
				'submenu' => array(
					array(
						'name' => trans('Inbox List'),
						'link' => '?m=gammuinboxlist',
						'tip' => trans('List of Inbox'),
						'prio' => 10,
					),
					array(
						'name' => trans('Outbox List'),
						'link' => '?m=gammuoutboxlist',
						'tip' => trans('List of Outbox'),
						'prio' => 20,
					),
				),
			),
		);

		$menu_keys = array_keys($hook_data);
		$i = array_search('messages', $menu_keys);
		array_splice($hook_data, $i + 1, 0, $menu_gammu);

		return $hook_data;
	}

	public function accessTableInit() {
		$access = AccessRights::getInstance();

	}
}
?>
