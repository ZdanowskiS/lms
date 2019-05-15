<?php

$TAXXO = TaxxoPlugin::getTaxxoInstance();

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=taxxodocumentlist');
}
else
	$id = $_GET['id'];

if (!$TAXXO->DocumentExists($id)) {
	$SESSION->redirect('?m=taxxodoxumentid');
}

$documentinfo = $TAXXO->GetDocument($id);

$contentinfo =$DB->GetRow('SELECT c.contractorid, c.totalnet, c.totaltax, c.totalgross,
									con.name as contractoname
								FROM taxxo_content c 
									JOIN taxxo_contractor con ON (c.contractorid=con.id)
							WHERE c.tdocid=?',array($id));

$contractoname=$DB->GetOne('SELECT name FROM taxxo_contractor WHERE id=?',
								array($contentinfo['contractorid']));
$TAXXO->ApiAuthenticate();

if($documentinfo['taxxoid']){
	$apiinfo = $TAXXO->GetApiDocumentById($documentinfo['taxxoid']);
}
else{
	$apiinfo = $TAXXO->GetApiDocumentBynumber($documentinfo['docid']);
}

$TAXXO->ApiLogout();

$layout['pagetitle'] = trans('TAXXO Document Info');

$SMARTY->assign('documentinfo', $documentinfo);
$SMARTY->assign('contentinfo', $contentinfo);
$SMARTY->assignByRef('apiinfo', $apiinfo);

$SMARTY->display('taxxodocumentinfo.html');

?>
