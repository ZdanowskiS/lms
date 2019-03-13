<?php

$HDT = HDTemplatePlugin::getHDTemplateInstance();

if(isset($_GET['ajax'])) 
{
	header('Content-type: text/plain');
	$mode = urldecode(trim($_GET['mode']));
	$id=$_GET['id'];

	$template=$HDT->GetTemplate($id,$_GET['customerid']);

	print $template;
	exit();
}

?>
