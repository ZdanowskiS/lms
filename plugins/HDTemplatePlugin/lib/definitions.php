<?php

define('HDT_TYPE_NONE',0);
define('HDT_TYPE_TICKET',1);
define('HDT_TYPE_MESSAGE',2);

$HDTTYPES = array(
    HDT_TYPE_NONE	=> trans('none'),
    HDT_TYPE_TICKET		=> trans('New Ticket'),
    HDT_TYPE_MESSAGE	=> trans('New Message'),
);

$SMARTY->assign('_HDTTYPES', $HDTTYPES);
?>
