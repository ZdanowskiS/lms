<?php

class UStatsPlugin extends LMSPlugin {
	const plugin_directory_name = 'UStatsPlugin';
	const PLUGIN_DBVERSION = '2019032200';
	const PLUGIN_NAME = 'UStats';
	const PLUGIN_DESCRIPTION = 'User Stats - user work summary';
	const PLUGIN_AUTHOR = 'Sylwester Zdanowski';

	private static $ust = null;

	public static function getUStatsInstance() {
		if (empty(self::$ust))
			self::$ust = new UST();
		return self::$ust;
	}

	public function registerHandlers() {
		$this->handlers = array(
			'smarty_initialized' => array(
				'class' => 'USTInitHandler',
				'method' => 'smartyInit'
			),
			'modules_dir_initialized' => array(
				'class' => 'USTInitHandler',
				'method' => 'modulesDirInit'
			),
			'menu_initialized' => array(
				'class' => 'USTInitHandler',
				'method' => 'menuInit'
			),
			'access_table_initialized' => array(
				'class' => 'USTInitHandler',
				'method' => 'accessTableInit'
			),
			'customerinfo_before_display' => array(
				'class' => 'USTCustomerHandler',
				'method' => 'customerBeforeDisplay'
			),
		);
	 }

}
?>
