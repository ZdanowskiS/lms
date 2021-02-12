<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$layout['pagetitle'] = trans('Proxmox').' - '.trans('Offer List');

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

if(!isset($_GET['o']))
	$SESSION->restore('mogo', $o);
else
	$o = $_GET['o'];
$SESSION->save('mogo', $o);

$total = $PROXMOX->GetOfferList($o, null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$offerlist = $PROXMOX->GetOfferList($o, $per_page, $offset);

$listdata['order'] = $offerlist['order'];
$listdata['direction'] = $offerlist['direction'];

$listdata['string'] = $s;

unset($offerlist['total']);
unset($offerlist['order']);
unset($offerlist['direction']);

$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('offerlist',$offerlist);
$SMARTY->assign('listdata',$listdata);

$SMARTY->display('proxmox_offerlist.html');
?>
