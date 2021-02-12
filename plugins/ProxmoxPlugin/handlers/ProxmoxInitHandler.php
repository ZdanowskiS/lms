<?php

class ProxmoxInitHandler {

	public function smartyInit(Smarty $hook_data) {

		$template_dirs = $hook_data->getTemplateDir();
		$plugin_templates = PLUGINS_DIR . DIRECTORY_SEPARATOR . ProxmoxPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'templates';
		array_unshift($template_dirs, $plugin_templates);
		$hook_data->setTemplateDir($template_dirs);

		$SMARTY = $hook_data;
		require_once(PLUGINS_DIR . DIRECTORY_SEPARATOR . ProxmoxPlugin::plugin_directory_name . DIRECTORY_SEPARATOR
			. 'lib' . DIRECTORY_SEPARATOR . 'definitions.php');

		return $hook_data;
	}

	public function modulesDirInit(array $hook_data = array()) {
		$plugin_modules = PLUGINS_DIR . DIRECTORY_SEPARATOR . ProxmoxPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'modules';
		array_unshift($hook_data, $plugin_modules);
		return $hook_data;
	}

	public function menuInit(array $hook_data = array()) {
		$menu_proxmox = array(
			'Proxmox' => array(
				'name' => 'Proxmox',
				'img' => 'radius.gif',
				'link' =>'?m=proxmoxnodelist',
				'accesskey' =>'p',
				'prio' => 11,
				'submenu' => array(
					array(
						'name' => trans('Node List'),
						'link' => '?m=proxmox_nodelist',
						'tip' => trans('List of Proxmox nodes'),
						'prio' => 10,
					),
					array(
						'name' => trans('New Proxmox Node'),
						'link' => '?m=proxmox_nodeadd',
						'tip' => trans('New Node'),
						'prio' => 20,
					),
					array(
						'name' => trans('Offer List'),
						'link' => '?m=proxmox_offerlist',
						'tip' => trans('List of Proxmox offert'),
						'prio' => 30,
					),
					array(
						'name' => trans('New Offer'),
						'link' => '?m=proxmox_offeradd',
						'tip' => trans('New Offer'),
						'prio' => 40,
					),
					array(
						'name' => trans('VM/CT List'),
						'link' => '?m=proxmox_vmctlist',
						'tip' => trans('VM/CT List'),
						'prio' => 50,
					),
				),
			),
		);

		$menu_keys = array_keys($hook_data);
		$i = array_search('netdevices', $menu_keys);
		array_splice($hook_data, $i + 1, 0, $menu_proxmox);

		return $hook_data;
	}

	public function accessTableInit() {
		$access = AccessRights::getInstance();

		$access->insertPermission(new Permission('Proxmox_full_access', trans('proxmox - full'), '^proxmox.*$'),
			AccessRights::FIRST_FORBIDDEN_PERMISSION);
	}
}
?>
