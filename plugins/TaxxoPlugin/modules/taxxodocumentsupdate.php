<?php

$TAXXO = TaxxoPlugin::getTaxxoInstance();

$TAXXO->ApiAuthenticate();

$TAXXO->UpdateDocuments($y,$m);

$TAXXO->ApiLogout();

header('Location: ?m=taxxoactions');
?>
