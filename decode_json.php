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

$specs=array();
foreach ($data as $value) {
    preg_match_all('/specs=(\S+)/', $value, $spec);

    array_push($specs, $spec[1][0]);
}


$decoded = base64_decode($specs[0]);
$uncompressed = @gzinflate(substr($decoded, 10));

echo print_r(json_decode($uncompressed, true));


foreach ($specs as $spec) {
    echo base64_decode($spec);
}
?>