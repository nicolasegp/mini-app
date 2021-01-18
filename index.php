<?php
require 'app.php';

(new App)
->config('mod_rewrite', true)
->config('404', 'app/404.html')
->config('mysqli', 'localhost', 'user', 'pass', 'database') // Comentar si no se va a probar la db

// Para indicar el index dejar la ruta vacia
->route('', function($app) {
	// Usar $app para obtener $app->url
	require 'app/inicio.html';
})

// Comentar si no se va a probar la db
->route('secc/db', function($app) {
	echo '<h1>Listado de tablas en la base de datos</h1>';
	if($buscar = $app->db->query('SHOW TABLES')) {
		echo '<ul>';
		while($ver = $buscar->fetch_row()) {
			echo "<li>{$ver[0]}</li>";
		}
		echo '</ul>';
		$buscar->free();
	}
	else {
		echo $app->db->error;
	}
	$app->db->close();
})

->route('api/hora', function() {
	header('Content-Type: application/json');
	sleep(1);
	echo json_encode([
		'hora' => (new DateTime)->format('H:i (P)')
	]);
})

->route('api/suma/(\d+)-(\d+)', function($app, $num1, $num2) {
	header('Content-Type: application/json');
	sleep(2);
	echo json_encode([
		'x' => $num1,
		'y' => $num2,
		'z' => $num1 + $num2
	]);
});

?>