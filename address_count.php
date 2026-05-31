<?php
ini_set('memory_limit', '8192M');
$files = glob(__DIR__ . '/*.log');
$data = file($files[0], FILE_IGNORE_NEW_LINES);
$ip_addresses = array();

foreach ($data as $value) {
  preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $value, $ip_matches);
  array_push($ip_addresses, ...$ip_matches[0]);
}

$count = array_count_values($ip_addresses);
arsort($count);
$array = array_slice($count, 0, -(count($count)-10));
echo '<pre>'; print_r($array); echo '</pre>';
?>