<?php

class USTCustomerHandler {
	public function customerBeforeDisplay(array $hook_data) {
		global $LMS;

		$UST = UStatsPlugin::getUStatsInstance();
		$customerinfo =$hook_data['customerinfo'];

		$SMARTY = $hook_data['smarty'];
		if(ConfigHelper::checkConfig('ustats.customerinfo'))
		{
			$action=$UST->GetLastUserAction(UST_CUSTOMER_DISPLAY);

			if($action['action']!=UST_CUSTOMER_DISPLAY || $action['val']!=$customerinfo['id']){
				$UST->CustomerDisplayAdd(UST_CUSTOMER_DISPLAY, $customerinfo['id']);
			}
		}

		return $hook_data;
	}

}

?>
