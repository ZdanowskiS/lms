<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

$hostid=intval($_GET['hostid']);
$itemid=intval($_GET['id']);

$host=$BOARD->GetHost($hostid);

$iteminfo = $BOARD->RestGetItem($host, $itemid);

$layout['pagetitle'] = trans('Offer Information: $a', $iteminfo['name']);

$SMARTY->assign('iteminfo', $iteminfo);

$SMARTY->display('sellboarditemread.html');
?>
