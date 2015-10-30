<?php
require 'Config/db.php';

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
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}


function addDoctor() {
	$request = \Slim\Slim::getInstance()->request();
	$doc = json_decode($request->getBody());
	$sql = "INSERT INTO doctor (usuario, password, nombre, servicios, telefono, horario, direccion, correo, foto1, foto2, foto3) VALUES (:usuario, :password, :nombre, :servicios, :telefono, :horario, :direccion, :correo, :foto1, :foto2, :foto3)";
	try {
		$db = getConnection(); 
		$stmt = $db->prepare($sql);
		$stmt->bindParam("usuario", $doc[0]->usuario);
		$stmt->bindParam("password", $doc[0]->password);
		$stmt->bindParam("nombre", $doc[0]->nombre);
		$stmt->bindParam("servicios", $doc[0]->servicios);
		$stmt->bindParam("telefono", $doc[0]->telefono);
		$stmt->bindParam("horario", $doc[0]->horario);
		$stmt->bindParam("direccion", $doc[0]->direccion);
		$stmt->bindParam("correo", $doc[0]->correo);
		$stmt->bindParam("foto1", $doc[0]->foto1);
		$stmt->bindParam("foto2", $doc[0]->foto2);
		$stmt->bindParam("foto3", $doc[0]->foto3);
		$stmt->execute();
		$db = null;
		echo json_encode($doc[0]);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

?>