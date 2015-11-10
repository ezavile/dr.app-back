<?php 
require 'Slim/Slim.php'; 
require 'Helper/ErrorHandler.php';
require 'Config/db.php';
require 'Helper/Helper.php';
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

//Paciente
/*Crear un nuevo paciente*/
$app->post('/pacientes', 'pacientePostPaciente');
/*Actualizar un paciente*/
$app->put('/pacientes', 'pacientePutPaciente');
$app->delete('/pacientes', 'pacienteDeletePaciente');

/* Obtener los datos de un paciente */
$app->get('/pacienteById/:paciente', 'pacienteById');
/* Crear un nuevo comentario */
$app->post('/pacientes/comentarios', 'pacientePostComentario');

/* Obtener los mensajes */
$app->get('/pacientes/mensajes/:paciente', 'pacienteGetMensajes');

/* Crear una nueva cita */
$app->post('/pacientes/citas', 'pacientePostCita');
/* Obtener las citas */
$app->get('/pacientes/citas/:paciente', 'pacienteGetCitas');
/* Modificar citas */
$app->put('/pacientes/citas', 'pacientePutCitas');

//Doctor
/* Obtener todas las especialidades */
$app->get('/especialidades', 'doctorGetEspecialidades');
/* Crear un nuevo doctor */
$app->post('/doctores', 'doctorPostDoctor');
$app->put('/doctores', 'doctorPutDoctor');
$app->delete('/doctores', 'doctorDeleteDoctor');
/* Obtener todos los doctores */
$app->get('/doctores', 'doctorGetDoctores');
/* Obtener los doctores de una especialidad */
$app->get('/doctoresByEspecialidad/:especialidad', 'doctoresByEspecialidad');
$app->get('/doctoresByEnfermedad/:enfermedad', 'doctoresByEnfermedad');
/* Obtener los datos de un doctor */
$app->get('/doctorById/:doctor', 'doctorById');
$app->get('/doctores/citas/:doctor', 'doctorGetCitas');
$app->get('/doctores/comentarios/:doctor', 'doctorGetComentarios');
$app->get('/doctores/mensajes/:doctor', 'doctorGetMensajes');


$app->put('/citas', 'putEstatusCita');
$app->delete('/citas', 'deleteCitas');
/* Crear un nuevo mensaje */
$app->post('/mensajes', 'postMensaje');

$app->run();

?>