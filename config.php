<?php
/**
 * Configuración default para el componente text
 */

return [
	'name' => 'Mapa',
	'description' => 'Un mapa interactivo de Google Maps',
	'general' => [
		'nombre' => [
			'type' => 'varchar',
			'name' => 'Nombre',
			'detail' => 'Nombre del componente',
			'required' => false,
			'value' => 'Texto',
		],
		'invisible_box' => [
			'type' => 'checkbox',
			'name' => '¿Caja transparente?',
			'detail' => 'Caja visible o no visible',
			'required' => false,
			'default_values' => [
				'true' => 'Si',
				'false' => 'No',
			],
			'value' => 'false',
		],
		'ancho' => [
			'type' => 'enum',
			'name' => 'Anchura del componente',
			'detail' => 'Ancho de la caja del componente',
			'required' => false,
			'default_values' => [
				'3' => '25%',
				'4' => '30%',
				'6' => '50%',
				'8' => '60%',
				'9' => '75%',
				'12' => '100%',
			],
			'value' => '4',
		],
		'alto' => [
			'type' => 'enum',
			'name' => 'Altura del componente',
			'detail' => 'Ancho de la caja del componente',
			'required' => false,
			'default_values' => [
				'h-md-auto' => 'Auto',
				'h-md-25' => '25%',
				'h-md-50' => '50%',
				'h-md-75' => '75%',
				'h-md-100' => '100%',
			],
			'value' => 'h-md-50',
		],
	],
	'conexión' => [
		'external_connection' => [
			'type' => 'connections',
			'name' => 'Conexión externa',
			'detail' => 'Conexión a un servidor de base de datos externo',
			'external_table' => 'system__connections',
			'external_field' => 'connection',
			'value' => '',
		],
	],
	'contenido' => [
		'map_type' => [
			'type' => 'enum',
			'name' => 'Tipo de mapa',
			'detail' => 'El tipo de mapa a mostrar',
			'required' => false,
			'default_values' => [
				'tecnicos-prov' => 'Técnicos por provincia',
				'tecnicos-mapa' => 'Técnicos en mapa',
				'tiendas-mapa' => 'Centros de trabajo en mapa',
				'intervenciones-mapa' => 'Intervención en mapa',
				'login_propio' => 'Inicios de sesión propios (max. 30)',
			],
			'value' => 'login_propio',
		],
		'zoom' => [
			'type' => 'varchar',
			'name' => 'Zoom del mapa',
			'detail' => 'El zoom sobre el mapa a mostrar',
			'required' => false,
			'value' => '6',
		],
		'name_filter' => [
			'type' => 'varchar',
			'name' => 'Filtrar por nombre',
			'detail' => 'Texto por el que filtrar los nombres',
			'required' => false,
			'value' => '',
		],
	],
];