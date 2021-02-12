<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$layout['pagetitle'] = trans('Proxmox').' - '.trans('<!pr>Node List');

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

if(!isset($_GET['o']))
	$SESSION->restore('mogo', $o);
else
	$o = $_GET['o'];
$SESSION->save('mogo', $o);

$total = $PROXMOX->GetNodeList($o, null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$nodelist = $PROXMOX->GetNodeList($o, $per_page, $offset);

$listdata['order'] = $nodelist['order'];
$listdata['direction'] = $nodelist['direction'];

$listdata['string'] = $s;

unset($nodelist['total']);
unset($nodelist['order']);
unset($nodelist['direction']);

$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('nodelist',$nodelist);
$SMARTY->assign('listdata',$listdata);

$SMARTY->display('proxmox_nodelist.html');
?>
