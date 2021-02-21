<?php

class LibreNMSInitializeHandler {
	public function lmsInitialize($hook_data) 
    {
        global $DB;

        if($_GET['librenmsapi'] || $_POST['librenmsapi'])
        {
            if($_GET['librenmsapi'])
                $data=$_GET;
            elseif($_POST['librenmsapi'])
                $data=$_POST;

            $LIBRENMS = LibreNMSPlugin::getLibreNMSInstance();

            $LIBRENMS->login();

            if ($LIBRENMS->AUTH->islogged) {

                if($data['hostname'])
                    $netdevid=$DB->GetOne('SELECT id FROM netdevices WHERE name ?LIKE? '.$DB->Escape($data['hostname']));
                if(!$netdevid && $data['ip'])
                    $netdevid=$DB->GetOne('SELECT netdev FROM nodes WHERE ipaddr=inet_aton(?) OR ipaddr_pub=inet_aton(?)',
                                            array($data['ip'],$data['ip']));

                $queueid=ConfigHelper::getConfig('librenms.queue', '1');
                $recover_str=ConfigHelper::getConfig('librenms.recover', 'recover');

                #needs to be changed
                if($netdevid && $data['name']){
                    $exists=$DB->GetOne('SELECT r.id FROM rttickets r 
                                        JOIN rtmessages m ON (m.ticketid=r.id)
                                        WHERE queueid=? AND r.state IN(0,1)'
                                        .($netdevid ? ' AND r.netdevid='.$netdevid : '') 
                                       .' AND m.body ?LIKE? '.$DB->Escape('%'.$data['name'].'%'),
                                    array($queueid));
                }
                else
                {
                    $exists=$DB->GetOne('SELECT id FROM rttickets 
                                            WHERE queueid=? AND state IN(0,1)'
                                        .($netdevid ? ' AND netdevid='.$netdevid : '') 
                                       .' AND subject ?LIKE? '.$DB->Escape($data['subject']),
                                    array($queueid));
                }

                if(!$exists && !strpos($data['subject'],$recover_str))
                {
                    if($data['category'])
                        $cat=$DB->GetOne('SELECT id FROM rtcategories WHERE name ?LIKE? '
                                                    .$DB->Escape($data['category']));
                    else
                        $cat=$DB->GetOne('SELECT id FROM rtcategories');

                    if($data['owner'])
                        $ownerid=$DB->GetOne('SELECT id FROM users WHERE login ?LIKE? '.$DB->Escape($data['owner'])
                                            .' OR lastname ?LIKE? '.$DB->Escape($data['owner']));
                    elseif($data['ownerid'])
                        $ownerid=intval($data['ownerid']);
                    else
                        $ownerid=Auth::GetCurrentUser();


                    $categories[$cat]=$cat;

                    $ticket=array('queue' =>$queueid,
                                    'categories' =>$categories,
                                    'requestor' => 'LibreNMS',
                                    'requestor_mail' => '',
                                    'requestor_phone' => '',
                                    'requestor_userid' => Auth::GetCurrentUser(),
                                    'subject' => $data['subject'],
                                    'owner' =>$ownerid,
                                    'cause' => 0,
                                    'userid' => Auth::GetCurrentUser(),
                                    'source' => 0,
                                    'priority' => ($data['priority'] ? $data['priority'] : 0 ),
                                    'netnodeid' =>0,
                                    'netdevid' =>$netdevid,
                                    'verifierid' =>0,
                                    'service' => -1,
                                    'type' =>3,
                                    'body' =>$data['msg']);

                    $exists = $LIBRENMS->LMS->TicketAdd($ticket, $files = null);

                    $headers['From'] = $mailfname . ' <' . $mailfrom . '>';
                    $headers['Reply-To'] = $headers['From'];
                    $headers['Subject'] =$data['subject'];

                    $params=array('queue' => $queueid,
                                'verifierid' => null,
                                'mail_headers' => $headers,
                                'mail_body' => $data['msg'],
                                'sms_body' => $data['subject'],
                                'contenttype' => 'text/html',
                                'attachments' => null
                    );

                    $LIBRENMS->LMS->NotifyUsers($params);

                }
                else
                {
                    $messageid = '<msg.' . $queueid . '.' . $exists . '.'  . time() . '@rtsystem.' . gethostname() . '>';
                    $msgid = $LIBRENMS->LMS->TicketMessageAdd(array(
                                    'ticketid' => $exists,
                                    'messageid' => $messageid,
                                    'body' => $data['msg'],
                                    'type' => 1,#RTMESSAGE_NOTE
                                    ), $files);
                }

                if(strpos($data['subject'],$recover_str) && $exists)
                {
                    $DB->Execute('UPDATE rttickets SET state=? WHERE id=?',
                                    array(2,$exists));#RT_RESOLVED

                }            
                exit();
            }
       }
    }
}

?>
