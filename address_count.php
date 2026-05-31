<?php
ini_set('memory_limit', '8192M');
$data = file("./updatev12-access-pseudonymized.log", FILE_IGNORE_NEW_LINES);
$ip_addresses = array();

foreach ($data as $value) {
  preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $value, $ip_matches);
  array_push($ip_addresses, ...$ip_matches[0]);
}

?>