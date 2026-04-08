<?php
$apikey = '';
$json = file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?key='.$apikey); 
$data = json_decode($json);

header('Content-disposition: attachment; filename=googlefonts.json');
header('Content-type: application/json');

echo( $json);