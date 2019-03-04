<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

$layout['pagetitle'] = trans('Seller List');

if (!isset($_GET['o']))
	$SESSION->restore('nlo', $o);
else
	$o = $_GET['o'];
$SESSION->save('nlo', $o);

$total = $BOARD->GetSellerList($o, null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$sellerlist = $BOARD->GetSellerList($o, $dictp, $per_page, $offset);

$listdata['order']=$sellerlist['order'];
$listdata['direction']=$sellerlist['direction'];

unset($sellerlist['order']);
unset($sellerlist['direction']);

$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('sellerlist',$sellerlist);
$SMARTY->assign('listdata',$listdata);

$SMARTY->display('sellboardsellerlist.html');
?>
