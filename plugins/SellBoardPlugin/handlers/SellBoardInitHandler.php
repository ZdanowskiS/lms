<?php

class SellBoardInitHandler {
    /**
     * Sets plugin Smarty templates directory
     * 
     * @param Smarty $hook_data Hook data
     * @return \Smarty Hook data
     */
	public function smartyInit(Smarty $hook_data) {

		$template_dirs = $hook_data->getTemplateDir();
		$plugin_templates = PLUGINS_DIR . DIRECTORY_SEPARATOR . SellBoardPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'templates';
		array_unshift($template_dirs, $plugin_templates);
		$hook_data->setTemplateDir($template_dirs);

		$SMARTY = $hook_data;
		require_once(PLUGINS_DIR . DIRECTORY_SEPARATOR . SellBoardPlugin::plugin_directory_name . DIRECTORY_SEPARATOR
			. 'lib' . DIRECTORY_SEPARATOR . 'definitions.php');

		return $hook_data;
	}

    /**
     * Sets plugin Smarty modules directory
     * 
     * @param array $hook_data Hook data
     * @return array Hook data
     */
	public function modulesDirInit(array $hook_data = array()) {
		$plugin_modules = PLUGINS_DIR . DIRECTORY_SEPARATOR . SellBoardPlugin::plugin_directory_name . DIRECTORY_SEPARATOR . 'modules';
		array_unshift($hook_data, $plugin_modules);
		return $hook_data;
	}

	public function menuInit(array $hook_data = array()) {
		$menu = array(
			'Wizard' => array(
				'name' => trans('Sell Board'),
				'img' => 'promo.gif',
				'link' =>'?m=sellboarditemlist',
				'tip' => 'Sell Board',
				'accesskey' =>'w',
				'prio' => 26,
				'submenu' => array(
					array(
						'name' => trans('Seller List'),
						'link' => '?m=sellboardsellerlist',
						'tip' =>  trans('List of sellers'),
						'prio' => 10,
					),
					array(
						'name' => trans('New Seller'),
						'link' => '?m=sellboardselleradd',
						'tip' => trans('Add new category'),
						'prio' => 20,
					),
					array(
						'name' => trans('Category List'),
						'link' => '?m=sellboardcategorylist',
						'tip' =>  trans('List of subscription fees'),
						'prio' => 30,
					),
					array(
						'name' => trans('New Category'),
						'link' => '?m=sellboardcategoryadd',
						'tip' => trans('Add new category'),
						'prio' => 40,
					),
					array(
						'name' => trans('Item List'),
						'link' => '?m=sellboarditemlist',
						'tip' => '',
						'prio' => 50,
					),
					array(
						'name' => trans('New Item'),
						'link' => '?m=sellboarditemadd',
						'tip' => '',
						'prio' => 60,
					),
					array(
						'name' => trans('Host List'),
						'link' => '?m=sellboardhostlist',
						'tip' =>  trans('List of host'),
						'prio' => 70,
					),
					array(
						'name' => trans('New Host'),
						'link' => '?m=sellboardhostadd',
						'tip' => trans('Add new host'),
						'prio' => 80,
					),
					array(
						'name' => trans('Offer list'),
						'link' => '?m=sellboardread',
						'tip' =>  trans('List ofert'),
						'prio' => 70,
					),
				),
			),
		);
		$menu_keys = array_keys($hook_data);
		$i = array_search('finances', $menu_keys);
		array_splice($hook_data, $i + 1, 0, $menu);

		return $hook_data;
	}

    /**
     * Modifies access table
     * 
     */

	public function accessTableInit() {
		$access = AccessRights::getInstance();

		$access->insertPermission(new Permission('Sell_board_full_access', trans('Sell board - module management'), '^sellboard.*$'),
			AccessRights::FIRST_FORBIDDEN_PERMISSION);
	}

}
?>
