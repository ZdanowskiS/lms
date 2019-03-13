<?php

$HDT = HDTemplatePlugin::getHDTemplateInstance();

$templateadd = isset($_POST['templateadd']) ? $_POST['templateadd'] : NULL;

if($templateadd)
{
	if($templateadd['name'] == '')
		$error['name'] = trans('template name is required!');

	if(!$error)
	{
		$id = $HDT->AddTemplate($templateadd);

		if (!isset($templateadd['reuse'])){
			$SESSION->redirect('?m=hdtemplateinfo&id='.$id);
		}
	}

	$templatedata=$templateadd;
}

$layout['pagetitle'] = trans('New Template');
$SMARTY->assign('error', $error);
$SMARTY->assign('templatedata', $templatedata);
$SMARTY->display('hdtemplateadd.html');

?>
