<?php
$GAMMU = GAMMUPlugin::getGAMMUInstance();

$layout['pagetitle'] = trans('GAMMU Inbox List');

if(!isset($_GET['o']))
	$SESSION->restore('gio', $o);
else
	$o = $_GET['o'];
$SESSION->save('gio', $o);

$total = $GAMMU->GetInboxList($o, null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$inboxlist=$GAMMU->GetInboxList($o, $per_page, $offset);

$listdata['order']=$inboxlist['order'];
$listdata['direction']=$inboxlist['direction'];
$listdata['dictc']=$dictc ? $dictc : '';
$listdata['dictstr']=$dictstr ? $dictstr : '';
$listdata['dicts']=$dicts ? $dicts : 0;

unset($inboxlist['total']);
unset($inboxlist['order']);
unset($inboxlist['direction']);

$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('inboxlist', $inboxlist);
$SMARTY->assign('listdata',$listdata);

$SMARTY->display('gammuinboxlist.html');
?>
