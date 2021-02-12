<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$layout['pagetitle'] = trans('Proxmox').' - '.trans('<!pr>New node');

$nodeadd = isset($_POST['nodeadd']) ? $_POST['nodeadd'] : NULL;

if($nodeadd)
{
	if(!$nodeadd['name'])
		$error['name']= trans('Name is required!');
    elseif($PROXMOX->NodeNameExists($nodeadd['name']))
    {
		$error['name']= trans('Node exists!');
    }

	if(!$nodeadd['ipaddr'])
		$error['ipaddr']= trans('IP address is required!');

	if(!$nodeadd['realm'])
		$error['realm']= trans('Realm is required!');

	if(!$nodeadd['login'])
		$error['login']= trans('Login is required!');

	if(!$nodeadd['password'])
		$error['password']= trans('Password is required!');

    $PROXMOX->connection->setlogin($nodeadd['name'],$nodeadd['ipaddr'], $nodeadd['login'], $nodeadd['realm'], $nodeadd['password']);
    $login=$PROXMOX->connection->login();

    if(!$login)
    {
        $error['name']= trans('Can\'t login!');
    }
	if(!isset($error))
	{
	    $id=$PROXMOX->NodeAdd($nodeadd);

        $network=$PROXMOX->connection->getNodeNetwork($nodeadd['name']);

        if($network)foreach($network['data'] as $int)
        {
            if(!is_null($int['bridge_fd']))
            {
                $data=array('nodeid' => $id,
                            'name' => $int['iface']);
                $PROXMOX->BridgeAdd($data);
            }
        }

        $storage=$PROXMOX->connection->getNodeStorage($nodeadd['name']);

        if($storage)foreach($storage['data'] as $s)
        {
            $data=array('nodeid' => $id,
                        'name' => $s['storage']);
            $PROXMOX->StorageAdd($data);

            if(strpos($s['content'],'vztmpl'))
            { 
                $content=$PROXMOX->connection->getContent($nodeadd['name'],$s['storage']);

                if($content['data'])foreach($content['data'] as $c)
                {
                    if(strpos($c['volid'],'vztmpl'))
                    {

                        $data=array('nodeid' => $id,
                                    'name' =>$c['volid']);

                        $PROXMOX->VZTemplateAdd($data);
                    }
                }
            }
        }

	    if (!isset($nodeadd['reuse'])) {
		    $SESSION->redirect('?m=proxmox_nodeinfo&id=' . $id);
	    }
    }
}

$SMARTY->assign('error', $error);
$SMARTY->assign('nodeadd', $nodeadd);

$SMARTY->display('proxmox_nodeadd.html');
?>
