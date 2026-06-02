<?php
ini_set("memory_limit", "8192M");
$files = glob(__DIR__ . "/*.log");

if (!$files) {
    echo "Keine Log Datei gefunden. Bitte Log Datei zu dem Ordner der Anwendung hinzufügen";
    error_log(
        "Keine Log Datei gefunden. Bitte Log Datei zu dem Ordner der Anwendung hinzufügen"
    );
    exit();
}

if (sizeof($files) > 1) {
    error_log(
        "Mehrere Log Dateien gefunden, es kann aber nur eine geladen werden... Lade $files[0]"
    );
}

$data = file($files[0], FILE_IGNORE_NEW_LINES);

//Aufgabe 1
$serial_numbers = [];

foreach ($data as $value) {
    preg_match_all("/serial=(\S+)/", $value, $serial_nums);
    array_push($serial_numbers, ...$serial_nums[0]);
}

$count = array_count_values($serial_numbers);
arsort($count);
$array = array_slice($count, 0, -(count($count) - 10));
echo "<h1>Aufgabe 1</h1>";
echo "<pre>";
print_r($array);
echo "</pre>";
echo "<br />";

//Aufgabe 2
$serial_numbers_to_mac = [];
$mac_to_serial_numbers = [];

foreach ($data as $valueIndex => $value) {
    preg_match_all("/serial=(\S+)/", $value, $serial_nums);
    preg_match_all("/specs=(\S+)/", $value, $spec);

    if (empty($serial_nums[1]) || empty($spec[1])) {
        $line = $valueIndex++;
        error_log(
            "Probleme mit der Formattierung in Zeile $line, bitte pruefen"
        );
        continue;
    }

    $decoded = base64_decode($spec[1][0]);
    $uncompressed = @gzinflate(substr($decoded, 10));
    $jsondecoded = json_decode($uncompressed, true);

    if (empty($jsondecoded["mac"]) || $jsondecoded["mac"] == null) {
        $line = $valueIndex++;
        error_log(
            "Probleme in dekodierten Daten bei Log-Zeile $line, bitte pruefen"
        );
        continue;
    } else {
        $serial_numbers_to_mac[$jsondecoded["mac"]][] = $serial_nums[1][0];
        $mac_to_serial_numbers[$serial_nums[1][0]][] = $jsondecoded["mac"];
    }

    // $serial_numbers_to_specs[$spec[1][0]][] = $serial_nums[1][0];
    // $specs_to_serial_numbers[$serial_nums[1][0]][] = $spec[1][0];
}

foreach ($serial_numbers_to_mac as $mac => $serials) {
    $serial_numbers_to_mac[$mac] = array_unique($serials);
}

foreach ($mac_to_serial_numbers as $serial => $macs) {
    $mac_to_serial_numbers[$serial] = array_unique($macs);
}

$count = array_map("count", $serial_numbers_to_mac);
arsort($count);
$serial_mac_array = array_slice($count, 0, -(count($count) - 11));

$count = array_map("count", $mac_to_serial_numbers);
arsort($count);
$mac_array = array_slice($count, 0, -(count($count) - 10));

echo "<h1>Aufgabe 2</h1>";
echo "<pre>";
print_r($serial_mac_array);
echo "</pre>";
echo "<pre>";
print_r($mac_array);
echo "</pre>";
echo "<br />";

//Aufgabe 3
$specs = [];
foreach ($data as $valueIndex => $value) {
    preg_match_all("/specs=(\S+)/", $value, $spec);
    preg_match_all("/serial=(\S+)/", $value, $serial_nums);
    if (empty($serial_nums[1]) || empty($spec[1])) {
        $line = $valueIndex++;
        error_log(
            "Probleme mit der Formattierung in Log-Zeile $line, bitte pruefen"
        );
        continue;
    }

    $decoded = base64_decode($spec[1][0]);
    $uncompressed = @gzinflate(substr($decoded, 10));
    $jsondecoded = json_decode($uncompressed, true);

    if (empty($jsondecoded["cpu"])) {
        $line = $valueIndex++;
        $jsondecoded["cpu"] = "keine CPU Angabe";
        $specs[$jsondecoded["cpu"]][] = $serial_nums[1][0];
        error_log(
            "Probleme in dekodierten Daten bei Log-Zeile $line, bitte pruefen"
        );
    } else {
        $specs[$jsondecoded["cpu"]][] = $serial_nums[1][0];
    }
}

$count = array_map("count", $specs);
arsort($count);

echo "<h1>Aufgabe 3</h1>";
echo "<pre>";
print_r($count);
echo "</pre>";
?>
