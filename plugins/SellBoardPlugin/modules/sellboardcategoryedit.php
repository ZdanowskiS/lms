<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=sellboardcategorylist');
}
else
	$categoryid = $_GET['id'];

if (!$BOARD->CategoryExists($categoryid)) {
	$SESSION->redirect('?m=sellboardcategorylist');
}

$categorydata = $BOARD->GetCategory($categoryid);

$layout['pagetitle'] = trans('Category Edit: $a',$sellerdata['name']);

if(isset($_POST['categoryedit']))
{
	$categoryedit=$_POST['categoryedit'];

	if($categoryedit['name']==''){
		$error['name']=trans('Name is required!');
	}

    if(!$error){
		$BOARD->CategoryUpdate($categoryedit);
	}

	$categorydata = $categoryedit;
}

$SMARTY->assign('error',$error);
$SMARTY->assign('categorydata',$categorydata);

$SMARTY->display('sellboardcategoryedit.html');
?>
