<?php

class TaxxoInvoiceHandler {
	public function invoicedelBeforeDelete(array $hook_data) {
		global $LMS;

		$TAXXO = TaxxoPlugin::getTaxxoInstance();

		if($TAXXO->DocumentExists($hook_data['id']))
		{
			$TAXXO->DeleteApiDocumentById($hook_data['id']);

			$TAXXO->DocumentDelete($hook_data['id']);
		}
		return $hook_data;
	}

	public function balandedelBeforeDelete(array $hook_data) {
		global $LMS;

		$TAXXO = TaxxoPlugin::getTaxxoInstance();

		foreach($hook_data['ids'] as $key => $val)
		{
			if($TAXXO->DocumentExists($val))
			{
				$TAXXO->DeleteApiDocumentById($val);

				$TAXXO->DocumentDelete($val);
			}
		}

		return $hook_data;
	}
}

?>
