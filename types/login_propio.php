<?php
//init_ITEC();

if( ! function_exists( 'randy' ) ) {
    function randy(){
        return floatval('0.00000'.rand()) + floatval('0.00000'.rand()) - floatval('0.00000'.rand());
    }
}
?>
<script>
var map_<?=$this -> component_id?>;

function initMap_<?=$this -> component_id?>() {
    map_<?=$this -> component_id?> = new google.maps.Map(document.getElementById('map-<?=$this -> component_id?>'), {
        zoom: <?=$this->cfg( 'contenido', 'zoom' )?>,
        gestureHandling: 'cooperative',
        center: { lat: 40.4381311, lng: -3.8196196 },
        heading: 90,
    tilt: 45
    });

    <?php
    $this -> _ITE -> debug -> disabled = true;
    $coordenadas = [];

    $logins_info = $this -> _ITEC -> select('users__trace','*',['users_id' => $_SESSION['uid'], 'aux' => 'login', 'LIMIT' => '30','ORDER' => ['date' => 'DESC'] ]);
    
    if( $logins_info ) { foreach( $logins_info as $num => $login ) { 
        $coordenadas[$login['latitude'].'-'.$login['longitude']][] = $login;
    }}

    foreach( $coordenadas as $sort => $logins ) {
        if( count($logins) > 1){
            foreach( $logins as $login) {                 
                $lat = (rand(0,1) === 1) ? $login['latitude'] + randy() : $login['latitude'] - randy();
                $long = (rand(0,1) === 1) ? $login['longitude'] + randy() : $login['longitude'] - randy();
                ?>
            new google.maps.Marker({
                position: { lat: <?=$lat?>, lng: <?=$long?> },
                map: map_<?=$this -> component_id?>,
                title: '<?=$_ITE -> funcs -> date_format( $login['date'], 5 )?>',
                animation: google.maps.Animation.DROP,
            });
            <?php }
        } else { ?>
            new google.maps.Marker({
                position: { lat: <?=$logins[0]['latitude']?>, lng: <?=$logins[0]['longitude']?> },
                map: map_<?=$this -> component_id?>,
                title: '<?=$_ITE -> funcs -> date_format( $logins[0]['date'], 5 )?>',
                animation: google.maps.Animation.DROP,
            });
        <?php } ?>
    <?php }
    $this -> _ITE -> debug -> disabled = false;
    ?>
    

    
}
$.loadScript( "https://maps.googleapis.com/maps/api/js?key=<?=MAPS_API_KEY?>&callback=initMap_<?=$this -> component_id?>", false );
</script>
