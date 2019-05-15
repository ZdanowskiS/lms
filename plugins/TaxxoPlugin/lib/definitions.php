<?php

define('TAXXO_TYPE_INVOICE',1);
define('TAXXO_TYPE_FILE',2);

$TAXXO_TYPE = array(
    TAXXO_TYPE_INVOICE	=> trans('Invoice from LMS'),
    TAXXO_TYPE_FILE		=> trans('Invoice from file'),
);

define('TAXXO_STATUS_SENT',0);
define('TAXXO_STATUS_WARN',1);
define('TAXXO_STATUS_ERROR',2);

$TAXXO_STATUS = array(
    TAXXO_STATUS_SENT => trans('Sent'),
    TAXXO_STATUS_WARN => trans('Warrning'),
    TAXXO_STATUS_ERROR => trans('Error'),
);

$SMARTY->assign('_TAXXO_TYPE', $TAXXO_TYPE);
$SMARTY->assign('_TAXXO_STATUS', $TAXXO_STATUS);
?>
