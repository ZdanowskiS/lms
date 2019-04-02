<?php
$GAMMU = GAMMUPlugin::getGAMMUInstance();

$layout['pagetitle'] = trans('GAMMU Outbox List');

if(!isset($_GET['o']))
	$SESSION->restore('gio', $o);
else
	$o = $_GET['o'];
$SESSION->save('gio', $o);

$total = $GAMMU->GetOutboxList($o, null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$outboxlist=$GAMMU->GetOutboxList($o, $per_page, $offset);

$listdata['order']=$outboxlist['order'];
$listdata['direction']=$outboxlist['direction'];
$listdata['dictc']=$dictc ? $dictc : '';
$listdata['dictstr']=$dictstr ? $dictstr : '';
$listdata['dicts']=$dicts ? $dicts : 0;

unset($outboxlist['total']);
unset($outboxlist['order']);
unset($outboxlist['direction']);

$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('outboxlist', $outboxlist);
$SMARTY->assign('listdata',$listdata);

$SMARTY->display('gammuoutboxlist.html');
?>
