<?php
//init_ITEC();
var_dump($this -> component_id);die();
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
if( isset( $_REQUEST['d'] ) ) {
    $data = decode($_REQUEST['d']);
    $aux = $this -> _ITExt -> get('_intervenciones','*',[$data['table'].'_id' => $data['id']]);
    $tiendas = $this -> _ITExt -> select('_centros_trabajo','*',['_centros_trabajo_id' => $aux['_centros_trabajo_id']]);
    if($tiendas){
        $center_coods = '{ lat: '.$tiendas[0]['latitude'].', lng: '.$tiendas[0]['longitude'].' }';
    }
} 
?>
function initMap_<?=$this -> component_id?>() {
    //geocoder = new google.maps.Geocoder();
    map_<?=$this -> component_id?> = new google.maps.Map(document.getElementById('map-<?=$this -> component_id?>'), {
        zoom: <?=(!empty($this->cfg( 'contenido', 'zoom' ))?$this->cfg( 'contenido', 'zoom' ):'8')?>,
        center: <?=$center_coods?>,
        heading: 90,
    tilt: 45
    });

    <?php
    // function to geocode address, it will return false if unable to geocode address
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
            echo "<strong>ERROR: {$resp['status']}</strong>";
            return false;
        }
    }

    if( $tiendas ) { foreach( $tiendas as $num => $tienda ) { 
        if( empty($tienda['latitude']) || empty($tienda['longitude']) ){
            $coords = geocode($tienda['direccion'].', '.$tienda['localidad'].', '.$tienda['provincia']);
            $tienda['latitude'] = $coords['latitude'];
            $tienda['longitude'] = $coords['longitude'];
            $this -> _ITExt -> update('_centros_trabajo', ['latitude' => $coords['latitude'], 'longitude' => $coords['longitude']], ['_centros_trabajo_id' => $tienda['_centros_trabajo_id']]);
        }
        $coordenadas[$tienda['latitude'].' - '.$tienda['longitude']][] = $tienda;
    }}

    $info = $this -> _ITEC -> get( 'reports', '*', ['hook' => '_centros_trabajo'] );
    if( $info ) {
        $details_url = 'https://maps.google.com/?q='.$tienda['latitude'].','.$tienda['longitude'];
    }
    
    foreach( $coordenadas as $sort => $tiendas ) {
        if( count($tiendas) > 1){
            foreach( $tiendas as $tienda) {                 
                $lat = (rand(0,1) === 1) ? $tienda['latitude'] + randy() : $tienda['latitude'] - randy();
                $long = (rand(0,1) === 1) ? $tienda['longitude'] + randy() : $tienda['longitude'] - randy();
                ?>
                var marker_<?=$this -> component_id?>_<?=$tienda['_centros_trabajo_id']?> = new google.maps.Marker({
                    position: { lat: <?=$lat?>, lng: <?=$long?> },
                    map: map_<?=$this -> component_id?>,
                    title: '<?=$tienda['centro_trabajo']?> (<?=$tienda['codigo_tienda']?>)',
                    animation: google.maps.Animation.DROP,
                });

                <?php if( $info ) { ?>
                    marker_<?=$this -> component_id?>_<?=$tienda['_centros_trabajo_id']?>.addListener( 'click', function(){
                        <?php
                        $url_d = encode(['table' => '_centros_trabajo', 'id' => $tienda['_centros_trabajo_id'], 'dsn' => $this -> _ITExt -> info()['dsn'] ]);
                        ?>
                        window.open('<?=$details_url?>','_blank');
                    } );
                <?php } else { ?>
                    __error('No es posible generar el detalle de los puntos del mapa, puede que falte el informe o no tenga el hook adecuado.');
                <?php } ?>
            <?php }
        } else { ?>
            var marker_<?=$this -> component_id?>_<?=$tiendas[0]['_centros_trabajo_id']?> = new google.maps.Marker({
                position: { lat: <?=$tiendas[0]['latitude']?>, lng: <?=$tiendas[0]['longitude']?> },
                map: map_<?=$this -> component_id?>,
                title: '<?=$tiendas[0]['centro_trabajo']?> (<?=$tiendas[0]['codigo_tienda']?>)',
                animation: google.maps.Animation.DROP,
            });

            <?php if( $info ) { ?>
                marker_<?=$this -> component_id?>_<?=$tiendas[0]['_centros_trabajo_id']?>.addListener( 'click', function(){
                    <?php
                    $url_d = encode(['table' => '_centros_trabajo', 'id' => $tiendas[0]['_centros_trabajo_id'], 'dsn' => $this -> _ITExt -> info()['dsn'] ]);
                    ?>
                    window.open('<?=$details_url?>','_blank');
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