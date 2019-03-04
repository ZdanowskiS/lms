<?php

$id = intval($_GET['id']);

if ($id && $_GET['is_sure'] == '1') {

	if(!$DB->GetOne('SELECT 1 FROM sellboard_items WHERE id = ? 
		LIMIT 1', array($id)))
	{
		$DB->Execute('DELETE FROM sellboard_sellers WHERE id = ?', array($id));
	}
}

header('Location: ?m=sellboardsellerlist');
?>
