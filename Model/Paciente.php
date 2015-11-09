<?php

function pacienteDeleteCitas(){
	$request = \Slim\Slim::getInstance()->request();
	$pac = json_decode($request->getBody());
	$sql_query = "DELETE 
					FROM 
						paciente_doctor_citas
					WHERE 
						paciente = '$pac->paciente'
						AND
						fecha = '$pac->fecha'
						AND
						hora = '$pac->hora'
						AND
						doctor = '$pac->doctor'";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql_query);
		$stmt->bindParam("paciente", $pac->paciente);
		$stmt->bindParam("fecha", $pac->fecha);
		$stmt->bindParam("hora", $pac->hora);
		$stmt->bindParam("doctor", $pac->doctor);
		$stmt->execute();
		$db = null;
		$answer = array('estatus'=>'success', 'msj' => '¡Se ha eliminado correctamente la cita!');
	} 
	catch(PDOException $e) {
		$answer = array('estatus'=>'error', 'msj' => $e->getMessage());
	}
	echo json_encode($answer);
}

function pacienteDeletePaciente(){
	$request = \Slim\Slim::getInstance()->request();
	$pac = json_decode($request->getBody());
	$sql_query = "DELETE 
					FROM 
						paciente
					WHERE 
						paciente = '$pac->paciente'";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql_query);
		$stmt->bindParam("paciente", $pac->paciente);
		$stmt->execute();
		$db = null;
		$answer = array('estatus'=>'success', 'msj' => '¡Tu usuario se ha dado de baja del sistema!');
	} 
	catch(PDOException $e) {
		$msj = errorHandler($e->errorInfo[1], array('paciente'));
		$answer = array('estatus'=>'error', 'msj' => $msj);
	}
	echo json_encode($answer);
}

function pacientePostPaciente() {
	$request = \Slim\Slim::getInstance()->request();
	$pac = json_decode($request->getBody());
	$sql = "INSERT INTO paciente (paciente, password, imgPerfil, nombre, correo, telefono) VALUES (:paciente, :password, :imgPerfil, :nombre, :correo, :telefono)";
	try {
		$db = getConnection(); 
		$stmt = $db->prepare($sql);
		$stmt->bindParam("paciente", $pac->paciente);
		$stmt->bindParam("password", $pac->password);
		$stmt->bindParam("imgPerfil", $pac->imgPerfil);
		$stmt->bindParam("nombre", $pac->nombre);
		$stmt->bindParam("correo", $pac->correo);
		$stmt->bindParam("telefono", $pac->telefono);
		$stmt->execute();
		$db = null;
		$answer = array('estatus'=>'success', 'msj' => 'Te has registrado con éxito.', 'paciente' =>  $pac);
	} catch(PDOException $e) {
		$msj = errorHandler($e->errorInfo[1], array('paciente','usuario'));
		$answer = array('estatus'=>'error','msj' =>  $msj);
	}
	echo json_encode($answer);
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
		$answer = array('estatus'=>'success', 'msj' => '¡Tu cita ha sido agendada!', 'cita' =>  $req);
	} catch(PDOException $e) {
		$answer = array('estatus'=>'error','msj' =>  $e->getMessage());
	}
	echo json_encode($answer);
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
		$answer = array('estatus'=>'success', 'msj' => '¡Se ha enviado su comentario!', 'comentario' =>  $req);
	} catch(PDOException $e) {
		$answer = array('estatus'=>'error', 'msj' =>  $e->getMessage());
	}
	echo json_encode($answer);
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
		$answer = array('estatus'=>'success', 'msj' => '¡Se han realizados los cambios correctamente!', 'paciente' =>  $req);
	} catch(PDOException $e) {
		$answer = array('estatus'=>'error', 'msj' =>  $e->getMessage());
	}

	echo json_encode($answer);
}



// obtiene info general, mensajes, citas
function pacienteById($pac){
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
						paciente.paciente = '$pac'";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$data  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
		$comentarios = array();
		$paciente = $data[0];
		//$paciente->citas = pacienteGetCitas($pac);
		//$paciente->mensajes = pacienteGetMensajes($pac);
		echo json_encode($paciente);
	} 
	catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}


function pacienteGetMensajes($id){
	$mensajes = array();
	$sql_query = "SELECT 
						paciente_doctor_mensajes.idMensaje as DoctorMensaje_idMensaje,
						paciente_doctor_mensajes.doctor as DoctorMensaje_doctor,
						paciente_doctor_mensajes.mensaje as DoctorMensaje_mensaje,
						paciente_doctor_mensajes.fecha as DoctorMensaje_fecha,
						paciente_doctor_mensajes.autor as DoctorMensaje_autor,
						doctor.nombre as DocNombre,
						doctor.imgPerfil as DocImgPerfil
					FROM 
						doctor,
						paciente_doctor_mensajes
					WHERE 
						paciente_doctor_mensajes.doctor = doctor.doctor
						AND
						paciente_doctor_mensajes.paciente = '$id'
					ORDER BY
						paciente_doctor_mensajes.doctor asc, paciente_doctor_mensajes.fecha desc";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$res  = $stmt->fetchAll(PDO::FETCH_OBJ);

		foreach ($res as $msj) {
			$msj  = array(
					'idMensaje' => $msj->DoctorMensaje_idMensaje,
					'doctor' => array(
										'doctor' => $msj->DoctorMensaje_doctor,
										'nombre' => $msj->DocNombre,
										'imgPerfil' => $msj->DocImgPerfil
										), 
					'mensaje' => $msj->DoctorMensaje_mensaje ,
					'autor' => $msj->DoctorMensaje_autor ,
					'fecha' => $msj->DoctorMensaje_fecha 
					);
			unset($msj->DoctorMensaje_idMensaje);
			unset($msj->DoctorMensaje_doctor);
			unset($msj->DoctorMensaje_mensaje);
			unset($msj->DoctorMensaje_autor);
			unset($msj->DoctorMensaje_fecha);
			unset($msj->DocNombre);
			unset($msj->DocImgPerfil);
			array_push($mensajes, $msj);
		}

		$dbCon = null;
	} 
	catch(PDOException $e) {
		$mensajes = array( 'error' =>  $e->getMessage());
	}

	echo json_encode($mensajes);
}
function pacienteGetCitas($id){
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
						paciente_doctor_citas.fecha desc, paciente_doctor_citas.hora asc";
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

	echo json_encode($citas);
}

function pacientePutCitas() {
	$request = \Slim\Slim::getInstance()->request();
	$req = json_decode($request->getBody());

	$sql = "UPDATE paciente_doctor_citas SET fecha=:fecha, hora=:hora, asunto=:asunto WHERE paciente='$req->paciente' AND doctor='$req->doctor' AND fecha='$req->fecha' AND hora='$req->hora'";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("fecha", $req->newFecha);
		$stmt->bindParam("hora", $req->newHora);
		$stmt->bindParam("asunto", $req->asunto);
		$stmt->execute();
		$db = null;
		echo json_encode($req);
	} catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}
}

?>