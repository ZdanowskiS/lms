<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

$layout['pagetitle'] = trans('Item List');

if (!isset($_GET['o']))
	$SESSION->restore('nlo', $o);
else
	$o = $_GET['o'];
$SESSION->save('nlo', $o);

if (!isset($_GET['boardc']))
	$SESSION->restore('boardc', $boardc);
else
	$boardc = $_GET['boardc'];
$SESSION->save('boardc', $boardc);

if (!isset($_GET['boardvfrom']))
	$SESSION->restore('boardvfrom', $boardvfrom);
else
	$boardvfrom = $_GET['boardvfrom'];
$SESSION->save('boardvfrom', $boardvfrom);

if (!isset($_GET['boardvto']))
	$SESSION->restore('boardvto', $boardvto);
else
	$boardfromv = $_GET['boardvto'];
$SESSION->save('boardvto', $boardvto);


$total = $BOARD->GetItemList($o, $boardc, $boardvfrom, $boardvto, null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$itemlist = $BOARD->GetItemList($o, $boardc, $boardfrom, $boardto, $per_page, $offset);

$listdata['order']=$itemlist['order'];
$listdata['direction']=$itemlist['direction'];
$listdata['boardvfrom']=$boardvfrom;
$listdata['boardvto']=$boardvto;
$listdata['boardc']=$boardc;

unset($itemlist['order']);
unset($itemlist['direction']);

$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('categorylist',$BOARD->GetCategoryNames());
$SMARTY->assign('itemlist',$itemlist);
$SMARTY->assign('listdata',$listdata);

$SMARTY->display('sellboarditemlist.html');
?>
