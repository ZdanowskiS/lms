<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$layout['pagetitle'] = trans('Proxmox').' - '.trans('New VM/CT');

$vmctadd = isset($_POST['vmctadd']) ? $_POST['vmctadd'] : NULL;

if($vmctadd)
{
    if(!$vmctadd['offerid'])
        $error['offer'] = trans('Offer is required!');

    if(!$vmctadd['ipaddr'])
        $error['ipaddr'] = trans('IP address is required!');

    if(!$error)
    {
        $offer=$PROXMOX->GetOffer($vmctadd['offerid']);

        $node=$PROXMOX->GetNode($offer['nodeid']);

	    $PROXMOX->connection->setlogin($node['name'],$node['ipaddr'], $node['login'], $node['realm'], $node['password']);

	    $PROXMOX->connection->login();

	    $resources=$PROXMOX->connection->getResources();

	    $vmid=0;
	    if($resources)foreach($resources['data'] as $key => $resource){
		    if($resource['vmid']>$vmid)
			    $vmid=$resource['vmid'];
	    }
	    $vmid=$vmid+1;

        $vmctadd['vmid']=$vmid;
        $vmctadd['nodeid']=$offer['nodeid'];
        $vmctadd['type']=$offer['type'];

	    $data=array('customerid' => $assignmentlxcadd['customerid'],
			'nodeid' => ($assignmentlxcadd['nodeid'] ? $assignmentlxcadd['nodeid'] : 0),
			'offerlxcid' => $assignmentlxcadd['offerlxcid'],
			'vmid' => $vmid);

	    $param = array(
			'vmid' => $vmid,
			'hostname' => $vmctadd['name'],
			'cores' => $offer['cores'],
			'memory' => $offer['memory'],
			'onboot' => urlencode(1),
			'storage' => $offer['storage'],
			'rootfs' => $offer['storage'].':'.$offer['size'],
            'size' => $offer['size'],
			'password' => $offer['password'],
			'net0' => $offer['net'],
			'ostemplate' =>$offer['ostemplate'],
			'clone' =>$offer['clone'],
            'bridge' => $offer['bridge'],
            'nettype' => $PROXMOXNETTYPE[$offer['nettype']]
		);

	    $data['vmct']=$vmctadd;
	    $data['customer']=$LMS->GetCustomer($assignmentlxcadd['customerid']);

	    $param=$PROXMOX->parseParam($param, $data);

        if($offer['type']==PROXMOX_TYPE_CT)
        {
	        $PROXMOX->connection->addCT($param);
        }
        elseif($offer['type']==PROXMOX_TYPE_VM)
        {
            $PROXMOX->connection->addVM($param);
        }
        elseif($offer['type']==PROXMOX_TYPE_VMCLONE)
        {
            $PROXMOX->connection->addClone($param);
        }

	    $id=$PROXMOX->VMCTAdd($vmctadd);

	    if (!isset($vmctadd['reuse'])) {
		    $SESSION->redirect('?m=proxmox_vmctinfo&id='.$id);
	    }
    }
    $vmctdata=$vmctadd;
}
else{
    $vmctdata['customer']=$_GET['customerid'];
}

$LMS->InitXajax();
include(MODULES_DIR . DIRECTORY_SEPARATOR . 'nodexajax.inc.php');

$SMARTY->assign('xajax', $LMS->RunXajax());

$templates=$PROXMOX->GetOfferNames();
$SMARTY->assign('templates', $templates);

if (!ConfigHelper::checkConfig('phpui.big_networks')) {
    $SMARTY->assign('customers', $LMS->GetCustomerNames());
}
$SMARTY->assign('networks', $LMS->GetNetworks(true));
$SMARTY->assign('error', $error);
$SMARTY->assign('vmctdata', $vmctdata);

$SMARTY->display('proxmox_vmctadd.html');
?>
