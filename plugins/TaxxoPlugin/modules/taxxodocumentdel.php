<?php

$TAXXO = TaxxoPlugin::getTaxxoInstance();

$TAXXO->ApiAuthenticate();

$TAXXO->DeleteApiDocumentById($_GET['id']);

$TAXXO->DocumentDelete($_GET['id']);

$TAXXO->ApiLogout();

header('Location: ?m=taxxodocumentlist');
?>
