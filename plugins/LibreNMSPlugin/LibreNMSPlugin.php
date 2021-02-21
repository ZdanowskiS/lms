<?php

class LibreNMSPlugin extends LMSPlugin {
	const plugin_directory_name = 'LibreNMSPlugin';
	const PLUGIN_DBVERSION = '2021021500';
	const PLUGIN_NAME = 'LibreNMS';
	const PLUGIN_DESCRIPTION = 'LibreNMS API communication';
	const PLUGIN_AUTHOR = 'Sylwester Zdanowski';

	private static $librenms = null;

	public static function getLibreNMSInstance() {
		if (empty(self::$librenms))
			self::$librenms = new LibreNMS();
		return self::$librenms;
	}

	public function registerHandlers() {
		$this->handlers = array(
			'lms_initialized' => array(
				'class' => 'LibreNMSInitializeHandler',
				'method' => 'lmsInitialize'
			),
			'smarty_initialized' => array(
				'class' => 'LibreNMSInitHandler',
				'method' => 'smartyInit'
			),
			'modules_dir_initialized' => array(
				'class' => 'LibreNMSInitHandler',
				'method' => 'modulesDirInit'
			),
		);
	 }

}
?>
