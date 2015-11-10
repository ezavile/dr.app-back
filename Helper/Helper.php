<?php


function postMensaje(){
	$request = \Slim\Slim::getInstance()->request();
	$req = json_decode($request->getBody());
	$sql = "INSERT INTO paciente_doctor_mensajes(doctor, paciente, fecha, mensaje, autor) VALUES (:doctor, :paciente, :fecha, :mensaje, :autor)";

	try {
		$db = getConnection(); 
		$stmt = $db->prepare($sql);
		$stmt->bindParam("doctor", $req->doctor);
		$stmt->bindParam("paciente", $req->paciente);
		$stmt->bindParam("fecha", date_create()->format('Y-m-d H:i:s'));
		$stmt->bindParam("mensaje", $req->mensaje);
		$stmt->bindParam("autor", $req->autor);
		$stmt->execute();
		$db = null;
		$req->fecha = date_create()->format('Y-m-d H:i:s');
		$answer = array('estatus'=>'success','msj'=>"¡Se ha enviado su mensaje!");
	} catch(PDOException $e) {
		$answer = array('estatus'=>'error','msj'=>$e->getMessage());
	}
	echo json_encode($answer);
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
		$answer = array('estatus'=>'success', 'msj' => '¡Se han realizados los cambios correctamente!', 'cita' =>  $req);
	} catch(PDOException $e) {
		$answer = array('estatus'=>'error', 'msj' =>  $e->getMessage());
	}

	echo json_encode($answer);
}


function deleteCitas(){
	$request = \Slim\Slim::getInstance()->request();
	$req = json_decode($request->getBody());
	$sql_query = "DELETE 
					FROM 
						paciente_doctor_citas
					WHERE 
						doctor = '$req->doctor'
						AND
						fecha = '$req->fecha'
						AND
						hora = '$req->hora'
						AND
						paciente = '$req->paciente'";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql_query);
		$stmt->bindParam("paciente", $req->paciente);
		$stmt->bindParam("fecha", $req->fecha);
		$stmt->bindParam("hora", $req->hora);
		$stmt->bindParam("doctor", $req->doctor);
		$stmt->execute();
		$db = null;
		$answer = array('estatus'=>'success', 'msj' => '¡Se ha eliminado correctamente la cita!');
	} 
	catch(PDOException $e) {
		$answer = array('estatus'=>'error', 'msj' => $e->getMessage());
	}
	echo json_encode($answer);
}
?>