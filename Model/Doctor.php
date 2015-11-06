<?php

function doctorGetDoctores() { 
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

function doctorGetEspecialidades() { 
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


function doctorPostDoctor() {
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

function doctoresByEspecialidad($esp) {
	$sql_query = "SELECT * FROM doctor, especialidades WHERE doctor.idEspecialidad = especialidades.idEspecialidad AND doctor.idEspecialidad = '$esp'";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$data  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
		foreach ($data as $doc) {

			$especialidad = array('idEspecialidad' => $doc->idEspecialidad, 'especialidad' => $doc->especialidad, 'enfermedadesAsociadas' => $doc->enfermedadesAsociadas);
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
// obtiene info general, comentarios, info de los pacientes
function doctorById($doc){
	$sql_query = "SELECT 
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
						doctor.coordenadas as coordenadas
					FROM 
						doctor
					WHERE 
						doctor.doctor = '$doc'";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$data  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
		$comentarios = array();
		$doctor = $data[0];
		//obtener comentarios del doctor
		//$doctor->comentarios = doctorGetComentarios($doc);
		//$doctor->citas = doctorGetCitas($doc);
		echo json_encode($doctor);
	} 
	catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}

function doctorGetComentarios($id){
	$comentarios = array();
	$sql_query = "SELECT 
						paciente_doctor_comentarios.idComentario as DoctorComentarios_idComentario,
						paciente_doctor_comentarios.doctor as DoctorComentarios_doctor, 
						paciente_doctor_comentarios.paciente as DoctorComentarios_paciente, 
						paciente_doctor_comentarios.fecha as DoctorComentarios_fecha, 
						paciente_doctor_comentarios.comentario as DoctorComentarios_comentario,
						paciente.nombre as PacienteNombre,
						paciente.imgPerfil as PacienteImgPerfil
					FROM 
						paciente,
						paciente_doctor_comentarios
					WHERE 
						paciente_doctor_comentarios.paciente = paciente.paciente
						AND
						paciente_doctor_comentarios.doctor = '$id'
					ORDER BY
						paciente_doctor_comentarios.fecha desc";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$res  = $stmt->fetchAll(PDO::FETCH_OBJ);
		foreach ($res as $comentario) {
			$comentario  = array(
					'idComentario' => $comentario->DoctorComentarios_idComentario, 
					'paciente' => array(
										'paciente' => $comentario->DoctorComentarios_paciente,
										'nombre' => $comentario->PacienteNombre,
										'imgPerfil' => $comentario->PacienteImgPerfil
										), 
					'fecha' => $comentario->DoctorComentarios_fecha, 
					'comentario' => $comentario->DoctorComentarios_comentario
					);
			unset($comentario->DoctorComentarios_doctor);
			unset($comentario->DoctorComentarios_idComentario);
			unset($comentario->DoctorComentarios_paciente);
			unset($comentario->PacienteNombre);
			unset($comentario->PacienteImgPerfil);
			unset($comentario->DoctorComentarios_fecha);
			unset($comentario->DoctorComentarios_comentario);
			array_push($comentarios, $comentario);

		}


		$dbCon = null;
	} 
	catch(PDOException $e) {
		$comentarios = array( 'error' =>  $e->getMessage());
	}
	echo json_encode($comentarios);
}

function doctorGetCitas($id){
	$citas = array();
	$sql_query = "SELECT 
						paciente_doctor_citas.fecha as DoctorCita_fecha,
						paciente_doctor_citas.hora as DoctorCita_hora,
						paciente_doctor_citas.paciente as DoctorCita_paciente,
						paciente_doctor_citas.asunto as DoctorCita_asunto,
						paciente_doctor_citas.estatus as DoctorCita_estatus,
						paciente.nombre as PacienteNombre,
						paciente.imgPerfil as PacienteImgPerfil
					FROM 
						paciente,
						paciente_doctor_citas
					WHERE 
						paciente_doctor_citas.paciente = paciente.paciente
						AND
						paciente_doctor_citas.doctor = '$id'
					ORDER BY
						paciente_doctor_citas.fecha, paciente_doctor_citas.hora desc";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$res  = $stmt->fetchAll(PDO::FETCH_OBJ);
		foreach ($res as $cita) {
			$cita  = array(
					'fecha' => $cita->DoctorCita_fecha, 
					'hora' => $cita->DoctorCita_hora, 
					'paciente' => array(
										'paciente' => $cita->DoctorCita_paciente,
										'nombre' => $cita->PacienteNombre,
										'imgPerfil' => $cita->PacienteImgPerfil
										), 
					'estatus' => $cita->DoctorCita_estatus,
					'asunto' => $cita->DoctorCita_asunto 
					);
			unset($cita->DoctorCita_fecha);
			unset($cita->DoctorCita_hora);
			unset($cita->DoctorCita_paciente);
			unset($cita->DoctorCita_asunto);
			unset($cita->DoctorCita_estatus);
			unset($cita->PacienteNombre);
			unset($cita->PacienteImgPerfil);
			array_push($citas, $cita);

		}

		$dbCon = null;
	} 
	catch(PDOException $e) {
		$citas = array( 'error' =>  $e->getMessage());
	}
	echo json_encode($citas);
}

?>