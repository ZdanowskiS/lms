<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

$layout['pagetitle'] = trans('Category List');

if (!isset($_GET['o']))
	$SESSION->restore('nlo', $o);
else
	$o = $_GET['o'];
$SESSION->save('nlo', $o);

$total = $BOARD->GetCategoryList($o, null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$itemlist = $BOARD->GetCategoryList($o, $dictp, $per_page, $offset);

$listdata['order']=$itemlist['order'];
$listdata['direction']=$itemlist['direction'];

#unset($sellerlist['total']);
unset($itemlist['order']);
unset($itemlist['direction']);

$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('itemlist',$itemlist);
$SMARTY->assign('listdata',$listdata);

$SMARTY->display('sellboardcategorylist.html');
?>
