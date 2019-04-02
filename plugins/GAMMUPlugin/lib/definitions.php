<?php

define('GAMMU_STATUS_UNKNOWN',1);
define('GAMMU_STATUS_NOACTION',2);
define('GAMMU_STATUS_REPLY',3);
define('GAMMU_STATUS_DTERMINATED',4);
define('GAMMU_STATUS_ANOTHERREPLIED',5);
define('GAMMU_STATUS_NOTSUPPORTED',6);
define('GAMMU_STATUS_TIMEOUT',7);

$GAMMUSTATUS = array(
	GAMMU_STATUS_UNKNOWN => trans('Unknown'),
	GAMMU_STATUS_NOACTION => trans('No action is needed'),
	GAMMU_STATUS_REPLY => trans('Replay is expected'),
	GAMMU_STATUS_DTERMINATED => trans('USSD dialog terminated'),
	GAMMU_STATUS_ANOTHERREPLIED => trans('Another client replied'),
	GAMMU_STATUS_NOTSUPPORTED => trans('Operation not supported'),
	GAMMU_STATUS_TIMEOUT => trans('Network timeout'),
);

define('GAMMU_CLASS_NORMAL',1);
define('GAMMU_CLASS_MULTIMEDIA',2);

$GAMMUCLASS = array (
	GAMMU_CLASS_NORMAL => trans('Normal'),
	GAMMU_CLASS_MULTIMEDIA => trans('Multimedia'),
);

if(isset($SMARTY))
{
	$SMARTY->assign('_GAMMUSTATUS',$GAMMUSTATUS);
	$SMARTY->assign('_GAMMUCLASS',$GAMMUCLASS);
}

?>
