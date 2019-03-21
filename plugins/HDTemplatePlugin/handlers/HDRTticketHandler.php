<?php

class HDTRTticketHandler {
	public function ticketaddBeforeSubmit(array $hook_data) {
		global $LMS;

		$HDT = HDTemplatePlugin::getHDTemplateInstance();

		$SMARTY = $hook_data['smarty'];

		$SMARTY->assign('templates', $HDT->GetTemplateNames(HDT_TYPE_TICKET));

		return $hook_data;
	}

}

?>
