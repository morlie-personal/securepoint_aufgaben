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

    //Aufgabe 1
    $ip_addresses = array();

    foreach ($data as $value) {
        preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $value, $ip_matches);
        array_push($ip_addresses, ...$ip_matches[0]);
    }

    $count = array_count_values($ip_addresses);
    arsort($count);
    $array = array_slice($count, 0, -(count($count)-10));
    echo '<h1>Aufgabe 1</h1>';
    echo '<pre>'; print_r($array); echo '</pre>';
    echo '<br />';



    //Aufgabe 2
    $serial_numbers_to_specs_total = array();
    $serial_numbers_to_specs = array();
    $specs_to_serial_numbers = array();

    foreach ($data as $valueIndex => $value) {
        preg_match_all('/serial=(\S+)/', $value, $serial_nums);
        preg_match_all('/specs=(\S+)/', $value, $spec);

        if (empty($serial_nums[1]) || empty($spec[1])) {
            $line = $valueIndex++;
            error_log("Probleme mit der Formattierung in Zeile $line, bitte pruefen");
            continue;
        }

        $serial_numbers_to_specs[$spec[1][0]][] = $serial_nums[1][0];
        $specs_to_serial_numbers[$serial_nums[1][0]][] = $spec[1][0];
    }

    foreach ($serial_numbers_to_specs as $spec => $serials) {
        $serial_numbers_to_specs_total[$spec] = $serials;
    }

    foreach ($serial_numbers_to_specs as $spec => $serials) {
        $serial_numbers_to_specs[$spec] = array_unique($serials);
    }

    foreach ($specs_to_serial_numbers as $serial => $specs) {
        $specs_to_serial_numbers[$serial] = array_unique($specs);
    }

    $count = array_map('count', $specs_to_serial_numbers);
    arsort($count);
    $serial_array_total = array_slice($count, 0, -(count($count)-10));

    $count = array_map('count', $serial_numbers_to_specs);
    arsort($count);
    $serial_array = array_slice($count, 0, -(count($count)-10));

    $count = array_map('count', $specs_to_serial_numbers);
    arsort($count);
    $spec_array = array_slice($count, 0, -(count($count)-10));

    echo '<h1>Aufgabe 2</h1>';
    echo '<pre>'; print_r($serial_array_total); echo '</pre>';
    echo '<pre>'; print_r($serial_array); echo '</pre>';
    echo '<pre>'; print_r($spec_array); echo '</pre>';
    echo '<br />';


    //Aufgabe 3
    $specs=array();
    foreach ($data as $valueIndex => $value) {
        preg_match_all('/specs=(\S+)/', $value, $spec);
        preg_match_all('/serial=(\S+)/', $value, $serial_nums);
        if (empty($serial_nums[1]) || empty($spec[1])) {
            $line = $valueIndex++;
            error_log("Probleme mit der Formattierung in Log-Zeile $line, bitte pruefen");
            continue;
        }
        
        $decoded = base64_decode($spec[1][0]);
        $uncompressed = @gzinflate(substr($decoded, 10));
        $jsondecoded = json_decode($uncompressed, true);

        if (empty($jsondecoded["cpu"])) {
            $line = $valueIndex++;
            $jsondecoded["cpu"] = "keine CPU Angabe";
            $specs[$jsondecoded["cpu"]][] = $serial_nums[1][0];
            error_log("Probleme in dekodierten Daten bei Log-Zeile $line, bitte pruefen");
        } else {
            $specs[$jsondecoded["cpu"]][] = $serial_nums[1][0];
        }    
    }

    $count = array_map('count', $specs);
    arsort($count);

    echo '<h1>Aufgabe 3</h1>';
    echo '<pre>'; print_r($count); echo '</pre>';
    ?>