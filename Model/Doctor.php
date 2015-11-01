<?php

function getDoctores() { 
	$sql_query = "SELECT * FROM doctor";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$data  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
		echo json_encode($data);
	} 
	catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}

function getEspecialidades() { 
	$sql_query = "SELECT * FROM especialidades";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$data  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
		echo json_encode($data);
	} 
	catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}


function addDoctor() {
	$request = \Slim\Slim::getInstance()->request();
	$doc = json_decode($request->getBody());
	$sql = "INSERT INTO doctor (idEspecialidad, doctor, imgPerfil, password, nombre, servicios, telefono, direccion, coordenadas, correo, foto1, foto2, foto3) VALUES (:idEspecialidad, :doctor, :imgPerfil, :password, :nombre, :servicios, :telefono, :direccion, :coordenadas, :correo, :foto1, :foto2, :foto3)";
	try {
		$db = getConnection(); 
		$stmt = $db->prepare($sql);
		$stmt->bindParam("idEspecialidad", $doc->idEspecialidad);
		$stmt->bindParam("doctor", $doc->doctor);
		$stmt->bindParam("imgPerfil", $doc->imgPerfil);
		$stmt->bindParam("password", $doc->password);
		$stmt->bindParam("nombre", $doc->nombre);
		$stmt->bindParam("servicios", $doc->servicios);
		$stmt->bindParam("telefono", $doc->telefono);
		$stmt->bindParam("direccion", $doc->direccion);
		$stmt->bindParam("coordenadas", $doc->coordenadas);
		$stmt->bindParam("correo", $doc->correo);
		$stmt->bindParam("foto1", $doc->foto1);
		$stmt->bindParam("foto2", $doc->foto2);
		$stmt->bindParam("foto3", $doc->foto3);
		$stmt->execute();
		$db = null;
		echo json_encode($doc);
	} catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}
// obtiene info general, comentarios, info de los pacientes
function doctorById(){
	$request = \Slim\Slim::getInstance()->request();
	$doc = json_decode($request->getBody());

	$sql_query = 	"SELECT 
						doctor.doctor as doctor, 
						doctor.nombre as nombre, 
						doctor.imgPerfil as imgPerfil, 
						doctor.servicios as servicios, 
						doctor.telefono as telefono, 
						doctor.direccion as direccion, 
						doctor.correo as correo, 
						doctor.foto1 as foto1, 
						doctor.foto2 as foto2, 
						doctor.foto3 as foto3, 
						doctor.coordenadas as coordenadas, 
						doctor_comentarios.idComentario as DoctorComentarios_idComentario, 
						doctor_comentarios.paciente as DoctorComentarios_paciente, 
						doctor_comentarios.fecha as DoctorComentarios_fecha, 
						doctor_comentarios.comentario as DoctorComentarios_comentario,
						paciente.nombre as PacienteNombre
					FROM 
						doctor, 
						doctor_comentarios,
						paciente
					WHERE 
						doctor.doctor = doctor_comentarios.doctor
						AND
						doctor_comentarios.paciente = paciente.paciente
						AND 
						doctor.doctor = '$doc->doctor'";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$data  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
		$comentarios = array();
		foreach ($data as $doc) {
			array_push($comentarios, array(
									'idComentario' => $doc->DoctorComentarios_idComentario, 
									'paciente' => array(
														'paciente' => $doc->DoctorComentarios_paciente,
														'nombre' => $doc->PacienteNombre
														), 
									'fecha' => $doc->DoctorComentarios_fecha, 
									'comentario' => $doc->DoctorComentarios_comentario
								)); 
		}
		$doctor = $data[0];
		$doctor->comentarios = $comentarios;
		unset($doctor->DoctorComentarios_idComentario);
		unset($doctor->DoctorComentarios_paciente);
		unset($doctor->PacienteNombre);
		unset($doctor->DoctorComentarios_fecha);
		unset($doctor->DoctorComentarios_comentario);
		echo json_encode($doctor);
	} 
	catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}

function doctoresByEspecialidad() {
	$request = \Slim\Slim::getInstance()->request();
	$especialidad = json_decode($request->getBody());

	$sql_query = "SELECT * FROM doctor, especialidades WHERE doctor.idEspecialidad = especialidades.idEspecialidad AND doctor.idEspecialidad = '$especialidad->idEspecialidad'";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$data  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
		foreach ($data as $doc) {
			$especialidad = array('idEspecialidad' => $doc->especialidad, 'especialidad' => $doc->especialidad, 'enfermedadesAsociadas' => $doc->enfermedadesAsociadas);
			$doc->especialidad = $especialidad;
			unset($doc->idEspecialidad);
			unset($doc->enfermedadesAsociadas);
		}
		echo json_encode($data);
	} 
	catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}
?>