<?php

class ApiPlugin extends LMSPlugin {
	const plugin_directory_name = 'ApiPlugin';
	const PLUGIN_DBVERSION = '2019051100';
	const PLUGIN_NAME = 'API';
	const PLUGIN_DESCRIPTION = 'Pozwala połaczyć się innym aplikacją';
	const PLUGIN_AUTHOR = 'Sylwester Zdanowski';

	private static $api = null;

	public static function getApiInstance() {
		if (empty(self::$api))
			self::$api = new Api();
		return self::$api;
	}

	public function registerHandlers() {
		$this->handlers = array(
			'smarty_initialized' => array(
				'class' => 'ApiInitHandler',
				'method' => 'smartyInit'
			),
			'modules_dir_initialized' => array(
				'class' => 'ApiInitHandler',
				'method' => 'modulesDirInit'
			)
		);
	 }

}

?>
