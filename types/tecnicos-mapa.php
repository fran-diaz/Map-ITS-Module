<?php
//init_ITEC();

if( ! function_exists( 'randy' ) ) {
    function randy(){
        return floatval('0.00000'.rand()) + floatval('0.00000'.rand()) - floatval('0.00000'.rand());
    }
}
?>
<script>
//var geocoder;
var map_<?=$this -> component_id?>;

function codeAddress( address ) {
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == 'OK') {
        return results[0].geometry.location;
      } else {
        console.log('Geocode was not successful for address '+address+' for the following reason: ' + status);
      }
    });
}
<?php
$this -> _ITE -> debug -> disabled = true;
$coordenadas = [];

$center_coods = '{ lat: 40.4381311, lng: -3.8196196 }';
if( isset( $_REQUEST['d'] ) && $_SESSION['d'] != false ) {
    $data = decode($_REQUEST['d']);
    $tecnicos = $this -> _ITExt -> select('_tecnicos','*',[$data['table'].'_id' => $data['id']]);
    if($tecnicos){
        $center_coods = '{ lat: '.$tecnicos[0]['latitude'].', lng: '.$tecnicos[0]['longitude'].' }';
    }
} else {
    $tecnicos = $this -> _ITExt -> select('_tecnicos');
}
?>

function initMap_<?=$this -> component_id?>() {
    //geocoder = new google.maps.Geocoder();
    map_<?=$this -> component_id?> = new google.maps.Map(document.getElementById('map-<?=$this -> component_id?>'), {
        zoom: <?=$this->cfg( 'contenido', 'zoom' )?>,
        gestureHandling: 'cooperative',
        center: <?=$center_coods?>,
        heading: 90,
    tilt: 45
    });

    <?php
    // function to geocode address, it will return false if unable to geocode address
    if( ! function_exists( 'geocode' ) ) {
        function geocode($address){
            // url encode the address
            $address = urlencode($address);
             
            // google map geocode api url
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=".MAPS_API_KEY;
         
            // get the json response
            $resp_json = file_get_contents($url);
             
            // decode the json
            $resp = json_decode($resp_json, true);
         
            // response status will be 'OK', if able to geocode given address 
            if($resp['status']=='OK'){
         
                // get the important data
                $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
                $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
                $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";
                 
                // verify if data is complete
                if($lati && $longi && $formatted_address){
                 
                    // put the data in the array
                    $data_arr = [
                        'latitude' => $lati,
                        'longitude' => $longi,
                        'formatted_address' => $formatted_address,
                    ];            
                     
                    return $data_arr;
                     
                }else{
                    return false;
                }
            }
         
            else{
                echo "<strong>ERROR: {$resp['status']} ($address)</strong>";
                return false;
            }
        }
    }
    
    $info = $this -> _ITEC -> get( 'reports', '*', ['hook' => '_tecnicos'] );
    if( $info ) {
        $details_url = '/informes/'.$info['reports_id'];
    }

    if( $tecnicos ) { foreach( $tecnicos as $num => $tecnico ) { 
        if( empty($tecnico['latitude']) || empty($tecnico['longitude']) ){
            if( empty( $tecnico['direccion'] ) && empty( $tecnico['localidad'] ) && empty( $tecnico['provincia'] ) ) {
                continue;
            }
            $coords = geocode($tecnico['direccion'].', '.$tecnico['localidad'].', '.$tecnico['provincia']);
            $tecnico['latitude'] = $coords['latitude'];
            $tecnico['longitude'] = $coords['longitude'];
            $this -> _ITExt -> update('_tecnicos', ['latitude' => $coords['latitude'], 'longitude' => $coords['longitude']], ['_tecnicos_id' => $tecnico['_tecnicos_id']]);
        }
        $coordenadas[$tecnico['latitude'].' - '.$tecnico['longitude']][] = $tecnico;
    }}

    
    foreach( $coordenadas as $sort => $tecnicos ) {
        if( count($tecnicos) > 1){
            foreach( $tecnicos as $tecnico) {                 
                $lat = (rand(0,1) === 1) ? $tecnico['latitude'] + randy() : $tecnico['latitude'] - randy();
                $long = (rand(0,1) === 1) ? $tecnico['longitude'] + randy() : $tecnico['longitude'] - randy();
                ?>
                var marker_<?=$this -> component_id?>_<?=$tecnico['_tecnicos_id']?> = new google.maps.Marker({
                    position: { lat: <?=$lat?>, lng: <?=$long?> },
                    map: map_<?=$this -> component_id?>,
                    title: '<?=$tecnico['nombre']?> (<?=$tecnico['telefono']?>)',
                    animation: google.maps.Animation.DROP,
                });

                <?php if( $info ) { ?>
                    marker_<?=$this -> component_id?>_<?=$tecnico['_tecnicos_id']?>.addListener( 'click', function(){
                        <?php
                        $url_d = encode(['table' => '_tecnicos', 'id' => $tecnico['_tecnicos_id'], 'dsn' => $this -> _ITExt -> info()['dsn'] ]);
                        ?>
                        window.location.href = '<?=$details_url?>?d=<?=$url_d?>';
                    } );
                <?php } else { ?>
                    __error('No es posible generar el detalle de los puntos del mapa, puede que falte el informe o no tenga el hook adecuado.');
                <?php } ?>
            <?php }
        } else { ?>
            var marker_<?=$this -> component_id?>_<?=$tecnicos[0]['_tecnicos_id']?> = new google.maps.Marker({
                position: { lat: <?=$tecnicos[0]['latitude']?>, lng: <?=$tecnicos[0]['longitude']?> },
                map: map_<?=$this -> component_id?>,
                title: '<?=$tecnicos[0]['nombre']?> (<?=$tecnicos[0]['telefono']?>)',
                animation: google.maps.Animation.DROP,
            });

            <?php if( $info ) { ?>
                marker_<?=$this -> component_id?>_<?=$tecnicos[0]['_tecnicos_id']?>.addListener( 'click', function(){
                    <?php
                    $url_d = encode(['table' => '_tecnicos', 'id' => $tecnicos[0]['_tecnicos_id'], 'dsn' => $this -> _ITExt -> info()['dsn'] ]);
                    ?>
                    window.location.href = '<?=$details_url?>?d=<?=$url_d?>';
                } );
            <?php } else { ?>
                __error('No es posible generar el detalle de los puntos del mapa, puede que falte el informe o no tenga el hook adecuado.');
            <?php } ?>
        <?php } ?>
    <?php }
    $this -> _ITE -> debug -> disabled = false;
    ?>
    

    
}

$.loadScript( "https://maps.googleapis.com/maps/api/js?key=<?=MAPS_API_KEY?>&callback=initMap_<?=$this -> component_id?>", false );

</script>