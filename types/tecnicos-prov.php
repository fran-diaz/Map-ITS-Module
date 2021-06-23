<script>
var map_<?=$this -> component_id?>;
var marker_<?=$this -> component_id?>;

function initMap_<?=$this -> component_id?>() {
    map_<?=$this -> component_id?> = new google.maps.Map(document.getElementById('map-<?=$this -> component_id?>'), {
        zoom: <?=$this->cfg( 'contenido', 'zoom' )?>,
        gestureHandling: 'cooperative',
        center: { lat: 40.4381311, lng: -3.8196196 },
    });

    marker_<?=$this -> component_id?> = new google.maps.Marker({
        position: { lat: 40.4381311, lng: -3.8196196 },
        map: map_<?=$this -> component_id?>,
        title: '',
        animation: google.maps.Animation.DROP,
    });

    var info_template_<?=$this -> component_id?> = '<div id="content">' +
        '<h4>##TITLE##</h4>' +
        '<div>' +
        '<p>Listado de técnicos disponibles:<ul><li>Lorem Ipsum</li><li>Lorem Ipsum</li></ul>##INFO##</p>' +
        '</div></div>';

    var infowindow_<?=$this -> component_id?> = new google.maps.InfoWindow({
        content: info_template_<?=$this -> component_id?>
    });

    // NOTE: This uses cross-domain XHR, and may not work on older browsers.
    map_<?=$this -> component_id?>.data.loadGeoJson('/resources/GeoJSON/provincias-espanolas.geojson');

    // Set the stroke width, and fill color for each polygon
    map_<?=$this -> component_id?>.data.setStyle({
        fillColor: 'green',
        strokeWeight: 0.15
    });

    map_<?=$this -> component_id?>.data.addListener('mouseover', function(event) {
        map_<?=$this -> component_id?>.data.revertStyle();
        map_<?=$this -> component_id?>.data.overrideStyle(event.feature, { strokeWeight: 1 });
        marker_<?=$this -> component_id?>.setPosition({ lat: event.feature.h.geo_point_2d[0], lng: event.feature.h.geo_point_2d[1] });
        marker_<?=$this -> component_id?>.setVisible(true);
        marker_<?=$this -> component_id?>.setTitle(event.feature.h.provincia);
    });

    map_<?=$this -> component_id?>.data.addListener('mouseout', function(event) {
        map_<?=$this -> component_id?>.data.revertStyle();
        marker_<?=$this -> component_id?>.setVisible(false);
        infowindow_<?=$this -> component_id?>.close();
    });

    // Set the fill color to red when the feature is clicked.
    // Stroke weight remains 3.
    map_<?=$this -> component_id?>.data.addListener('click', function(event) {
        map_<?=$this -> component_id?>.data.overrideStyle(event.feature, { fillColor: 'red' });
        infowindow_<?=$this -> component_id?>.setContent(spinner_html);
        infowindow_<?=$this -> component_id?>.open(map_<?=$this -> component_id?>, marker_<?=$this -> component_id?>);
        var prov_<?=$this -> component_id?> = event.feature.h.provincia;
        var ccaa_<?=$this -> component_id?> = event.feature.h.ccaa;
        var component_<?=$this -> component_id?> = $('#map-<?=$this -> component_id?>').closest('.component').data('component');
        //console.log(event);
        $.ajax({
            type: 'POST',
            url: '/ajax',
            data: 'DEBUG=0&action=map/list-tecnicos&c='+component_<?=$this -> component_id?>+'&prov='+prov_<?=$this -> component_id?>+'&ccaa='+ccaa_<?=$this -> component_id?>,
            success: function( data ) {
                //var info = info_template.replace('##TITLE##', event.feature.h.provincia);
                //info = info.replace('##INFO##', event.feature.h.ccaa);
                infowindow_<?=$this -> component_id?>.setContent(data);
            }
        });
    });
}

$.loadScript( "https://maps.googleapis.com/maps/api/js?key=<?=MAPS_API_KEY?>&callback=initMap_<?=$this -> component_id?>", false );
</script>