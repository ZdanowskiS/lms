<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$nodeid=intval($_GET['id']);

$bridges=$PROXMOX->GetNodeBridges($nodeid);
$bridges=$DB->GetAllByKey('SELECT id, name FROM proxmox_node_bridges WHERE nodeid=?',name,array($nodeid));

$node=$PROXMOX->GetNode($nodeid);

$PROXMOX->connection->setlogin($node['name'],$node['ipaddr'], $node['login'], $node['realm'], $node['password']);

if($PROXMOX->connection->login())
{
    $network=$PROXMOX->connection->getNodeNetwork($node['name']);

    if($network)foreach($network['data'] as $int)
    {
        if(!is_null($int['bridge_fd']))
        {
            if(!$bridges[$int['iface']])
            {
                $data=array('nodeid' => $nodeid,
                        'name' => $int['iface']);

                 $PROXMOX->BridgeAdd($data);
            }
            else
            {
                $bridges[$int['iface']]['exists']=1;
            }        
        }
    }

    foreach($bridges as $key => $bridge)
    {
        if(!$bridge['exists'] && !$DB->GetOne('SELECT 1 FROM proxmox_offerts WHERE bridgeid=?',array($bridge['id'])))
        {
            $DB->Execute('DELETE FROM proxmox_bridges WHERE id=?',array($bridge['id']));
        }
    }
}
$SESSION->redirect('?m=proxmox_nodeinfo&id='.$nodeid);

?>
