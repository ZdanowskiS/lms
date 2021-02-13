<?php

$GAMMU = GAMMUPlugin::getGAMMUInstance();

$layout['pagetitle'] = trans('GAMMU').' - '.trans('Sent, SMS Info:');


if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=gammuinboxlist');
}
else
	$id = $_GET['id'];

#if (!$GAMMU->InboxExists($id)) {
#		$SESSION->redirect('?m=gammuinboxlist');
#}

$inboxinfo = $GAMMU->GetSent($id);

$SMARTY->assign('inboxinfo', $inboxinfo);

$SMARTY->display('gammusentboxinfo.html');
?>
