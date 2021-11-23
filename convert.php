<?php 

chdir(dirname(__FILE__));

require 'vendor/autoload.php';

$g_iterator=-1;

function process_api($url){
    global $g_iterator;
    
    $g_iterator++;

    //$url = "https://data.bmkg.go.id/DataMKG/MEWS/DigitalForecast/DigitalForecast-Indonesia.xml";
    
    $existing_kota = array();
    
    if(file_exists("cuaca/kota.json")){
        $existing_kota = json_decode(stripslashes(file_get_contents("cuaca/kota.json")),true);
        echo json_last_error_msg();
        echo PHP_EOL;
    }

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
                        'value_c' => $time['value'][0],
                        'value_f' => $time['value'][1]
                    );
                    
                    $j++;
                }
            }elseif(in_array($param_id,array("wd"))){
                $j = 0;
                foreach($param['timerange'] as $time){
                    $forecast[$kota[$i]['id']][$param_id][$j] = array(
                        'datetime' => $time['@attributes']['datetime'],
                        'value_deg' => $time['value'][0],
                        'value_CARD' => $time['value'][1],
                        'value_SEXA' => $time['value'][2]
                    );
                    
                    $j++;
                }
            }elseif(in_array($param_id,array("ws"))){
                $j = 0;
                foreach($param['timerange'] as $time){
                    $forecast[$kota[$i]['id']][$param_id][$j] = array(
                        'datetime' => $time['@attributes']['datetime'],
                        'value_Kt' => $time['value'][0],
                        'value_MPH' => $time['value'][1],
                        'value_KPH' => $time['value'][2],
                        'value_MS' => $time['value'][3]
                    );
                    
                    $j++;
                }
            }
            
            
            
        }
        $i++;
    }

    @mkdir('cuaca');
    
    file_put_contents("cuaca/kota_".$g_iterator.".json",json_encode($kota));
    
    if($existing_kota){
        
       $kota = array_merge($existing_kota,$kota);
        
    }

    file_put_contents("cuaca/kota.json",json_encode($kota));

    foreach($forecast as $id_kota=>$f_kota){
        file_put_contents("cuaca/forecast_".$id_kota.".json",json_encode($f_kota));
    }
}

@mkdir("xml");


$url = "https://data.bmkg.go.id/prakiraan-cuaca/";

$httpClient = new \simplehtmldom\HtmlWeb();
$response = $httpClient->load($url);

$links = $response->find('table.table tbody tr td pre a');

foreach($links as $link){
    $url_p = $url.($link->href);
    $url_p_hash = time()."_".md5($url_p);
    $local_url = "xml/".$url_p_hash.".xml";
    echo "processing ".$url_p.PHP_EOL;
    echo "with name ".$local_url.PHP_EOL;
    passthru("curl -s ".$url_p." > ".$local_url);
    process_api($url_p);
}