<?php

class LibreNMSInitHandler {

	public function smartyInit(Smarty $hook_data) {

		$template_dirs = $hook_data->getTemplateDir();
		$plugin_templates = PLUGINS_DIR . DIRECTORY_SEPARATOR . LibreNMSPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'templates';
		array_unshift($template_dirs, $plugin_templates);
		$hook_data->setTemplateDir($template_dirs);

		$SMARTY = $hook_data;
		#require_once(PLUGINS_DIR . DIRECTORY_SEPARATOR . LibreNMSPlugin::plugin_directory_name . DIRECTORY_SEPARATOR
		#	. 'lib' . DIRECTORY_SEPARATOR . 'definitions.php');

		return $hook_data;
	}

	public function modulesDirInit(array $hook_data = array()) {
		$plugin_modules = PLUGINS_DIR . DIRECTORY_SEPARATOR . LibreNMSPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'modules';
		array_unshift($hook_data, $plugin_modules);
		return $hook_data;
	}
}

?>
