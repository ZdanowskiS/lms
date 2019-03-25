<?php

function sortlist($a,$b) { 
	global $direction,$sort;

	if(is_int($b[$sort]))
	{
		if($direction=='desc'){
			return ($b[$sort]-$a[$sort]);
		}
		else{
			return ($a[$sort]-$b[$sort]);
		}
	}
	else
	{
		if($direction=='desc'){
			return strcasecmp($b[$sort],$a[$sort]);
		}
		else{
			return strcasecmp($a[$sort],$b[$sort]);
		}
	}
}

$HDT = HDTemplatePlugin::getHDTemplateInstance();

$type = isset($_GET['type']) ? $_GET['type'] : '';

switch($type)
{
	case 'stats':
		$datefrom  = !empty($_GET['datefrom']) ? $_GET['datefrom'] : $_POST['datefrom'];
		$dateto  = !empty($_GET['dateto']) ? $_GET['dateto'] : $_POST['dateto'];
		$direction = !empty($_GET['direction']) ? $_GET['direction'] : $_POST['direction'];
		$sort  = !empty($_GET['sort']) ? $_GET['sort'] : $_POST['sort'];

 		($direction != 'desc') ? $direction = 'asc' : $direction = 'desc';

		if(!empty($datefrom)){
			$datefrom=date_to_timestamp($datefrom);
		}
		else
			$datefrom = 0;

		if(!empty($dateto)){
			$dateto=date_to_timestamp($dateto);
		}
		else
			$dateto = 0;

		$users=$DB->GetAll('SELECT id, lastname, firstname, position FROM users');

		foreach($users as $key => $user)
		{
			$stats[$user['id']]['name']=$user['lastname']." ".$user['firstname'];

			$stats[$user['id']]['position']=$user['position'];

			$stats[$user['id']]['messages']=$DB->GetOne('SELECT count(*) FROM rtmessages 
													WHERE userid=? '
													.($datefrom ? ' AND createtime>='.$datefrom : '' )
													.($dateto ? ' AND createtime<='.$dateto : '' ),
													array($user['id']));
			$sum['messages']+=$stats[$user['id']]['messages'];

			$stats[$user['id']]['tickets']=$DB->GetOne('SELECT count(*) FROM rttickets 
													WHERE creatorid=?'
													.($datefrom ? ' AND createtime>='.$datefrom : '' )
													.($dateto ? ' AND createtime<='.$dateto : '' ),
													array($user['id']));
			$sum['tickets']+=$stats[$user['id']]['tickets'];

			$stats[$user['id']]['ownerevents']=$DB->GetOne('SELECT count(*) FROM events 
													WHERE userid=?'
													.($datefrom ? ' AND creationdate>='.$datefrom : '' )
													.($dateto ? ' AND creationdate<='.$dateto : '' ),
													array($user['id']));
			$sum['ownerevents']+=$stats[$user['id']]['ownerevents'];

			$stats[$user['id']]['closedevents']=$DB->GetOne('SELECT count(*) FROM events 
													WHERE closeduserid=?'
													.($datefrom ? ' AND closeddate>='.$datefrom : '' )
													.($dateto ? ' AND closeddate<='.$dateto : '' ),
													array($user['id']));
			$sum['closedevents']+=$stats[$user['id']]['closedevents'];

			$stats[$user['id']]['closedassignedevents']=$DB->GetOne('SELECT count(*) 
													FROM events e 
													JOIN eventassignments ea ON (e.id=ea.eventid)
													WHERE closeduserid=?'
													.($datefrom ? ' AND closeddate>='.$datefrom : '' )
													.($dateto ? ' AND closeddate<='.$dateto : '' ),
													array($user['id']));
			$sum['closedassignedevents']+=$stats[$user['id']]['closedassignedevents'];

		}

		if($sort!=''){
			uasort($stats, 'sortlist');
		}
		$layout['pagetitle'] = trans('User Activity Report');

		$SMARTY->assign('sum', $sum);
		$SMARTY->assign('stats', $stats);
		$SMARTY->display('ustprintstat.html');
	break;
	case 'timestat':
		$datefrom  = !empty($_GET['datefrom']) ? $_GET['datefrom'] : $_POST['datefrom'];
		$dateto  = !empty($_GET['dateto']) ? $_GET['dateto'] : $_POST['dateto'];

		if($datefrom ) {
			list($year, $month, $day) = explode('/',$datefrom );
			$unixfrom = mktime(0,0,0,$month,$day,$year);
		} else {
			$from = date('Y/m/d',time());
			$unixfrom = mktime(0,0,0); //today
		}
		if($dateto) {
			list($year, $month, $day) = explode('/',$dateto);
			$unixto = mktime(23,59,59,$month,$day,$year);
		} else {
			$dateto = date('Y/m/d',time());
			list($year, $month, $day) = explode('/',$dateto);
			$unixto = mktime(23,59,59,$month,$day,$year);
		}

		#print $unixfrom."<".$unixto."<BR>";
		#print date('Y/m/d',$unixfrom)." ".date('Y/m/d',$unixto)."<BR>";
		$max=0;
		$data['ammount']=0;
		do{
			$d=date('Y/m/d',$unixfrom);
			if(!$stats[$d]){
				$i=0;
			}
			$i++;
			$data['ammount']++;
			$headers[$i]['from']=$unixfrom;
			$headers[$i]['to']=$unixfrom+3600;

			$stats[$d][$i]['messages']=$DB->GetOne('SELECT count(*) FROM rtmessages 
												WHERE createtime>=? AND createtime<?',
												array($headers[$i]['from'],$headers[$i]['to']));

			$stats[$d][$i]['tickets']=$DB->GetOne('SELECT count(*) FROM rttickets 
												WHERE createtime>=? AND createtime<?',
												array($headers[$i]['from'],$headers[$i]['to']));

			$stats[$d][$i]['events']=$DB->GetOne('SELECT count(*) FROM events 
												WHERE creationdate>=? AND creationdate<?',
												array($headers[$i]['from'],$headers[$i]['to']));

			$stats[$d][$i]['sum']=$stats[$d][$i]['messages']+$stats[$d][$i]['tickets']+$stats[$d][$i]['events'];

			$data['max']=($data['max']>$stats[$d][$i]['sum'] ? $data['max'] : $stats[$d][$i]['sum']);
			$unixfrom+=3600;

		}while($unixfrom<$unixto);

		$layout['pagetitle'] = trans('Activity In Time Report');

		$SMARTY->assign('headers', $headers);	
		$SMARTY->assign('stats', $stats);
		$SMARTY->assign('data', $data);
		$SMARTY->display('usprinttime.html');
		exit;
	default:

		$layout['pagetitle'] = trans('User Activity Reports');

		$SMARTY->display('ustprintindex.html');
	break;
}

?>
