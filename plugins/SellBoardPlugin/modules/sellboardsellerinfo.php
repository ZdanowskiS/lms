<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=sellboardsellerlist');
}
else
	$sellerid = $_GET['id'];

if (!$BOARD->SellerExists($sellerid)) {
	$SESSION->redirect('?m=sellboardsellerlist');
}

$sellerinfo = $BOARD->GetSeller($sellerid);

$layout['pagetitle'] = trans('Seller Info: $a',$sellerinfo['name']);

$SMARTY->assign('sellerinfo', $sellerinfo);

$SMARTY->display('sellboardsellerinfo.html');
?>
