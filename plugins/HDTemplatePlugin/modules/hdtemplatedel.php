<?php

$id = intval($_GET['id']);

if ($id && $_GET['is_sure'] == '1') {
	$DB->Execute('DELETE FROM hdtemplates WHERE id = ?', array($id))
}

header('Location: ?m=hdtemplatelist');
?>
