<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

$hostlist = $DB->GetAll('SELECT id, name, url FROM sellboard_hosts WHERE access=1');

if($hostlist)foreach($hostlist as $host)
{
	$result=$BOARD->RestGetHostList($host);
	if($result)foreach($result as $item)
	{
		$url=$item['url'];
		if(!$DB->GetOne('SELECT 1 FROM sellboard_hosts WHERE url ?LIKE? ' . $DB->Escape("$url") . ')'))
		{
			$data = array (
				'name' => $item['name'],
				'url' => $item['url'],
				'access' => $item['access'],
				'share' => $item['share']
				);
			$BOARD->HostAdd($data);
		}
	}	
}

$SESSION->redirect('?m=sellboardhostlist');
?>
