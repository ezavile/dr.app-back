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
		$answer = array('estatus'=>'error','msj' =>  $e->getMessage());
	}

	$sql_query = "SELECT * FROM doctor WHERE doctor = '$usuario->usuario' AND password = '$usuario->password'";
	try {
		$dbCon = getConnection();
		$stmt   = $dbCon->query($sql_query);
		$doctor  = $stmt->fetchAll(PDO::FETCH_OBJ);
		$dbCon = null;
	} 
	catch(PDOException $e) {
		$answer = array('estatus'=>'error','msj' =>  $e->getMessage());
	}

	if(count($paciente) > 0){
		$paciente = $paciente[0];
		$answer = array('estatus'=>'success','msj'=>"¡Bienvenido $paciente->nombre!",'tipoUsuario'=>'paciente','paciente'=>$paciente);
	} else {
		if(count($doctor) > 0){
			$doctor = $doctor[0];
			$answer = array('estatus'=>'success','msj'=>"¡Bienvenido $doctor->nombre!",'tipoUsuario'=>'doctor','doctor'=>$doctor);
		} else {
			$answer = array( 'tipoUsuario' =>  null);
			$answer = array('estatus'=>'error','msj'=>'Usuario y/o contraseñas incorrectas. Por Favor intente de nuevo.');
		}
	}

	echo json_encode($answer);
}

?>