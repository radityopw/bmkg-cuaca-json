<?php 

$url = "https://data.bmkg.go.id/DataMKG/MEWS/DigitalForecast/DigitalForecast-Indonesia.xml";

$xml = simplexml_load_file($url);

$dir = "cuaca/";

$kota = array();

$forecast = array();

$i = 0;

foreach($xml->forecast->area as $area){

	$area = (json_decode(json_encode($area),TRUE));
	
	$kota[$i] = array();
	$kota[$i]['id'] = $area['@attributes']['id'];
	$kota[$i]['latitude'] = $area['@attributes']['latitude'];
	$kota[$i]['longitude'] = $area['@attributes']['longitude'];
	$kota[$i]['level'] = $area['@attributes']['level'];
	$kota[$i]['domain'] = $area['@attributes'];
	$kota[$i]['nama'] = $area['name'][1];

	$forecast[$kota[$i]['id']] = array();

	foreach($area['parameter'] as $param){
		$forecast[$kota[$i]['id']][$param['@attributes']['id']] = array();
		
		foreach($param['timerange'] as $time){
			
		}
		
	}
	$i++;
}
