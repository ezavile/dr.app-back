<?php 
require 'Slim/Slim.php'; 
require 'Config/db.php';
require 'Model/Doctor.php';
require 'Model/Paciente.php';
require 'Model/Login.php';


/* Register autoloader and instantiate Slim */
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
date_default_timezone_set('America/Mexico_City');

header('Access-Control-Allow-Origin: *');

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 0');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
}

function upload(){
	if ( !empty( $_FILES ) ) {
		$tempPath = $_FILES[ 'file' ][ 'tmp_name' ];
		$url = 'uploads' . DIRECTORY_SEPARATOR . $_FILES[ 'file' ][ 'name' ];
		$uploadPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $url;
		move_uploaded_file( $tempPath, $uploadPath );
		$answer = array( 'url' => 'http://localhost/Dr.App/back/' . $url);
		echo json_encode($answer);
	} else {
		$answer = array( 'error' => 'No se subio la imagen correctamente');
		echo json_encode($answer);
	}
}

$app->post('/upload', 'upload');
$app->post('/login', 'login');
$app->get('/especialidades', 'getEspecialidades');

//Paciente
$app->post('/paciente', 'addPaciente');
$app->post('/paciente/comentario', 'addComentario');
$app->post('/paciente/mensaje', 'addMensaje');
$app->post('/paciente/cita', 'addCita');
$app->put('/paciente', 'updatePaciente');

//Doctor
$app->post('/doctor', 'addDoctor');
$app->get('/doctores', 'getDoctores');
$app->post('/doctoresByEspecialidad', 'doctoresByEspecialidad');
$app->post('/doctorById', 'doctorById');

$app->run();

?>