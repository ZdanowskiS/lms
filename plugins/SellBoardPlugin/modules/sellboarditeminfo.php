<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=sellboarditemlist');
}
else
	$itemid = $_GET['id'];

if (!$BOARD->ItemExists($itemid)) {
	$SESSION->redirect('?m=sellboarditemlist');
}

$iteminfo = $BOARD->GetItem($itemid);

$layout['pagetitle'] = trans('Item Info: $a',$iteminfo['name']);

$SMARTY->assign('iteminfo', $iteminfo);

$SMARTY->display('sellboarditeminfo.html');
?>
