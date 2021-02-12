<?php

class ProxmoxPlugin extends LMSPlugin {
	const plugin_directory_name = 'ProxmoxPlugin';
	const PLUGIN_DBVERSION = '2021020700';
	const PLUGIN_NAME = 'Proxmox';
	const PLUGIN_DESCRIPTION = 'Proxmox API communication';
	const PLUGIN_AUTHOR = 'Sylwester Zdanowski';

	private static $proxmox = null;

	public static function getProxmoxInstance() {
		if (empty(self::$proxmox))
			self::$proxmox = new PROXMOX();
		return self::$proxmox;
	}

	public function registerHandlers() {
		$this->handlers = array(
			'smarty_initialized' => array(
				'class' => 'ProxmoxInitHandler',
				'method' => 'smartyInit'
			),
			'modules_dir_initialized' => array(
				'class' => 'ProxmoxInitHandler',
				'method' => 'modulesDirInit'
			),
			'menu_initialized' => array(
				'class' => 'ProxmoxInitHandler',
				'method' => 'menuInit'
			),
			'customerinfo_before_display' => array(
				'class' => 'ProxmoxCustomerHandler',
				'method' => 'customerInfoBeforeDisplay'
			),
			'access_table_initialized' => array(
				'class' => 'ProxmoxInitHandler',
				'method' => 'accessTableInit'
			),
			'networkrecord_after_get' => array(
				'class' => 'ProxmoxNetworkHandler',
				'method' => 'networkrecord_after_get'
			),
		);
	 }

}
?>
