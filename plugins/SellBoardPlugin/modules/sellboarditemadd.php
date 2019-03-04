<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

$itemadd = isset($_POST['itemadd']) ? $_POST['itemadd'] : NULL;

if($itemadd)
{
	if($itemadd['name']==''){
		$error['name']=trans('Name is required!');
	}

	if($itemadd['ammount']==''){
		$itemadd['ammount']=1;
		$error['ammount']=trans('Ammount is required! Default 1');
	}

	$itemadd['value'] = str_replace(',','.', $itemadd['value']);

	if(!$error)
	{
		$id = $BOARD->ItemAdd($itemadd);

		if (!isset($itemadd['reuse']))
			$SESSION->redirect('?m=sellboarditeminfo&id='.$id);
	}
}

$layout['pagetitle'] = trans('New Item');

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('error', $error);
$SMARTY->assign('sellers', $BOARD->GetSellerNames());
$SMARTY->assign('itemadd', $itemadd);

$SMARTY->display('sellboarditemadd.html');

?>
