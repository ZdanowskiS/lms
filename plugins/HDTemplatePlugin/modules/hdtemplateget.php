<?php

$HDT = HDTemplatePlugin::getHDTemplateInstance();

if(isset($_GET['ajax'])) 
{
	header('Content-type: text/plain');
	$mode = urldecode(trim($_GET['mode']));
	$id=intval($_GET['id']);

	$template=$HDT->GetTemplate($id,$_GET['customerid']);

	echo $template;
	exit();
}

?>
