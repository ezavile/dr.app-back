<?php

function getPacientes() { 
	$sql_query = "SELECT * FROM paciente";
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

function addPaciente() {
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

function addCita(){
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

function addComentario(){
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
function addMensaje(){
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

function updatePaciente() {
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

?>