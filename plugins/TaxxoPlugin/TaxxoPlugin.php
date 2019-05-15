<?php

class TaxxoPlugin extends LMSPlugin {
	const plugin_directory_name = 'TaxxoPlugin';
	const PLUGIN_DBVERSION = '2019032900';
	const PLUGIN_NAME = 'Taxxo';
	const PLUGIN_DESCRIPTION = 'Taxxo - API support for sending documents';
	const PLUGIN_AUTHOR = 'Sylwester Zdanowski';

	private static $taxxo = null;

	public static function getTaxxoInstance() {
		if (empty(self::$taxxo))
			self::$taxxo = new LMSTaxxo();
		return self::$taxxo;
	}

	public function registerHandlers() {
		$this->handlers = array(
			'smarty_initialized' => array(
				'class' => 'TaxxoInitHandler',
				'method' => 'smartyInit'
			),
			'modules_dir_initialized' => array(
				'class' => 'TaxxoInitHandler',
				'method' => 'modulesDirInit'
			),
			'menu_initialized' => array(
				'class' => 'TaxxoInitHandler',
				'method' => 'menuInit'
			),
			'access_table_initialized' => array(
				'class' => 'TaxxoInitHandler',
				'method' => 'accessTableInit'
			),
			'invoicedel_before_delete' => array(
				'class' => 'TaxxoInvoiceHandler',
				'method' => 'invoicedelBeforeDelete'
			),
			'balancedel_before_delete' => array(
				'class' => 'TaxxoInvoiceHandler',
				'method' => 'balandedelBeforeDelete'
			),
		);
	 }

}
?>
