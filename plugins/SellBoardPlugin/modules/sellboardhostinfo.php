<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=sellboardsellerlist');
}
else
	$hostid = $_GET['id'];

if (!$BOARD->HostExists($hostid)) {
	$SESSION->redirect('?m=sellboardhostlist');
}

$hostinfo = $BOARD->GetHost($hostid);

$layout['pagetitle'] = trans('Host Info: $a',$hostinfo['name']);

$SMARTY->assign('hostinfo', $hostinfo);

$SMARTY->display('sellboardhostinfo.html');
?>
