<?php
	function errorHandler($code, $info){
		$msj = "";
		$code = (string)$code;
		switch($code){
			case '1062':
				$msj = "Ya existe un " . $info[0] . " con ese " . $info[1]. ". Por favor intente de nuevo."; 
			break;
			case '1451':
				$msj = "No puedes darte de baja, ya que tienes CITAS AGENDADAS. Por favor elimine sus citas para poder dar de baja su cuenta.";
			break;
			default:
				$msj = "Ha ocurrido un error. Por favor intente de nuevo";
			break;
		}

		return $msj;
	}
?>