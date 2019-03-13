<?php

$HDT = HDTemplatePlugin::getHDTemplateInstance();

$layout['pagetitle'] = trans('Template List');

if (!isset($_GET['o']))
	$SESSION->restore('nlo', $o);
else
	$o = $_GET['o'];
$SESSION->save('nlo', $o);

$total = $HDT->GetTemplateList($o, null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$templatelist = $HDT->GetTemplateList($o, $per_page, $offset);

$listdata['order']=$templatelist['order'];
$listdata['direction']=$templatelist['direction'];

unset($templatelist['order']);
unset($templatelist['direction']);

$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('templatelist',$templatelist);
$SMARTY->assign('listdata',$listdata);

$SMARTY->display('hdtemplatelist.html');
?>
