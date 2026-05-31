<?php
ini_set('memory_limit', '8192M');
$data = file("./updatev12-access-pseudonymized.log", FILE_IGNORE_NEW_LINES);
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