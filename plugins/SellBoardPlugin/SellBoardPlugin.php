<?php

class SellBoardPlugin extends LMSPlugin {
	const plugin_directory_name = 'SellBoardPlugin';
	const PLUGIN_DBVERSION = '2018071000';
	const PLUGIN_NAME = 'TAblica sprzedaży';
	const PLUGIN_DESCRIPTION = 'Pozwala łączyć sie z api innych LMS, sprawdzaczy ktoś chce coś sprzedać';
	const PLUGIN_AUTHOR = 'Sylwester Zdanowski';

	private static $board = null;

	public static function getSellBoardInstance() {
		if (empty(self::$board))
			self::$board = new SellBoard();
		return self::$board;
	}

	public function registerHandlers() {
		$this->handlers = array(
			'smarty_initialized' => array(
				'class' => 'SellBoardInitHandler',
				'method' => 'smartyInit'
			),
			'modules_dir_initialized' => array(
				'class' => 'SellBoardInitHandler',
				'method' => 'modulesDirInit'
			),
			'menu_initialized' => array(
				'class' => 'SellBoardInitHandler',
				'method' => 'menuInit'
			),
			'userpanel_smarty_initialized' => array(
				'class' => 'SellBoardPanelHandler',
				'method' => 'userpanelLmsInitialize'
			),
		/*	),
			'ajax_wizard_config' => array(
				'class' => 'WizardLmsZTEHandler',
				'method' => 'ajax_wizardConfig'
			),
			'access_table_initialized' => array(
				'class' => 'WizardInitHandler',
				'method' => 'accessTableInit'
			),
			'customerinfo_before_display' => array(
				'class' => 'WizardCustomerHandler',
				'method' => 'customerInfoBeforeDisplay'
			),*/
		);
	 }

}

?>
