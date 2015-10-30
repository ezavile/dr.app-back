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
	$sql = "INSERT INTO doctor (doctor, imgPerfil, password, nombre, servicios, telefono, direccion, correo, foto1, foto2, foto3) VALUES (:doctor, :imgPerfil, :password, :nombre, :servicios, :telefono, :direccion, :correo, :foto1, :foto2, :foto3)";
	try {
		$db = getConnection(); 
		$stmt = $db->prepare($sql);
		$stmt->bindParam("doctor", $doc->usuario);
		$stmt->bindParam("imgPerfil", $doc->imgPerfil);
		$stmt->bindParam("password", $doc->password);
		$stmt->bindParam("nombre", $doc->nombre);
		$stmt->bindParam("servicios", $doc->servicios);
		$stmt->bindParam("telefono", $doc->telefono);
		$stmt->bindParam("direccion", $doc->direccion);
		$stmt->bindParam("correo", $doc->correo);
		$stmt->bindParam("foto1", $doc->foto1);
		$stmt->bindParam("foto2", $doc->foto2);
		$stmt->bindParam("foto3", $doc->foto3);
		$stmt->execute();
		$db = null;
		echo json_encode($doc);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

?>