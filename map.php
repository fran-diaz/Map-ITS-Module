<?php
/**
 * Componente text
 */

class map extends base_component implements components_interface {
	
	public function make_map() : string { 
		$html = '';
		ob_start();

		echo '<div id="map-'.$this -> component_id.'" class="h-100"></div>';
		include(__DIR__.'/types/'.$this -> cfg('contenido','map_type').'.php');

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function gen_content( ) : string {		
		return $this -> make_map();
	}
}