<?php

class HDTemplatePlugin extends LMSPlugin {
	const plugin_directory_name = 'HDTemplatePlugin';
	const PLUGIN_DBVERSION = '2019031300';
	const PLUGIN_NAME = 'HDTemplate';
	const PLUGIN_DESCRIPTION = 'Help Desk Templates - allows to crete default replays';
	const PLUGIN_AUTHOR = 'Sylwester Zdanowski';

	private static $hdt = null;

	public static function getHDTemplateInstance() {
		if (empty(self::$hdt))
			self::$hdt = new HDT();
		return self::$hdt;
	}

	public function registerHandlers() {
		$this->handlers = array(
			'smarty_initialized' => array(
				'class' => 'HDTInitHandler',
				'method' => 'smartyInit'
			),
			'modules_dir_initialized' => array(
				'class' => 'HDTInitHandler',
				'method' => 'modulesDirInit'
			),
			'menu_initialized' => array(
				'class' => 'HDTInitHandler',
				'method' => 'menuInit'
			),
			'access_table_initialized' => array(
				'class' => 'HDTInitHandler',
				'method' => 'accessTableInit'
			),
			'rtmessageadd_before_display' => array(
				'class' => 'HDTRtessageHandler',
				'method' => 'rtmessageaddBeforeSubmit'
			),
		);
	 }

}
?>
