<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

if (!isset($_GET['o']))
	$SESSION->restore('nlo', $o);
else
	$o = $_GET['o'];
$SESSION->save('nlo', $o);
#boardh
if (!isset($_GET['boardh']))
	$SESSION->restore('boardh', $boardh);
else
	$boardh = $_GET['boardh'];
$SESSION->save('boardh', $boardh);

if (!isset($_GET['boardc']))
	$SESSION->restore('boardc', $boardc);
else
	$boardc = $_GET['boardc'];
$SESSION->save('boardc', $boardc);

if (!isset($_GET['boardvfrom']))
	$SESSION->restore('boardvfrom', $boardvfrom);
else
	$boardvfrom = $_GET['boardvfrom'];
$SESSION->save('boardvfrom', $boardvfrom);

if (!isset($_GET['boardvto']))
	$SESSION->restore('boardvto', $boardvto);
else
	$boardvto = $_GET['boardvto'];
$SESSION->save('boardvto', $boardvto);

$hostlist = $DB->GetAll('SELECT id, name, url FROM sellboard_hosts WHERE access=1'
						.($boardh ? ' AND id='.$boardh : ''));

###########################################################
if(isset($_GET['ajax'])) 
{
	header('Content-type: text/plain');
	$mode = urldecode(trim($_GET['mode']));

	if($hostlist)foreach($hostlist as $host)
	{
		#echo "Current Response is {$i} \r\n";
		if($mode=='category')
		{
			$result=$BOARD->RestGetCategory($host);
			if(is_array($result)){
				$category[$host['id']]=$result;
			}
		}
		elseif($mode=='item')
		{
			$decoded=$BOARD->RestGetItemList($host, $boardc, $boardvfrom, $boardvto);

			if(is_array($decoded))foreach($decoded as $item)
			{
					echo '<TR >
		<TD><a id='.$host['id'].'_'.$item['id'].'" href="?m=sellboarditemread&amp;hostid='.$host['id'].'&amp;id='.$item['id'].'">'.
			$item['name'].'</TD>
						<TD>'.$item['description'].'</TD>
						<TD>'.$item['sellername'].'<BR>
							'.$item['email'].'</TD>
						<TD>'.date("Y/m/d H:i",$item['cdate']).'</TD>
						<TD>'.$item['ammount'].'</TD>
						<TD>'.$item['value'].'</TD>
						<TD class="text-right lms-ui-buttons nobr">
<button onclick="location.href=\'?m=sellboarditemread&amp;hostid='.$host['id'].'&amp;id='.$item['id'].'\'" type="button">
     info</button>
</TD></TR>';
			}
			
    		flush();
    		ob_flush();
		}
	}

	if($mode=='category')
	{
		foreach($category as $key => $value)
		{
			foreach($category[$key] as $val)
			{
				$categorylist[$val['name']]['id']=$val['id'];
				$categorylist[$val['name']]['name']=$val['name'];
			}
		}
		echo '<option value="0">'.trans('- all -').'</option>';
		if($categorylist)foreach($categorylist as $val)
		{
			echo '<option value="'.$val['name'].'"'
				.($val['name']==$boardc ? 'SELECTED' : '').'>'.$val['name'].'</option>';
		}
	}
	exit;
}
#########################################################


$itemlist=array();
#$categorylist=array();

#$SESSION->save('sellboard', $sellboard);

$layout['pagetitle'] = trans('Offer list');

$listdata['boardvfrom']=$boardvfrom;
$listdata['boardvto']=$boardvto;
$listdata['boardc']=$boardc;

#$total=count($itemlist);
#$page = (!isset($_GET['page']) ? 1 : intval($_GET['page']));
#$per_page = intval(ConfigHelper::getConfig('phpui.nodelist_pagelimit', $total));
#$offset = ($page - 1) * $per_page;

#$pagination = LMSPaginationFactory::getPagination($page, intval($total), $per_page, ConfigHelper::checkConfig('phpui.short_pagescroller'));

#unset($itemlist[0]);
#$SMARTY->assign('pagination',$pagination);
$SMARTY->assign('hostlist', $hostlist);

$SMARTY->assign('itemlist', $itemlist);
#$SMARTY->assign('categorylist', $categorylist);
$SMARTY->assign('listdata', $listdata);

$SMARTY->display('sellboardread.html');

?>
