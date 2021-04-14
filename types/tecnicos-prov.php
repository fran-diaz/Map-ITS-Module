<script>
var map_<?=$component_info['report_components_id']?>;
var marker_<?=$component_info['report_components_id']?>;

function initMap_<?=$component_info['report_components_id']?>() {
    map_<?=$component_info['report_components_id']?> = new google.maps.Map(document.getElementById('map-<?=$component_info['report_components_id']?>'), {
        zoom: <?=$this->cfg( 'contenido', 'zoom' )?>,
        center: { lat: 40.4381311, lng: -3.8196196 },
    });

    marker_<?=$component_info['report_components_id']?> = new google.maps.Marker({
        position: { lat: 40.4381311, lng: -3.8196196 },
        map: map_<?=$component_info['report_components_id']?>,
        title: '',
        animation: google.maps.Animation.DROP,
    });

    var info_template_<?=$component_info['report_components_id']?> = '<div id="content">' +
        '<h4>##TITLE##</h4>' +
        '<div>' +
        '<p>Listado de técnicos disponibles:<ul><li>Lorem Ipsum</li><li>Lorem Ipsum</li></ul>##INFO##</p>' +
        '</div></div>';

    var infowindow_<?=$component_info['report_components_id']?> = new google.maps.InfoWindow({
        content: info_template_<?=$component_info['report_components_id']?>
    });

    // NOTE: This uses cross-domain XHR, and may not work on older browsers.
    map_<?=$component_info['report_components_id']?>.data.loadGeoJson('/resources/GeoJSON/provincias-espanolas.geojson');

    // Set the stroke width, and fill color for each polygon
    map_<?=$component_info['report_components_id']?>.data.setStyle({
        fillColor: 'green',
        strokeWeight: 0.15
    });

    map_<?=$component_info['report_components_id']?>.data.addListener('mouseover', function(event) {
        map_<?=$component_info['report_components_id']?>.data.revertStyle();
        map_<?=$component_info['report_components_id']?>.data.overrideStyle(event.feature, { strokeWeight: 1 });
        marker_<?=$component_info['report_components_id']?>.setPosition({ lat: event.feature.h.geo_point_2d[0], lng: event.feature.h.geo_point_2d[1] });
        marker_<?=$component_info['report_components_id']?>.setVisible(true);
        marker_<?=$component_info['report_components_id']?>.setTitle(event.feature.h.provincia);
    });

    map_<?=$component_info['report_components_id']?>.data.addListener('mouseout', function(event) {
        map_<?=$component_info['report_components_id']?>.data.revertStyle();
        marker_<?=$component_info['report_components_id']?>.setVisible(false);
        infowindow_<?=$component_info['report_components_id']?>.close();
    });

    // Set the fill color to red when the feature is clicked.
    // Stroke weight remains 3.
    map_<?=$component_info['report_components_id']?>.data.addListener('click', function(event) {
        map_<?=$component_info['report_components_id']?>.data.overrideStyle(event.feature, { fillColor: 'red' });
        infowindow_<?=$component_info['report_components_id']?>.setContent(spinner_html);
        infowindow_<?=$component_info['report_components_id']?>.open(map_<?=$component_info['report_components_id']?>, marker_<?=$component_info['report_components_id']?>);
        var prov_<?=$component_info['report_components_id']?> = event.feature.h.provincia;
        var ccaa_<?=$component_info['report_components_id']?> = event.feature.h.ccaa;
        var component_<?=$component_info['report_components_id']?> = $('#map-<?=$component_info['report_components_id']?>').closest('.component').data('component');
        //console.log(event);
        $.ajax({
            type: 'POST',
            url: '/ajax',
            data: 'DEBUG=0&action=map/list-tecnicos&c='+component_<?=$component_info['report_components_id']?>+'&prov='+prov_<?=$component_info['report_components_id']?>+'&ccaa='+ccaa_<?=$component_info['report_components_id']?>,
            success: function( data ) {
                //var info = info_template.replace('##TITLE##', event.feature.h.provincia);
                //info = info.replace('##INFO##', event.feature.h.ccaa);
                infowindow_<?=$component_info['report_components_id']?>.setContent(data);
            }
        });
    });
}

$.loadScript( "https://maps.googleapis.com/maps/api/js?key=AIzaSyCwzaC6vTV8unjsZy4vsZ_h8-X0367J7D4&callback=initMap_<?=$component_info['report_components_id']?>", false );
</script>