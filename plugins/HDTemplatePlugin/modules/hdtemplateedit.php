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

$templatedata = $HDT->GetTemplateInfo($id);

$layout['pagetitle'] = trans('Template Edit: $a',$templatedata['name']);

if(isset($_POST['templateedit']))
{
	$templateedit=$_POST['templateedit'];
	$name=$templateedit['name'];

	if($name==''){
		$error['name']=trans('Name is required!');
	}
	elseif($DB->GetOne('SELECT 1 FROM hdtemplates WHERE id!=? AND name ?LIKE? '.$DB->Escape("%$name%"),array($id))){
		$error['name']=trans('Template with that name exists!');
	}

    if(!$error){
		$HDT->UpdateTemplate($templateedit);

		$SESSION->redirect('?m=hdtemplateinfo&id='.$id);
	}

	$templatedata = $templateedit;
}

$SMARTY->assign('error',$error);
$SMARTY->assign('templatedata',$templatedata);

$SMARTY->display('hdtemplateedit.html');
?>
