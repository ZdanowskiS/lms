<?php

class HDTRtessageHandler {
	public function rtmessageaddBeforeSubmit(array $hook_data) {
		global $LMS;

		$HDT = HDTemplatePlugin::getHDTemplateInstance();

		$SMARTY = $hook_data['smarty'];

		#require_once(PLUGINS_DIR . '/' . RadiusPlugin::plugin_directory_name . '/modules/radiusnodereload.inc.php');
		$SMARTY->assign('templates', $HDT->GetTemplateNames());

		return $hook_data;
	}

}

?>
