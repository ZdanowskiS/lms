<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$nodeid=intval($_GET['id']);

$templates=$DB->GetAllByKey('SELECT id, name FROM proxmox_node_vztemplates WHERE nodeid=?','name',array($nodeid));

$node=$PROXMOX->GetNode($nodeid);

$PROXMOX->connection->setlogin($node['name'],$node['ipaddr'], $node['login'], $node['realm'], $node['password']);

if($PROXMOX->connection->login())
{
    $storage=$PROXMOX->connection->getNodeStorage($node['name']);

    if($storage)foreach($storage['data'] as $k => $s)
    {
        if(strpos($s['content'],'vztmpl'))
        { 
            $content=$PROXMOX->connection->getContent($node['name'],$s['storage']);

            if($content)foreach($content['data'] as $k =>$t)
            {

                if(strpos($t['volid'],'vztmpl') && !$templates[$t['volid']])
                {
                    $data=array('nodeid' => $nodeid,
                        'name' => $t['volid']);

                    $PROXMOX->VZTemplateAdd($data);
                }
                else
                {
                    $templates[$t['volid']]['exists']=1;
                }        
            }
        }
    }

    if($templates)foreach($templates as $template)
    {
        if(!$storage['exists'] && !$DB->GetOne('SELECT 1 FROM proxmox_offerts WHERE vztemplateid=?',array($template['id'])))
        {
            $DB->Execute('DELETE FROM proxmox_storages WHERE id=?',array($template['id']));
        }
    }
}

$SESSION->redirect('?m=proxmox_nodeinfo&id='.$nodeid);

?>
