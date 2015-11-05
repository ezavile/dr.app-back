<?php

function login() {
	$request = \Slim\Slim::getInstance()->request();
	$usuario = json_decode($request->getBody());

	$sql_query = "SELECT * FROM paciente WHERE paciente = '$usuario->usuario' AND password = '$usuario->password'";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$paciente  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
	} 
	catch(PDOException $e) {
		$answer = array( 'error' =>  $e->getMessage());
		echo json_encode($answer);
	}

	$sql_query = "SELECT * FROM doctor WHERE doctor = '$usuario->usuario' AND password = '$usuario->password'";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$doctor  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
	} 
	catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}

	if(count($paciente) > 0){
		$paciente = $paciente[0];
		$paciente->tipoUsuario = 'paciente';
		$paciente->citas = pacienteGetCitas($usuario->usuario);
		$paciente->mensajes = pacienteGetMensajes($usuario->usuario);
		echo json_encode($paciente);
	} else {
		if(count($doctor) > 0){
			$doctor[0]->tipoUsuario = 'doctor';
			echo json_encode($doctor[0]);
		} else {
			$answer = array( 'tipoUsuario' =>  null);
			echo json_encode($answer);
		}
	}
}

?>