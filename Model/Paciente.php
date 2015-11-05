<?php
function pacientePostPaciente() {
	$request = \Slim\Slim::getInstance()->request();
	$doc = json_decode($request->getBody());
	$sql = "INSERT INTO paciente (paciente, password, imgPerfil, nombre, correo, telefono) VALUES (:paciente, :password, :imgPerfil, :nombre, :correo, :telefono)";
	try {
		$db = getConnection(); 
		$stmt = $db->prepare($sql);
		$stmt->bindParam("paciente", $doc->paciente);
		$stmt->bindParam("password", $doc->password);
		$stmt->bindParam("imgPerfil", $doc->imgPerfil);
		$stmt->bindParam("nombre", $doc->nombre);
		$stmt->bindParam("correo", $doc->correo);
		$stmt->bindParam("telefono", $doc->telefono);
		$stmt->execute();
		$db = null;
		echo json_encode($doc);
	} catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}

function pacientePostCita(){
	$request = \Slim\Slim::getInstance()->request();
	$req = json_decode($request->getBody());
	$req->estatus = "EN ESPERA";

	$sql = "INSERT INTO paciente_doctor_citas (fecha, hora, doctor, paciente, asunto, estatus) VALUES (:fecha, :hora, :doctor, :paciente, :asunto, :estatus)";

	try {
		$db = getConnection(); 
		$stmt = $db->prepare($sql);
		$stmt->bindParam("fecha", $req->fecha);
		$stmt->bindParam("hora", $req->hora);
		$stmt->bindParam("doctor", $req->doctor);
		$stmt->bindParam("paciente", $req->paciente);
		$stmt->bindParam("asunto", $req->asunto);
		$stmt->bindParam("estatus", $req->estatus);
		$stmt->execute();
		$db = null;
		echo json_encode($req);
	} catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}

function pacientePostComentario(){
	$request = \Slim\Slim::getInstance()->request();
	$req = json_decode($request->getBody());
	$sql = "INSERT INTO paciente_doctor_comentarios (doctor, paciente, fecha, comentario) VALUES (:doctor, :paciente, :fecha, :comentario)";

	try {
		$db = getConnection(); 
		$stmt = $db->prepare($sql);
		$stmt->bindParam("doctor", $req->doctor);
		$stmt->bindParam("paciente", $req->paciente);
		$stmt->bindParam("fecha", date_create()->format('Y-m-d H:i:s'));
		$stmt->bindParam("comentario", $req->comentario);
		$stmt->execute();
		$db = null;
		$req->fecha = date_create()->format('Y-m-d H:i:s');
		echo json_encode($req);
	} catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}
function pacientePostMensaje(){
	$request = \Slim\Slim::getInstance()->request();
	$req = json_decode($request->getBody());
	$sql = "INSERT INTO paciente_doctor_mensajes(doctor, paciente, fecha, mensaje) VALUES (:doctor, :paciente, :fecha, :mensaje)";

	try {
		$db = getConnection(); 
		$stmt = $db->prepare($sql);
		$stmt->bindParam("doctor", $req->doctor);
		$stmt->bindParam("paciente", $req->paciente);
		$stmt->bindParam("fecha", date_create()->format('Y-m-d H:i:s'));
		$stmt->bindParam("mensaje", $req->mensaje);
		$stmt->execute();
		$db = null;
		$req->fecha = date_create()->format('Y-m-d H:i:s');
		echo json_encode($req);
	} catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}

function pacientePutPaciente() {
	$request = \Slim\Slim::getInstance()->request();
	$req = json_decode($request->getBody());

	$sql = "UPDATE paciente SET password=:password, imgPerfil=:imgPerfil, nombre=:nombre, correo=:correo, telefono=:telefono WHERE paciente='$req->paciente'";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("password", $req->password);
		$stmt->bindParam("imgPerfil", $req->imgPerfil);
		$stmt->bindParam("nombre", $req->nombre);
		$stmt->bindParam("correo", $req->correo);
		$stmt->bindParam("telefono", $req->telefono);
		$stmt->execute();
		$db = null;
		echo json_encode($req);
	} catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}


/*
function pacienteGetCita($id){
	$citas = array();
	$sql_query = "SELECT 
						paciente_doctor_citas.fecha as DoctorCita_fecha,
						paciente_doctor_citas.hora as DoctorCita_hora,
						paciente_doctor_citas.doctor as DoctorCita_doctor,
						paciente_doctor_citas.asunto as DoctorCita_asunto,
						paciente_doctor_citas.estatus as DoctorCita_estatus,
						doctor.nombre as DocNombre,
						doctor.imgPerfil as DocImgPerfil
					FROM 
						doctor,
						paciente_doctor_citas
					WHERE 
						paciente_doctor_citas.doctor = doctor.doctor
						AND
						paciente_doctor_citas.paciente = '$id'
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
					'doctor' => array(
										'doctor' => $cita->DoctorCita_doctor,
										'nombre' => $cita->DocNombre,
										'imgPerfil' => $cita->DocImgPerfil
										), 
					'estatus' => $cita->DoctorCita_estatus,
					'asunto' => $cita->DoctorCita_asunto 
					);
			unset($cita->DoctorCita_fecha);
			unset($cita->DoctorCita_hora);
			unset($cita->DoctorCita_doctor);
			unset($cita->DoctorCita_asunto);
			unset($cita->DoctorCita_estatus);
			unset($cita->DocNombre);
			unset($cita->DocImgPerfil);
			array_push($citas, $cita);

		}

		$dbCon = null;
	} 
	catch(PDOException $e) {
		$citas = array( 'error' =>  $e->getMessage());
	}
	return $citas;
}


// obtiene info general, comentarios, info de los pacientes
function pacienteById(){
	$request = \Slim\Slim::getInstance()->request();
	$pac = json_decode($request->getBody());

	$sql_query = "SELECT 
						paciente.paciente as paciente, 
						paciente.password as password, 
						paciente.nombre as nombre, 
						paciente.imgPerfil as imgPerfil, 
						paciente.correo as correo, 
						paciente.telefono as telefono
					FROM 
						paciente
					WHERE 
						paciente.paciente = '$pac->paciente'";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$data  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
		$comentarios = array();
		$paciente = $data[0];

		$paciente->citas = getCitasPaciente($pac->paciente);
		echo json_encode($paciente);
	} 
	catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}*/
?>