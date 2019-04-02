<?php

class GAMMUPlugin extends LMSPlugin {
	const plugin_directory_name = 'GAMMUPlugin';
	const PLUGIN_DBVERSION = '2019032200';
	const PLUGIN_NAME = 'GAMMU';
	const PLUGIN_DESCRIPTION = 'GAMU (BETA) - bezpośredni odczyt bazy danych gammu';
	const PLUGIN_AUTHOR = 'Sylwester Zdanowski';

	private static $gammu = null;

	public static function getGAMMUInstance() {
		if (empty(self::$gammu))
			self::$gammu = new GAMMU();
		return self::$gammu;
	}

	public function registerHandlers() {
		$this->handlers = array(
			'smarty_initialized' => array(
				'class' => 'GAMMUInitHandler',
				'method' => 'smartyInit'
			),
			'modules_dir_initialized' => array(
				'class' => 'GAMMUInitHandler',
				'method' => 'modulesDirInit'
			),
			'menu_initialized' => array(
				'class' => 'GAMMUInitHandler',
				'method' => 'menuInit'
			),
			'access_table_initialized' => array(
				'class' => 'GAMMUInitHandler',
				'method' => 'accessTableInit'
			),
		);
	 }

}
?>
