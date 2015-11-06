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



function putEstatusCita() {
	$request = \Slim\Slim::getInstance()->request();
	$req = json_decode($request->getBody());

	$sql = "UPDATE paciente_doctor_citas SET estatus=:estatus WHERE paciente='$req->paciente' AND doctor='$req->doctor' AND fecha='$req->fecha' AND hora='$req->hora'";

	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("estatus", $req->estatus);
		$stmt->execute();
		$db = null;
		echo json_encode($req);
	} catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
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

/* Obtener los datos de un paciente */
$app->get('/pacienteById/:paciente', 'pacienteById');
/* Crear un nuevo comentario */
$app->post('/pacientes/comentarios', 'pacientePostComentario');

/* Crear un nuevo mensaje */
$app->post('/pacientes/mensajes', 'pacientePostMensaje');
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
/* Obtener todos los doctores */
$app->get('/doctores', 'doctorGetDoctores');
/* Obtener los doctores de una especialidad */
$app->get('/doctoresByEspecialidad/:especialidad', 'doctoresByEspecialidad');
/* Obtener los datos de un doctor */
$app->get('/doctorById/:doctor', 'doctorById');
$app->get('/doctores/citas/:doctor', 'doctorGetCitas');
$app->get('/doctores/comentarios/:doctor', 'doctorGetComentarios');


$app->put('/estatusCita', 'putEstatusCita');

$app->run();

?>