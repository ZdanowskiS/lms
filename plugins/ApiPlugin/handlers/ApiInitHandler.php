<?php

class ApiInitHandler {
    /**
     * Sets plugin Smarty templates directory
     * 
     * @param Smarty $hook_data Hook data
     * @return \Smarty Hook data
     */
	public function smartyInit(Smarty $hook_data) {

		$template_dirs = $hook_data->getTemplateDir();

		$SMARTY = $hook_data;

		return $hook_data;
	}

    /**
     * Sets plugin Smarty modules directory
     * 
     * @param array $hook_data Hook data
     * @return array Hook data
     */
	public function modulesDirInit(array $hook_data = array()) {
		$plugin_modules = PLUGINS_DIR . DIRECTORY_SEPARATOR . ApiPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'modules';
		array_unshift($hook_data, $plugin_modules);
		return $hook_data;
	}

	public function menuInit(array $hook_data = array()) {

		return $hook_data;
	}

    /**
     * Modifies access table
     * 
     */

	public function accessTableInit() {
		$access = AccessRights::getInstance();

		$access->insertPermission(new Permission('Sell_board_full_access', trans('Sell board - module management'), '^sellboard.*$'),
			AccessRights::FIRST_FORBIDDEN_PERMISSION);
	}

}
?>
