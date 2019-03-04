<?php

class SellBoardPanelHandler {
	public function userpanelLmsInitialize($SMARTY) {
		global $LMS,$SESSION,$DB;

		require_once(PLUGINS_DIR . DIRECTORY_SEPARATOR . SellBoardPlugin::plugin_directory_name . DIRECTORY_SEPARATOR
			. 'lib' . DIRECTORY_SEPARATOR . 'definitions.php');

		$BOARD = SellBoardPlugin::getSellBoardInstance();

		if($_POST['sellboard']==1)
		{
			if(ConfigHelper::getConfig('sellboard.allowip'))
			{
				$block=1;
				if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				elseif (isset($_SERVER['HTTP_CLIENT_IP']))
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				else
					$ip = $_SERVER['REMOTE_ADDR'];

				$allowedlist = explode(',', ConfigHelper::getConfig('sellboard.allowip'));

				foreach ($allowedlist as $value){
					if($value==$ip)
						$block=0;
				}
			}
			if(ConfigHelper::getConfig('sellboard.blockip'))
			{
				if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				elseif (isset($_SERVER['HTTP_CLIENT_IP']))
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				else
					$ip = $_SERVER['REMOTE_ADDR'];

				$allowedlist = explode(',', ConfigHelper::getConfig('sellboard.blockip'));

				foreach ($allowedlist as $value){
					if($value==$ip)
						$block=1;
				}
			}
			if($block==1)
				exit;

			if($_POST['data']==BDATA_ITEMLIST)
			{
				if($_POST['category']){
					$category=$_POST['category'];
					$category=preg_replace("/[^A-Za-z0-9 ]/", '', $category);
					$categoryid=$DB->GetOne('SELECT id FROM sellboard_category 
												WHERE name ?LIKE? UPPER(' . $DB->Escape("$category") . ')');
				}
				$valuefrom=str_replace(',', '.', $_POST['valuefrom']);
				if (!preg_match('/^[0-9]+(\.[0-9]+)*$/', $valuefrom)) {
					$valuefrom=0;
				}

				$valueto=str_replace(',', '.', $_POST['valueto']);
				if (!preg_match('/^[0-9]+(\.[0-9]+)*$/', $valueto)) {
					$valueto=0;
				}

				$result=$DB->GetAll('SELECT si.id,si.name, si.description, si.value, si.ammount, si.cdate, 
									 ss.name as sellername, ss.phone, ss.email  
										FROM sellboard_items si 
										JOIN sellboard_sellers ss ON (ss.id=si.sellerid) 
									WHERE 1=1 '
									.($categoryid ? ' AND (SELECT 1 FROM sellboard_itemcategories sic 
													WHERE sic.itemid=si.id AND sic.categoryid='.$categoryid.')=1' : '')
									.($valuefrom ? ' AND si.value>='.$valuefrom : '')
									.($valueto ? ' AND si.value<='.$valueto : ''));
				print json_encode($result);
				exit;
			}
			elseif($_POST['data']==BDATA_CATEGORYLIST)
			{
				$result=$DB->GetAll('SELECT id, name FROM sellboard_category');
				print json_encode($result);
				exit;
			}
			elseif($_POST['data']==BDATA_ITEM)
			{
				$id=intval($_POST['itemid']);
				$result=$BOARD->GetItem($id);
				print json_encode($result);
				exit;
			}
			elseif($_POST['data']==BDATA_HOSTLIST)
			{
				$result=$DB->GetAll('SELECT id, name, url, access, share FROM sellboard_hosts 
									WHERE access=1 AND share=1');
				print json_encode($result);
				exit;
			}
		}

		return $hook_data;
	}
}
?>
