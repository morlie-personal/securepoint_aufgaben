<?php
ini_set("memory_limit", "8192M");

$files = glob(__DIR__ . "/*.log");

if (!$files) {
    echo "Keine Log Datei gefunden. Bitte Log Datei dem Ordner der Anwendung hinzufügen";
    error_log(
        "Keine Log Datei gefunden. Bitte Log Datei dem Ordner der Anwendung hinzufügen"
    );
    exit();
}

if (sizeof($files) > 1) {
    error_log(
        "Mehrere Log Dateien gefunden, es kann aber nur eine geladen werden... Lade $files[0]"
    );
}

$data = file($files[0], FILE_IGNORE_NEW_LINES);

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

echo "<pre>";
print_r($count);
echo "</pre>";
?>
