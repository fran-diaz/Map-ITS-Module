<?php
init_ITEC();
$_ITExt = init_DSN();

$prov = $_REQUEST['prov'];
$ccaa = $_REQUEST['ccaa'];

$tecnicos = $_ITExt -> select( '_tecnicos', '*', [ 'provincia' => $prov ] );
if( $tecnicos ){
	?>
	<div id="content">
        <h4><?=$prov?> / <?=$ccaa?></h4>
        <div>
        	<p>Listado de técnicos disponibles:
        		<ul>
        			<?php
        			foreach( $tecnicos as $tecnico ) {
        				echo '<li>'.$tecnico['nombre'].' ('.$tecnico['telefono'].')</li>';
        			}
        			?>
        		</ul>
        	</p>
        </div>
    </div>
	<?php
	echo '<ul class="list-group">';
	foreach( $slides as $slide ) {
		$selected_class = ( $slide['slideshow__slides_id'] === $selected_slide ) ? ' list-group-item-success' : '';
		$d = encode( [
			'table' => 'slideshow__slides', 
			'id' => $slide['slideshow__slides_id'], 
			'field' => 'slide', 
			'value' => $slide['slide'] 
		] );
		?>
		<li class="list-group-item d-flex justify-content-between align-items-center slideshow__slide  <?=$selected_class?> " data-id="<?=$slide['slideshow__slides_id']?>" data-d="<?=$d?>" data-context="slideshow__slide">
	        <?=$slide['slide']?>
	    </li>
		<?php
	}
	echo '</ul>';
} else {
	echo '<p>No hay técnicos todavía.</p>';
}
?>
