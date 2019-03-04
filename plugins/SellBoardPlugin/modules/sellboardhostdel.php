<?php

$id = intval($_GET['id']);

if ($id && $_GET['is_sure'] == '1') {
	$DB->Execute('DELETE FROM sellboard_hosts WHERE id = ?', array($id));
}

header('Location: ?m=sellboardhostlist');
?>
