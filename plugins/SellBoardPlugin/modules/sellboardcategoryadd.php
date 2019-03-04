<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

$categoryadd = isset($_POST['categoryadd']) ? $_POST['categoryadd'] : NULL;

if($categoryadd)
{
	if($categoryadd['name']==''){
		$error['name']=trans('Name is required!');
	}

	if(!$error)
	{
		$id = $BOARD->CategoryAdd($categoryadd);

		if (!isset($categoryadd['reuse']))
			$SESSION->redirect('?m=sellboardcategoryinfo&id='.$id);
	}
}

$layout['pagetitle'] = trans('New Category');

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('error', $error);
$SMARTY->assign('hosts', $hosts);
$SMARTY->assign('categoryadd', $categoryadd);

$SMARTY->display('sellboardcategoryadd.html');

?>
