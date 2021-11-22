<?php 

chdir(dirname(__FILE__));

$url = "https://data.bmkg.go.id/DataMKG/MEWS/DigitalForecast/DigitalForecast-Indonesia.xml";

$xml = simplexml_load_file($url);

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
	$kota[$i]['domain'] = $area['@attributes']['domain'];
	$kota[$i]['nama'] = $area['name'][1];

	$forecast[$kota[$i]['id']] = array();

	foreach($area['parameter'] as $param){
        
        $param_id = $param['@attributes']['id'];
        
		$forecast[$kota[$i]['id']][$param_id] = array();
        
        if(in_array($param_id,array("hu","humax","humin","weather"))){
            $j = 0;
            foreach($param['timerange'] as $time){
                $forecast[$kota[$i]['id']][$param_id][$j] = array(
                    'datetime' => $time['@attributes']['datetime'],
                    'value' => $time['value']
                );
                
                $j++;
            }
        }elseif(in_array($param_id,array("t","tmax","tmin"))){
            $j = 0;
            foreach($param['timerange'] as $time){
                $forecast[$kota[$i]['id']][$param_id][$j] = array(
                    'datetime' => $time['@attributes']['datetime'],
                    'value1' => $time['value'][0],
                    'value2' => $time['value'][1]
                );
                
                $j++;
            }
        }elseif(in_array($param_id,array("wd"))){
            $j = 0;
            foreach($param['timerange'] as $time){
                $forecast[$kota[$i]['id']][$param_id][$j] = array(
                    'datetime' => $time['@attributes']['datetime'],
                    'value1' => $time['value'][0],
                    'value2' => $time['value'][1],
                    'value3' => $time['value'][2]
                );
                
                $j++;
            }
        }elseif(in_array($param_id,array("ws"))){
            $j = 0;
            foreach($param['timerange'] as $time){
                $forecast[$kota[$i]['id']][$param_id][$j] = array(
                    'datetime' => $time['@attributes']['datetime'],
                    'value1' => $time['value'][0],
                    'value2' => $time['value'][1],
                    'value3' => $time['value'][2],
                    'value4' => $time['value'][3]
                );
                
                $j++;
            }
        }
		
		
		
	}
	$i++;
}

@mkdir('cuaca');

file_put_contents("cuaca/kota.json",json_encode($kota));

foreach($forecast as $id_kota=>$f_kota){
    file_put_contents("cuaca/forecast_".$id_kota.".json",json_encode($f_kota));
}