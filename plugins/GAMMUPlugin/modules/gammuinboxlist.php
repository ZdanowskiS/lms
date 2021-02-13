<?php
$GAMMU = GAMMUPlugin::getGAMMUInstance();

$layout['pagetitle'] = trans('GAMMU').' - '. trans('Inbox List');

if(!isset($_GET['o']))
	$SESSION->restore('gio', $o);
else
	$o = $_GET['o'];
$SESSION->save('gio', $o);

if (isset($_GET['datefrom'])) {
    $datefrom = date_to_timestamp($_GET['datefrom']);
    if (empty($datefrom)) {
        $datefrom = time();
    }
} else {
    $SESSION->restore('gamdf', $datefrom);
}
$SESSION->save('gamdf', $datefrom);

if (isset($_POST['dateto'])) {
    $dateto = date_to_timestamp($_GET['dateto']);
    if (empty($dateto)) {
        $dateto = 0;
    }
} else {
    $SESSION->restore('gamdt', $dateto);
}
$SESSION->save('gamdt', $dateto);

$total = $GAMMU->GetInboxList($o, $datefrom, $dateto, null, null, true);

$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
$offset = ($page - 1) * $per_page;

$inboxlist=$GAMMU->GetInboxList($o, $datefrom, $dateto, $per_page, $offset);

$listdata['order']=$inboxlist['order'];
$listdata['direction']=$inboxlist['direction'];
$listdata['datefrom']=($datefrom ? date('Y/m/d',$datefrom) : '');
$listdata['dateto']=($dateto ? date('Y/m/d',$dateto) : '');
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
