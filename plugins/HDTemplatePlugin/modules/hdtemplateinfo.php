<?php

$HDT = HDTemplatePlugin::getHDTemplateInstance();

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=hdtemplatelist');
}
else
	$id = $_GET['id'];

if (!$HDT->TemplateExists($id)) {
	$SESSION->redirect('?m=hdtemplatelist');
}

$templateinfo = $HDT->GetTemplateInfo($id);

$layout['pagetitle'] = trans('Template Info: $a',$templateinfo['name']);

$SMARTY->assign('templateinfo', $templateinfo);

$SMARTY->display('hdtemplateinfo.html');
?>
