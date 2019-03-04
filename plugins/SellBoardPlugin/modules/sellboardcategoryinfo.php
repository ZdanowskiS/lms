<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=sellboardcategorylist');
}
else
	$sellerid = $_GET['id'];

if (!$BOARD->CategoryExists($sellerid)) {
	$SESSION->redirect('?m=sellboardcategorylist');
}

$categoryinfo = $BOARD->GetCategory($sellerid);

$layout['pagetitle'] = trans('Category Info: $a',$categoryinfo['name']);

$SMARTY->assign('categoryinfo', $categoryinfo);

$SMARTY->display('sellboardcategoryinfo.html');
?>
