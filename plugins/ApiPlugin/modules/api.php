<?php

$api=($_POST['api'] ? $_POST['api'] : $_GET['api'] );

if(!$api)
	return;

$action=($_POST['action'] ? $_POST['action'] : $_GET['action']);
$id=($_POST['id'] ? $_POST['id'] : $_GET['id']);
$data=($_POST['data'] ? $_POST['data'] : $_GET['data']);
$status=($_POST['status'] ? $_POST['status'] : $_GET['status']);

if($action=='getTicketListByOwner')
{
	if($status=='open')
		$status=RT_OPEN;
	elseif($status=='new')
		$status=RT_NEW;
	elseif($status=='resolved')
		$status=RT_RESOLVED;
	else
		$status='';

	$helpdesklist=$DB->GetAll('SELECT id, customerid, subject, createtime, state FROM rttickets WHERE owner=?'
									.($status ? ' AND state='.$status : '' ),
							array(Auth::GetCurrentUser()));

	echo json_encode(array_values($helpdesklist));
	exit();
}
elseif($action=='getTicketById')
{
	$result[0]=$LMS->GetTicketContents($id);

	echo json_encode(array_values($result));
	exit();
}
elseif($action=='AddTicketNote')
{
	$msgid = $LMS->TicketMessageAdd(array(
				'ticketid' => $id,
				'messageid' => $messageid,
				'body' => $data,
				'type' => RTMESSAGE_NOTE,
			), $files);

	echo 0;
	exit();
}
elseif($action=='getEventList')
{
	$result=$DB->GetAll('SELECT id, title, date, begintime, enddate, endtime FROM events WHERE 
								(EXISTS (SELECT 1 FROM eventassignments WHERE eventid = events.id 
												AND userid=?)
								OR userid=?)',
								array(Auth::GetCurrentUser(),
										Auth::GetCurrentUser()));

	echo json_encode(array_values($result));
	exit();
}
elseif($action=='getEventById')
{
	$result[0] = $LMS->GetEvent($id);

	echo json_encode(array_values($result));
	exit();
}
elseif($action=='CloseEvent')
{
	$DB->Execute('UPDATE events SET closed=1, closeddate=?NOW? WHERE id=?',array($id));
	echo 0;
	exit();
}
elseif($action=='UpdateEventNote')
{
	$DB->Execute('UPDATE events SET note=? WHERE id=?',array($data,$id));
	echo 0;
	exit();
}
elseif($action=='getCustomerById')
{
	$customer=$DB->GetRow('SELECT name, lastname, full_address FROM customeraddressview WHERE id=?',array($id));

	$result[0]['tariffs']=$LMS->GetCustomerAssignments($id);

	$result[0]['name']=$customer['name'];
	$result[0]['lastname']=$customer['lastname'];
	$result[0]['full_address']=$customer['full_address'];
	$result[0]['phones']=$DB->GetAll('SELECT name, contact FROM customercontacts WHERE customerid=? AND type IN (1,4)',array($id));

	echo json_encode(array_values($result));
	exit();
}
elseif($action=='getHostList')
{
	$hostlist=$DB->GetAll('SELECT id, name, lastreload FROM hosts');

	echo json_encode(array_values($hostlist));
	exit();
}		
elseif($action=='reloadHost')
{
	$DB->Execute('UPDATE hosts SET reload=1 WHERE id=?',array($id));
	exit();
}
?>
