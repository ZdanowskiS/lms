<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$nodeid=intval($_GET['id']);

$storages=$DB->GetAllByKey('SELECT id, name FROM proxmox_node_storages WHERE nodeid=?','name',array($nodeid));

$node=$PROXMOX->GetNode($nodeid);

$PROXMOX->connection->setlogin($node['name'],$node['ipaddr'], $node['login'], $node['realm'], $node['password']);

if($PROXMOX->connection->login())
{
    $nodestorages=$PROXMOX->connection->getNodeStorage($node['name']);

    if($nodestorages)foreach($nodestorages['data'] as $s)
    {
        if(!$storages[$s['storage']])
        {
            $data=array('nodeid' => $nodeid,
                        'name' => $s['storage']);

            $PROXMOX->StorageAdd($data);
        }
        else
        {
            $storages[$s['storage']]['exists']=1;
        }        
    }

    if($storages)foreach($storages as $storage)
    {
        if(!$storage['exists'] && !$DB->GetOne('SELECT 1 FROM proxmox_offerts WHERE storageid=?',array($storage['id'])))
        {
            $DB->Execute('DELETE FROM proxmox_storages WHERE id=?',array($storage['id']));
        }
    }
}

$SESSION->redirect('?m=proxmox_nodeinfo&id='.$nodeid);

?>
