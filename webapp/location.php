<?php

require_once 'init.php';

//$adr = urldecode("Centrum plaza Gurgaon");

$url = "https://maps.googleapis.com/maps/api/geocode/json?address=Centrum+plaza+Gurgaon&sensor=false";
//$url = "https://www.google.co.in/?gfe_rd=cr&ei=S765VaSrGoLC8gelhquwDw&gws_rd=ssl#q=curl";

$result = Utils::getURLContents($url);

print_r($result);

?>