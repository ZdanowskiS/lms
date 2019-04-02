<?php

$GAMMU = GAMMUPlugin::getGAMMUInstance();

$layout['pagetitle'] = trans('GAMMU Outbox SMS Info:');

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=gammuoutboxlist');
}
else
	$id = $_GET['id'];

if (!$GAMMU->OutboxExists($id)) {
		$SESSION->redirect('?m=gammuoutboxlist');
}

$outboxinfo = $GAMMU->GetOutbox($id);

$SMARTY->assign('outboxinfo', $outboxinfo);

$SMARTY->display('gammuoutboxinfo.html');
?>
