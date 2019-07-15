<?php

class USTInitHandler {
    /**
     * Sets plugin Smarty templates directory
     * 
     * @param Smarty $hook_data Hook data
     * @return \Smarty Hook data
     */
	public function smartyInit(Smarty $hook_data) {

		$template_dirs = $hook_data->getTemplateDir();
		$plugin_templates = PLUGINS_DIR . DIRECTORY_SEPARATOR . UStatsPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'templates';
		array_unshift($template_dirs, $plugin_templates);
		$hook_data->setTemplateDir($template_dirs);

		$SMARTY = $hook_data;
		require_once(PLUGINS_DIR . DIRECTORY_SEPARATOR . UStatsPlugin::plugin_directory_name . DIRECTORY_SEPARATOR
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
		$plugin_modules = PLUGINS_DIR . DIRECTORY_SEPARATOR . UStatsPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'modules';
		array_unshift($hook_data, $plugin_modules);
		return $hook_data;
	}

	public function menuInit(array $hook_data = array()) {

		$hook_data['admin']['submenu']= array_merge($hook_data['admin']['submenu'], array( 1 => array(
						'name' => trans('User Report'),
						'link' => '?m=ustprint',
						'tip' => trans('User Status'),
						'prio' => 100,
					))
					);

		return $hook_data;
	}

	public function accessTableInit() {
		$access = AccessRights::getInstance();

		$access->insertPermission(new Permission('ust_full_access', trans('User Stats'), '^ust.*$'),
			AccessRights::FIRST_FORBIDDEN_PERMISSION);
	}
}
?>
