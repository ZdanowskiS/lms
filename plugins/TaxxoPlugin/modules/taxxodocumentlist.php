<?php

$TAXXO = TaxxoPlugin::getTaxxoInstance();

$layout['pagetitle'] = trans('TAXXO Document List');

if (!isset($_GET['o']))
	$SESSION->restore('tdoco', $o);
else
	$o = $_GET['o'];
$SESSION->save('tdoco', $o);
///type
if (!isset($_GET['t']))
	$SESSION->restore('tdoct', $t);
else
	$t = $_GET['t'];
$SESSION->save('tdoct', $t);
////status
if (!isset($_GET['s']))
	$SESSION->restore('tdocs', $s);
else
	$s = $_GET['s'];
$SESSION->save('tdocs', $s);
////////////cat
if (!isset($_GET['cat']))
	$SESSION->restore('tdoccat', $cat);
else
	$cat = $_GET['cat'];
$SESSION->save('tdoccat', $cat);
////////////search
if(isset($_GET['search']))
	$se = $_GET['search'];
else
	$SESSION->restore('tdocse', $se);
if(!isset($se))
{
	$year=date("Y", time());
	$month=date("m", time());
	$se = $year.'/'.$month;
}
$SESSION->save('tdocse', $se);


if($c == 'cdate' && $s && preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}$/', $se)){
	list($year, $month, $day) = explode('/', $se);
	$se = mktime(0,0,0, $month, $day, $year);
}
elseif($c == 'month' && $s && preg_match('/^[0-9]{4}\/[0-9]{2}$/', $se)){
	list($year, $month) = explode('/', $s);
        $se = mktime(0,0,0, $month, 1, $year);
}
elseif($c== 'contractor' && $s){
	$se='contractor';
}

$total = $TAXXO->GetDocumentList($o, $s, $t, $cat, $se,  null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$documentlist = $TAXXO->GetDocumentList($o, $s, $t, $cat, $se, $per_page, $offset);

$listdata['order']=$documentlist['order'];
$listdata['direction']=$documentlist['direction'];

$listdata['tds']=$s;
$listdata['tdt']=$t;
$listdata['cat']=$cat;
$listdata['search']=$se;

unset($documentlist['total']);
unset($documentlist['order']);
unset($documentlist['direction']);

$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('documentlist',$documentlist);
$SMARTY->assign('listdata',$listdata);

$SMARTY->display('taxxodocumentlist.html');
?>
