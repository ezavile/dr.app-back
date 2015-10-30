<?php

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

?>