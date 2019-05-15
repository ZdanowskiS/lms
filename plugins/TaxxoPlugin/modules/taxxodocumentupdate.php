<?php

$TAXXO = TaxxoPlugin::getTaxxoInstance();

$TAXXO->ApiAuthenticate();

$TAXXO->UpdateDocumentById($_GET['id']);

$TAXXO->ApiLogout();

header('Location: ?m=taxxodocumentinfo&id='.$_GET['id']);
?>
