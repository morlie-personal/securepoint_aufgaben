<?php
ini_set('memory_limit', '8192M');
$files = glob(__DIR__ . '/*.log');

if (!$files) {
  echo "Keine Log Datei gefunden. Bitte Log Datei zu dem Ordner der Anwendung hinzufügen";
  error_log("Keine Log Datei gefunden. Bitte Log Datei zu dem Ordner der Anwendung hinzufügen");
  exit;
}

if (sizeof($files) > 1) {
     error_log("Mehrere Log Dateien gefunden, es kann aber nur eine geladen werden... Lade $files[0]");
}

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