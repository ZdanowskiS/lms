<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$layout['pagetitle'] = trans('Proxmox').' - '.trans('VM/CT List');

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

if(!isset($_GET['o']))
	$SESSION->restore('mogo', $o);
else
	$o = $_GET['o'];
$SESSION->save('mogo', $o);

$total = $PROXMOX->GetVMCTList($o, null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$vmctlist = $PROXMOX->GetVMCTList($o, $per_page, $offset);

$listdata['order'] = $vmctlist['order'];
$listdata['direction'] = $vmctlist['direction'];

$listdata['string'] = $s;

unset($vmctlist['total']);
unset($vmctlist['order']);
unset($vmctlist['direction']);

$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('vmctlist',$vmctlist);
$SMARTY->assign('listdata',$listdata);

$SMARTY->display('proxmox_vmctlist.html');
?>
