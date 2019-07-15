<?php

class HDTRtessageHandler {
	public function rtmessageaddBeforeSubmit(array $hook_data) {
		global $LMS;

		$HDT = HDTemplatePlugin::getHDTemplateInstance();

		$SMARTY = $hook_data['smarty'];

		$SMARTY->assign('templates', $HDT->GetTemplateNames(HDT_TYPE_MESSAGE));

		return $hook_data;
	}
}

?>
