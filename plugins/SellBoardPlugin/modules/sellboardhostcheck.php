<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

$hostlist = $DB->GetAll('SELECT id, name, url FROM sellboard_hosts');

if($hostlist)foreach($hostlist as $host)
{
	if($BOARD->RestGetCategory($host))
		$status=TRUE;
	else
		$status=FALSE;

	$BOARD->HostSetAccess($host['id'], $status);		
}

$SESSION->redirect('?m=sellboardhostlist');
?>
