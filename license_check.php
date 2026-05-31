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
$serial_numbers_to_specs = array();


foreach ($data as $valueIndex => $value) {
    preg_match_all('/serial=(\S+)/', $value, $serial_nums);
    preg_match_all('/specs=(\S+)/', $value, $spec);


    $serial_numbers_to_specs[$spec[1][0]][] = $serial_nums[1][0];
}

foreach ($serial_numbers_to_specs as $spec => $serials) {
    $serial_numbers_to_specs[$spec] = array_unique($serials);
}


echo '<pre>'; print_r($serial_numbers_to_specs); echo '</pre>';

?>