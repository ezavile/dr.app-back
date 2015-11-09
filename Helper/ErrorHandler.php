<?php
	function errorHandler($code, $info){
		$msj = "";
		switch($code){
			case '23000':
				$msj = "Ya existe un " . $info[0] . " con ese " . $info[1]. ". Por favor intente de nuevo."; 
			break;
			default:
				$msj = "Ha ocurrido un error. Por favor intente de nuevo";
			break;
		}

		return $msj;
	}
?>