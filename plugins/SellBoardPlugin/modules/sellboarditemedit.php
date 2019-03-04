<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=sellboarditemlist');
}
else
	$itemid = $_GET['id'];

if (!$BOARD->ItemExists($itemid)) {
	$SESSION->redirect('?m=sellboarditemlist');
}

$itemdata = $BOARD->GetItem($itemid);

$layout['pagetitle'] = trans('Item Edit: $a',$itemdata['name']);

if(isset($_POST['itemedit']))
{
	$itemedit=$_POST['itemedit'];

	if(is_array($_POST['category']))foreach($_POST['category'] as $key => $cat)
	{
		$itemedit['category'][$cat]['categoryid']=$cat;
	}

	if($itemedit['name']==''){
		$error['name']=trans('Name is required!');
	}

	if($itemedit['ammount']==''){
		$itemedit['ammount']=1;
		$error['ammount']=trans('Ammount is required! Default 1');
	}

    if(!$error){
		$BOARD->ItemUpdate($itemedit);
	}

	$itemdata = $itemedit;
}

$sellers=$BOARD->GetSellerNames();
$categorylist = $BOARD->GetCategoryNames();

$SMARTY->assign('error',$error);
$SMARTY->assign('categorylist', $categorylist);
$SMARTY->assign('sellers', $BOARD->GetSellerNames());
$SMARTY->assign('itemdata',$itemdata);

$SMARTY->display('sellboarditemedit.html');
?>
