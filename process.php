<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once 'vendor/autoload.php';
$gi = geoip_open("/usr/share/GeoIP/GeoIPCity.dat",GEOIP_STANDARD);

$array = $fields = array(); $i = 0;
$handle = @fopen("users.csv", "r");
$header = null;
while ($row = fgetcsv($handle)) {
	if ($header === null) {
		array_push($row, 'country', 'region');
		$header = $row;
		$array[] = $row;
		continue;
	}
	$record = geoip_record_by_addr($gi, $row[7]);
	// Also available $record->city,postal_code,latitude,longitude,metro_code,area_code,continent_code
	array_push($row, $record->country_name, $GEOIP_REGION_NAME[$record->country_code][$record->region]); 
	$array[] = array_combine($header, $row);
}
fclose($handle);
geoip_close($gi);

$handle = fopen('users-appended.csv', 'w');

foreach ($array as $fields) {
    fputcsv($handle, $fields);
}

fclose($handle);
//var_dump($array);
