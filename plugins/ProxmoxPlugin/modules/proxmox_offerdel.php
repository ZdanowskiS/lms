<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$id = intval($_GET['id']);

if($id)
{
    $PROXMOX->DeleteOffer($id);
}

$SESSION->redirect('?m=proxmox_offerlist');
?>
